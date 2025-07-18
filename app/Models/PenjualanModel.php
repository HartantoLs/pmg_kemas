<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['no_surat_jalan', 'pelat_mobil', 'customer', 'tanggal'];

    protected $db;
    protected $stokModel;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->stokModel = new StokModel();
    }

    /**
     * Menyimpan data penjualan baru beserta detailnya dalam satu transaksi.
     */
    public function simpanPenjualan(array $data)
    {
        $this->db->transBegin();
        try {
            if (empty($data['items'])) {
                throw new \Exception("Harap tambahkan minimal satu item produk.");
            }

            $this->insert([
                'no_surat_jalan' => $data['no_surat_jalan'],
                'pelat_mobil'    => $data['pelat_mobil'],
                'customer'       => $data['customer'],
                'tanggal'        => $data['tanggal'],
            ]);
            $penjualan_id = $this->db->insertID();
            if (!$penjualan_id) throw new \Exception("Gagal membuat record penjualan utama.");

            $hasValidItem = false;
            foreach ($data['items'] as $item) {
                $id_produk = (int)($item['produk'] ?? 0);
                $id_gudang = (int)($item['gudang'] ?? 0);
                $jumlah_dus = (int)($item['jumlah_dus'] ?? 0);
                $jumlah_satuan = (int)($item['jumlah_satuan'] ?? 0);
                
                if ($id_produk > 0 && $id_gudang > 0 && ($jumlah_dus > 0 || $jumlah_satuan > 0)) {
                    $hasValidItem = true;
                    $stok_historis = $this->stokModel->getHistoricalStock($id_produk, $id_gudang, $data['tanggal']);
                    if ($jumlah_dus > $stok_historis['dus'] || $jumlah_satuan > $stok_historis['satuan']) {
                        $nama_produk = $this->db->table('produk')->select('nama_produk')->where('id_produk', $id_produk)->get()->getRow()->nama_produk;
                        throw new \Exception("Stok {$nama_produk} tidak mencukupi pada tanggal yang dipilih.");
                    }

                    $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus - ?, jumlah_satuan = jumlah_satuan - ? WHERE id_produk = ? AND id_gudang = ?", [$jumlah_dus, $jumlah_satuan, $id_produk, $id_gudang]);
                    $this->db->table('penjualan_detail')->insert(['penjualan_id' => $penjualan_id, 'produk_id' => $id_produk, 'gudang_id' => $id_gudang, 'jumlah_dus' => $jumlah_dus, 'jumlah_satuan' => $jumlah_satuan]);
                }
            }

            if (!$hasValidItem) throw new \Exception("Tidak ada item valid untuk disimpan.");
            if ($this->db->transStatus() === false) throw new \Exception('Gagal menyimpan detail penjualan.');
            
            $this->db->transCommit();
            return ['success' => true, 'message' => 'Penjualan berhasil disimpan!'];

        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Transaksi Gagal: ' . $e->getMessage()];
        }
    }
    
    public function getRiwayat(array $filters)
    {
        $builder = $this->db->table('penjualan_detail pd')
            ->select('pd.id, p.tanggal, p.no_surat_jalan, p.customer, pr.nama_produk, g.nama_gudang, pd.jumlah_dus, pd.jumlah_satuan')
            ->join('penjualan p', 'pd.penjualan_id = p.id')
            ->join('produk pr', 'pd.produk_id = pr.id_produk')
            ->join('gudang g', 'pd.gudang_id = g.id_gudang')
            ->where('DATE(p.tanggal) >=', $filters['tanggal_mulai'])
            ->where('DATE(p.tanggal) <=', $filters['tanggal_akhir']);

        if ($filters['gudang_id'] !== 'semua') $builder->where('pd.gudang_id', $filters['gudang_id']);
        if ($filters['produk_id'] !== 'semua') $builder->where('pd.produk_id', $filters['produk_id']);

        return $builder->orderBy('p.tanggal', 'DESC')->orderBy('p.id', 'DESC')->get()->getResultArray();
    }
    
    public function getDetail(int $detail_id)
    {
        $data = $this->db->table('penjualan_detail pd')
            ->select('pd.id, p.tanggal, pd.produk_id, pd.gudang_id, pd.jumlah_dus, pd.jumlah_satuan, pr.nama_produk, g.nama_gudang, pr.satuan_per_dus')
            ->join('penjualan p', 'pd.penjualan_id = p.id')
            ->join('produk pr', 'pd.produk_id = pr.id_produk')
            ->join('gudang g', 'pd.gudang_id = g.id_gudang')
            ->where('pd.id', $detail_id)->get()->getRowArray();

        if ($data) {
            $stok_historis = $this->stokModel->getHistoricalStock($data['produk_id'], $data['gudang_id'], $data['tanggal']);
            $data['stok_tersedia_saat_itu_dus'] = $stok_historis['dus'] + (int)$data['jumlah_dus'];
            $data['stok_tersedia_saat_itu_satuan'] = $stok_historis['satuan'] + (int)$data['jumlah_satuan'];
        }
        return $data;
    }

    public function updatePenjualan(array $data)
    {
        $this->db->transBegin();
        try {
            $detail_id = (int)$data['detail_id'];
            $new_dus = (int)$data['jumlah_dus'];
            $new_satuan = (int)$data['jumlah_satuan'];
            
            $old_data = $this->db->table('penjualan_detail')->where('id', $detail_id)->get()->getRowArray();
            if (!$old_data) throw new \Exception("Data penjualan tidak ditemukan.");
            
            // Validasi stok historis
            $stok_tersedia_historis = $this->getDetail($detail_id);
            if($new_dus > $stok_tersedia_historis['stok_tersedia_saat_itu_dus'] || $new_satuan > $stok_tersedia_historis['stok_tersedia_saat_itu_satuan']){
                throw new \Exception("Stok tidak mencukupi pada tanggal tersebut.");
            }

            // Kembalikan stok lama, kurangi stok baru.
            $selisih_dus = $old_data['jumlah_dus'] - $new_dus;
            $selisih_satuan = $old_data['jumlah_satuan'] - $new_satuan;

            $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? WHERE id_produk = ? AND id_gudang = ?", [$selisih_dus, $selisih_satuan, (int)$old_data['produk_id'], (int)$old_data['gudang_id']]);
            $this->db->table('penjualan_detail')->where('id', $detail_id)->update(['jumlah_dus' => $new_dus, 'jumlah_satuan' => $new_satuan]);

            if ($this->db->transStatus() === false) throw new \Exception('Gagal memperbarui data.');

            $this->db->transCommit();
            return ['success' => true, 'message' => 'Riwayat penjualan berhasil diperbarui!'];

        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }
    
    public function hapusPenjualan(int $id)
    {
        $this->db->transBegin();
        try {
            $data_to_delete = $this->db->table('penjualan_detail')->where('id', $id)->get()->getRowArray();
            if (!$data_to_delete) throw new \Exception("Data penjualan tidak ditemukan.");
            
            // Kembalikan stok ke gudang
            $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? WHERE id_produk = ? AND id_gudang = ?", [(int)$data_to_delete['jumlah_dus'], (int)$data_to_delete['jumlah_satuan'], (int)$data_to_delete['produk_id'], (int)$data_to_delete['gudang_id']]);

            // Hapus data penjualan
            $this->db->table('penjualan_detail')->where('id', $id)->delete();

            if ($this->db->transStatus() === false) throw new \Exception('Gagal menghapus data.');
            
            $this->db->transCommit();
            return ['success' => true, 'message' => 'Riwayat penjualan berhasil dihapus!'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }
    /**
     * Mengambil data penjualan berdasarkan nomor surat jalan.
     */
    public function getPenjualanByNoSuratJalan(string $no_surat_jalan)
    {
        return $this->db->table('penjualan p')
            ->select('p.id, p.no_surat_jalan, p.pelat_mobil, p.customer, p.tanggal')
            ->where('p.no_surat_jalan', $no_surat_jalan)
            ->get()->getRowArray();
    }

    /**
     * Mengambil detail produk dari penjualan berdasarkan no surat jalan dan produk.
     */
    public function getProdukFromPenjualan(string $no_surat_jalan, int $produk_id)
    {
        return $this->db->table('penjualan p')
            ->select('p.no_surat_jalan, p.customer, p.tanggal, pd.jumlah_dus, pd.jumlah_satuan, pr.nama_produk, pr.satuan_per_dus, g.nama_gudang')
            ->join('penjualan_detail pd', 'p.id = pd.penjualan_id')
            ->join('produk pr', 'pd.produk_id = pr.id_produk')
            ->join('gudang g', 'pd.gudang_id = g.id_gudang')
            ->where('p.no_surat_jalan', $no_surat_jalan)
            ->where('pd.produk_id', $produk_id)
            ->get()->getRowArray();
    }

    /**
     * Mengambil semua produk dari penjualan berdasarkan no surat jalan.
     */
    public function getAllProdukFromPenjualan(string $no_surat_jalan)
    {
        return $this->db->table('penjualan p')
            ->select('p.no_surat_jalan, p.customer, p.tanggal, pd.produk_id, pd.jumlah_dus, pd.jumlah_satuan, pr.nama_produk, pr.satuan_per_dus, g.nama_gudang')
            ->join('penjualan_detail pd', 'p.id = pd.penjualan_id')
            ->join('produk pr', 'pd.produk_id = pr.id_produk')
            ->join('gudang g', 'pd.gudang_id = g.id_gudang')
            ->where('p.no_surat_jalan', $no_surat_jalan)
            ->get()->getResultArray();
    }
}