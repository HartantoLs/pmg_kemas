<?php

namespace App\Models;

use CodeIgniter\Model;

class OperpackKerusakanModel extends Model
{
    protected $table = 'operpack_kerusakan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['no_surat_jalan', 'waktu_diterima', 'kategori_asal', 'asal', 'status'];
    
    protected $db;
    protected $stokModel;
    protected $penjualanModel;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->stokModel = new StokModel();
        $this->penjualanModel = new PenjualanModel();
    }

    /**
     * Menyimpan data kerusakan baru dalam satu transaksi.
     */
    public function simpanKerusakan(array $data)
    {
        $this->db->transBegin();
        
        try {
            // Validasi input dasar
            if (empty($data['no_surat_jalan'])) {
                throw new \Exception("No Surat Jalan harus diisi.");
            }
            
            if (empty($data['kategori_asal'])) {
                throw new \Exception("Kategori asal harus dipilih.");
            }
            
            if (empty($data['asal'])) {
                throw new \Exception("Asal pengembalian harus diisi.");
            }
            
            if (empty($data['items']) || !is_array($data['items'])) {
                throw new \Exception("Harap tambahkan minimal satu item produk.");
            }

            // Cek duplikasi no_surat_jalan
            $existing = $this->where('no_surat_jalan', $data['no_surat_jalan'])->first();
            if ($existing) {
                throw new \Exception("No Surat Jalan sudah digunakan sebelumnya.");
            }

            $tanggal = $data['tanggal'] ?? date('Y-m-d');
            $waktu_diterima = $tanggal . ' ' . date('H:i:s');
            
            // Validasi khusus berdasarkan kategori
            $id_gudang_asal = null;
            $nama_gudang_asal = $data['asal'];
            
            if ($data['kategori_asal'] === 'Eksternal') {
                // Validasi nomor surat jalan penjualan
                $penjualan_data = $this->penjualanModel->getPenjualanByNoSuratJalan($data['asal']);
                if (!$penjualan_data) {
                    throw new \Exception("Nomor surat jalan penjualan '{$data['asal']}' tidak ditemukan.");
                }
                $nama_gudang_asal = $data['asal']; // Untuk eksternal, asal adalah no surat jalan
            } else {
                // Konversi ID gudang ke nama gudang untuk kategori Internal
                $id_gudang_asal = (int)$data['asal'];
                
                // Ambil nama gudang berdasarkan ID
                $gudang = $this->db->table('gudang')
                                  ->select('nama_gudang')
                                  ->where('id_gudang', $id_gudang_asal)
                                  ->where('tipe_gudang', 'Produksi')
                                  ->get()->getRow();
                
                if (!$gudang) {
                    throw new \Exception("Gudang internal tidak valid.");
                }
                
                $nama_gudang_asal = $gudang->nama_gudang;
            }

            // 1. Simpan data header
            $header_data = [
                'no_surat_jalan' => $data['no_surat_jalan'],
                'waktu_diterima' => $waktu_diterima,
                'kategori_asal' => $data['kategori_asal'],
                'asal' => $nama_gudang_asal,
                'status' => 'Diterima'
            ];
            
            $this->insert($header_data);
            $operpack_id = $this->db->insertID();
            
            if ($operpack_id === 0) {
                throw new \Exception("Gagal membuat record kerusakan utama.");
            }

            // 2. Loop melalui setiap item produk
            $hasValidItem = false;
            
            foreach ($data['items'] as $item) {
                $id_produk = (int)($item['produk'] ?? 0);
                $jumlah_dus = (int)($item['jumlah_dus'] ?? 0);
                $jumlah_satuan = (int)($item['jumlah_satuan'] ?? 0);
                
                // Validasi item
                if ($id_produk <= 0) {
                    continue;
                }
                
                if ($jumlah_dus < 0 || $jumlah_satuan < 0) {
                    throw new \Exception("Jumlah dus dan satuan tidak boleh negatif.");
                }
                
                if ($jumlah_dus == 0 && $jumlah_satuan == 0) {
                    continue;
                }
                
                $hasValidItem = true;
                
                // Validasi produk exists
                $produk = $this->db->table('produk')
                                  ->select('nama_produk, satuan_per_dus')
                                  ->where('id_produk', $id_produk)
                                  ->get()->getRow();
                
                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$id_produk} tidak ditemukan.");
                }
                
                $satuan_per_dus = (int)$produk->satuan_per_dus;
                
                // Validasi berdasarkan kategori
                if ($data['kategori_asal'] === 'Internal' && $id_gudang_asal) {
                    // Validasi dan kurangi stok untuk kategori Internal
                    $stok_historis = $this->stokModel->getHistoricalStock($id_produk, $id_gudang_asal, $tanggal);
                    
                    if ($jumlah_dus > $stok_historis['dus'] || $jumlah_satuan > $stok_historis['satuan']) {
                        throw new \Exception("Stok '{$produk->nama_produk}' di gudang asal tidak mencukupi pada tanggal yang dipilih.");
                    }
                    
                    // Kurangi stok
                    $this->db->query(
                        "UPDATE stok_produk SET jumlah_dus = jumlah_dus - ?, jumlah_satuan = jumlah_satuan - ? WHERE id_produk = ? AND id_gudang = ?",
                        [$jumlah_dus, $jumlah_satuan, $id_produk, $id_gudang_asal]
                    );
                } elseif ($data['kategori_asal'] === 'Eksternal') {
                    // Untuk kategori Eksternal, tidak ada validasi stok karena barang dari customer
                    // Tapi kita bisa log atau validasi lain jika diperlukan
                }
                
                // Simpan detail kerusakan
                $total_pcs = ($jumlah_dus * $satuan_per_dus) + $jumlah_satuan;
                
                $this->db->table('operpack_kerusakan_detail')->insert([
                    'operpack_id' => $operpack_id,
                    'produk_id' => $id_produk,
                    'jumlah_dus_kembali' => $jumlah_dus,
                    'jumlah_satuan_kembali' => $jumlah_satuan,
                    'total_pcs' => $total_pcs
                ]);
            }
            
            if (!$hasValidItem) {
                throw new \Exception("Tidak ada item valid untuk disimpan.");
            }
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data kerusakan.');
            }
            
            $this->db->transCommit();
            
            $message = 'Data kerusakan berhasil disimpan!';
            if ($data['kategori_asal'] === 'Internal') {
                $message .= ' Stok gudang internal telah diupdate.';
            }
            
            return [
                'success' => true,
                'message' => $message,
                'operpack_id' => $operpack_id
            ];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return [
                'success' => false,
                'message' => 'Transaksi Gagal: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mengambil riwayat kerusakan untuk ditampilkan di tabel.
     */
    public function getRiwayat(array $filters)
    {
        $builder = $this->db->table('operpack_kerusakan_detail okd')
            ->select('okd.id, ok.waktu_diterima, ok.no_surat_jalan, ok.kategori_asal, ok.asal, pr.nama_produk, okd.jumlah_dus_kembali, okd.jumlah_satuan_kembali')
            ->join('operpack_kerusakan ok', 'okd.operpack_id = ok.id')
            ->join('produk pr', 'okd.produk_id = pr.id_produk')
            ->where('DATE(ok.waktu_diterima) >=', $filters['tanggal_mulai'])
            ->where('DATE(ok.waktu_diterima) <=', $filters['tanggal_akhir']);

        if ($filters['produk_id'] !== 'semua') {
            $builder->where('okd.produk_id', $filters['produk_id']);
        }

        if ($filters['kategori_asal'] !== 'semua') {
            $builder->where('ok.kategori_asal', $filters['kategori_asal']);
        }

        return $builder->orderBy('ok.waktu_diterima', 'DESC')
                      ->orderBy('ok.id', 'DESC')
                      ->get()->getResultArray();
    }

    /**
     * Mengambil detail satu item kerusakan untuk diedit.
     */
    public function getDetailRiwayat(int $detail_id)
    {
        $data = $this->db->table('operpack_kerusakan_detail okd')
            ->select('okd.id, okd.produk_id, ok.kategori_asal, ok.asal, ok.waktu_diterima, okd.jumlah_dus_kembali, okd.jumlah_satuan_kembali, p.nama_produk, g.id_gudang as gudang_asal_id')
            ->join('operpack_kerusakan ok', 'okd.operpack_id = ok.id')
            ->join('produk p', 'okd.produk_id = p.id_produk')
            ->join('gudang g', 'ok.asal = g.nama_gudang AND ok.kategori_asal = \'Internal\'', 'left')
            ->where('okd.id', $detail_id)
            ->get()->getRowArray();

        return $data;
    }

    /**
     * Memperbarui data kerusakan dan menyesuaikan stok.
     */
    public function updateKerusakan(array $data)
    {
        $this->db->transBegin();
        
        try {
            $detail_id = (int)$data['detail_id'];
            $new_dus = (int)$data['jumlah_dus'];
            $new_satuan = (int)$data['jumlah_satuan'];
            
            // Ambil data lama
            $old_data = $this->getDetailRiwayat($detail_id);
            if (!$old_data) {
                throw new \Exception("Data kerusakan tidak ditemukan.");
            }
            
            // Jika kategori Internal, lakukan validasi dan update stok
            if ($old_data['kategori_asal'] === 'Internal' && $old_data['gudang_asal_id']) {
                $tanggal = date('Y-m-d', strtotime($old_data['waktu_diterima']));
                
                // Hitung selisih (positif jika jumlah afkir berkurang, negatif jika bertambah)
                $selisih_dus = $old_data['jumlah_dus_kembali'] - $new_dus;
                $selisih_satuan = $old_data['jumlah_satuan_kembali'] - $new_satuan;
                
                // Validasi stok menggunakan historical stock
                $stok_historis = $this->stokModel->getHistoricalStock(
                    $old_data['produk_id'], 
                    $old_data['gudang_asal_id'], 
                    $tanggal
                );
                
                // Stok tersedia adalah stok historis + jumlah yang sudah diafkir sebelumnya
                $stok_tersedia_dus = $stok_historis['dus'] + $old_data['jumlah_dus_kembali'];
                $stok_tersedia_satuan = $stok_historis['satuan'] + $old_data['jumlah_satuan_kembali'];
                
                if ($new_dus > $stok_tersedia_dus || $new_satuan > $stok_tersedia_satuan) {
                    throw new \Exception("Stok di gudang asal tidak mencukupi untuk jumlah afkir baru.");
                }
                
                // Update stok (stok gudang bertambah jika selisih positif)
                $this->db->query(
                    "UPDATE stok_produk SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? WHERE id_produk = ? AND id_gudang = ?",
                    [$selisih_dus, $selisih_satuan, $old_data['produk_id'], $old_data['gudang_asal_id']]
                );
            }
            // Untuk kategori Eksternal, tidak perlu validasi stok karena tidak mempengaruhi stok internal
            
            // Update detail kerusakan
            $this->db->table('operpack_kerusakan_detail')
                    ->where('id', $detail_id)
                    ->update([
                        'jumlah_dus_kembali' => $new_dus,
                        'jumlah_satuan_kembali' => $new_satuan
                    ]);
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal memperbarui data.');
            }
            
            $this->db->transCommit();
            return ['success' => true, 'message' => 'Riwayat kerusakan berhasil diperbarui!'];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Menghapus data kerusakan dan mengembalikan stok (hanya untuk Internal).
     */
    public function hapusKerusakan(int $id)
    {
        $this->db->transBegin();
        
        try {
            // Ambil data yang akan dihapus
            $data_to_delete = $this->getDetailRiwayat($id);
            if (!$data_to_delete) {
                throw new \Exception("Data kerusakan tidak ditemukan.");
            }
            
            // Jika kategori Internal, kembalikan stok
            if ($data_to_delete['kategori_asal'] === 'Internal' && $data_to_delete['gudang_asal_id']) {
                $this->db->query(
                    "UPDATE stok_produk SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? WHERE id_produk = ? AND id_gudang = ?",
                    [
                        $data_to_delete['jumlah_dus_kembali'],
                        $data_to_delete['jumlah_satuan_kembali'],
                        $data_to_delete['produk_id'],
                        $data_to_delete['gudang_asal_id']
                    ]
                );
            }
            // Untuk kategori Eksternal, tidak perlu mengembalikan stok
            
            // Hapus detail kerusakan
            $this->db->table('operpack_kerusakan_detail')->where('id', $id)->delete();
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menghapus data.');
            }
            
            $this->db->transCommit();
            return ['success' => true, 'message' => 'Riwayat kerusakan berhasil dihapus!'];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Mengambil riwayat kerusakan berdasarkan kategori dan asal.
     */
    public function getDamageHistory(string $kategori_asal, string $asal)
    {
        return $this->db->table('operpack_kerusakan ok')
            ->select('ok.no_surat_jalan, ok.waktu_diterima, ok.kategori_asal, ok.asal, COUNT(okd.produk_id) as total_items, SUM(okd.total_pcs) as total_pcs')
            ->join('operpack_kerusakan_detail okd', 'ok.id = okd.operpack_id', 'left')
            ->where('ok.kategori_asal', $kategori_asal)
            ->where('ok.asal', $asal)
            ->groupBy('ok.id')
            ->orderBy('ok.waktu_diterima', 'DESC')
            ->limit(5)
            ->get()->getResultArray();
    }
}
