<?php

namespace App\Models;

use CodeIgniter\Model;

class OperstockModel extends Model
{
    protected $table = 'operstock';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['no_surat_jalan', 'gudang_asal_id', 'gudang_tujuan_id', 'waktu_kirim', 'waktu_terima'];
    protected $db;
    protected $stokModel;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->stokModel = new StokModel();
    }

    public function simpanOperstock(array $data)
    {
        $this->db->transBegin();
        try {
            if (empty($data['items'])) throw new \Exception("Harap tambahkan minimal satu item produk.");
            if ($data['gudang_asal'] === $data['gudang_tujuan']) throw new \Exception("Gudang asal dan tujuan tidak boleh sama.");

            $this->insert([
                'no_surat_jalan'   => $data['no_surat_jalan'],
                'gudang_asal_id'   => $data['gudang_asal'],
                'gudang_tujuan_id' => $data['gudang_tujuan'],
                'waktu_kirim'      => $data['tanggal'] . ' ' . date('H:i:s'),
            ]);

            $operstock_id = $this->db->insertID();
            if (!$operstock_id) throw new \Exception("Gagal membuat record operstock utama.");

            $hasValidItem = false;
            foreach ($data['items'] as $item) {
                $id_produk = (int)($item['produk'] ?? 0);
                $jumlah_dus = (int)($item['jumlah_dus'] ?? 0);
                $jumlah_satuan = (int)($item['jumlah_satuan'] ?? 0);
                                
                if ($id_produk > 0 && ($jumlah_dus > 0 || $jumlah_satuan > 0)) {
                    $hasValidItem = true;
                    $stok_historis_asal = $this->stokModel->getHistoricalStock($id_produk, (int)$data['gudang_asal'], $data['tanggal']);
                    
                    if ($jumlah_dus > $stok_historis_asal['dus'] || $jumlah_satuan > $stok_historis_asal['satuan']) {
                        $nama_produk = $this->db->table('produk')->select('nama_produk')->where('id_produk', $id_produk)->get()->getRow()->nama_produk;
                        throw new \Exception("Stok {$nama_produk} di gudang asal tidak mencukupi pada tanggal yang dipilih.");
                    }

                    // Kurangi stok dari gudang asal
                    $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus - ?, jumlah_satuan = jumlah_satuan - ? WHERE id_produk = ? AND id_gudang = ?", 
                        [$jumlah_dus, $jumlah_satuan, $id_produk, (int)$data['gudang_asal']]);
                                        
                    // Tambah stok ke gudang tujuan
                    $existing = $this->db->table('stok_produk')
                                        ->where('id_produk', $id_produk)
                                        ->where('id_gudang', (int)$data['gudang_tujuan'])
                                        ->get()->getRowArray();

                    if ($existing) {
                        $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? WHERE id_produk = ? AND id_gudang = ?", 
                            [$jumlah_dus, $jumlah_satuan, $id_produk, (int)$data['gudang_tujuan']]);
                    } else {
                        $this->db->table('stok_produk')->insert([
                            'id_produk' => $id_produk, 
                            'id_gudang' => (int)$data['gudang_tujuan'], 
                            'jumlah_dus' => $jumlah_dus, 
                            'jumlah_satuan' => $jumlah_satuan
                        ]);
                    }
                                        
                    $this->db->table('operstock_detail')->insert([
                        'operstock_id' => $operstock_id, 
                        'produk_id' => $id_produk, 
                        'jumlah_dus_dikirim' => $jumlah_dus, 
                        'jumlah_satuan_dikirim' => $jumlah_satuan
                    ]);
                }
            }

            if (!$hasValidItem) throw new \Exception("Tidak ada item valid untuk disimpan.");
            if ($this->db->transStatus() === false) throw new \Exception('Gagal menyimpan data operstock.');
                        
            $this->db->transCommit();
            return ['success' => true, 'message' => 'Perpindahan stok berhasil disimpan!'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Transaksi Gagal: ' . $e->getMessage()];
        }
    }

    public function getRiwayat(array $filters)
    {
        $builder = $this->db->table('operstock_detail od')
            ->select('od.id, o.waktu_kirim, o.no_surat_jalan, pr.nama_produk, pr.satuan_per_dus, g_asal.nama_gudang as gudang_asal, g_tujuan.nama_gudang as gudang_tujuan, od.jumlah_dus_dikirim, od.jumlah_satuan_dikirim')
            ->join('operstock o', 'od.operstock_id = o.id')
            ->join('produk pr', 'od.produk_id = pr.id_produk')
            ->join('gudang g_asal', 'o.gudang_asal_id = g_asal.id_gudang')
            ->join('gudang g_tujuan', 'o.gudang_tujuan_id = g_tujuan.id_gudang')
            ->where('DATE(o.waktu_kirim) >=', $filters['tanggal_mulai'])
            ->where('DATE(o.waktu_kirim) <=', $filters['tanggal_akhir']);

        if ($filters['gudang_id'] !== 'semua') {
            $builder->groupStart()
                   ->where('o.gudang_asal_id', (int)$filters['gudang_id'])
                   ->orWhere('o.gudang_tujuan_id', (int)$filters['gudang_id'])
                   ->groupEnd();
        }

        if ($filters['produk_id'] !== 'semua') {
            $builder->where('od.produk_id', (int)$filters['produk_id']);
        }

        return $builder->orderBy('o.waktu_kirim', 'DESC')->orderBy('o.id', 'DESC')->get()->getResultArray();
    }

    public function getDetailRiwayat(int $detail_id)
    {
        $data = $this->db->table('operstock_detail od')
            ->select('od.id, o.waktu_kirim, od.produk_id, o.gudang_asal_id, o.gudang_tujuan_id, od.jumlah_dus_dikirim, od.jumlah_satuan_dikirim, p.nama_produk, p.satuan_per_dus, g_asal.nama_gudang as nama_gudang_asal, g_tujuan.nama_gudang as nama_gudang_tujuan')
            ->join('operstock o', 'od.operstock_id = o.id')
            ->join('produk p', 'od.produk_id = p.id_produk')
            ->join('gudang g_asal', 'o.gudang_asal_id = g_asal.id_gudang')
            ->join('gudang g_tujuan', 'o.gudang_tujuan_id = g_tujuan.id_gudang')
            ->where('od.id', $detail_id)
            ->get()->getRowArray();

        if ($data) {
            $tanggal = date('Y-m-d', strtotime($data['waktu_kirim']));
            // Ambil stok historis gudang asal
            $stok_historis_asal = $this->stokModel->getHistoricalStock($data['produk_id'], $data['gudang_asal_id'], $tanggal);
            $data['stok_asal_saat_itu_dus'] = $stok_historis_asal['dus'] + (int)$data['jumlah_dus_dikirim'];
            $data['stok_asal_saat_itu_satuan'] = $stok_historis_asal['satuan'] + (int)$data['jumlah_satuan_dikirim'];
            
            // Ambil stok historis gudang tujuan
            $stok_historis_tujuan = $this->stokModel->getHistoricalStock($data['produk_id'], $data['gudang_tujuan_id'], $tanggal);
            $data['stok_tujuan_saat_itu_dus'] = $stok_historis_tujuan['dus'] - (int)$data['jumlah_dus_dikirim'];
            $data['stok_tujuan_saat_itu_satuan'] = $stok_historis_tujuan['satuan'] - (int)$data['jumlah_satuan_dikirim'];
        }

        return $data;
    }

    public function updateOperstock(array $data)
    {
        $this->db->transBegin();
        try {
            $detail_id = (int)$data['detail_id'];
            $new_dus = (int)$data['jumlah_dus'];
            $new_satuan = (int)$data['jumlah_satuan'];
                        
            $old_data = $this->getDetailRiwayat($detail_id);
            if (!$old_data) throw new \Exception("Data operstock tidak ditemukan.");
                        
            // Validasi stok di gudang asal
            if ($new_dus > $old_data['stok_asal_saat_itu_dus'] || $new_satuan > $old_data['stok_asal_saat_itu_satuan']) {
                throw new \Exception("Stok di gudang asal tidak mencukupi untuk jumlah transfer baru.");
            }
                        
            // Hitung selisih
            $selisih_dus = $old_data['jumlah_dus_dikirim'] - $new_dus;
            $selisih_satuan = $old_data['jumlah_satuan_dikirim'] - $new_satuan;
            
            // Update stok asal (bertambah jika selisih positif)
            $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? WHERE id_produk = ? AND id_gudang = ?", 
                [$selisih_dus, $selisih_satuan, $old_data['produk_id'], $old_data['gudang_asal_id']]);
            
            // Update stok tujuan (berkurang jika selisih positif)
            $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus - ?, jumlah_satuan = jumlah_satuan - ? WHERE id_produk = ? AND id_gudang = ?", 
                [$selisih_dus, $selisih_satuan, $old_data['produk_id'], $old_data['gudang_tujuan_id']]);
            
            // Update detail operstock
            $this->db->table('operstock_detail')->where('id', $detail_id)->update([
                'jumlah_dus_dikirim' => $new_dus, 
                'jumlah_satuan_dikirim' => $new_satuan
            ]);
            
            if ($this->db->transStatus() === false) throw new \Exception('Gagal memperbarui data.');
                        
            $this->db->transCommit();
            return ['success' => true, 'message' => 'Riwayat operstock berhasil diperbarui!'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function hapusOperstock(int $id)
    {
        $this->db->transBegin();
        try {
            $data_to_delete = $this->db->table('operstock_detail od')
                ->select('od.id, od.produk_id, o.gudang_asal_id, o.gudang_tujuan_id, od.jumlah_dus_dikirim, od.jumlah_satuan_dikirim')
                ->join('operstock o', 'od.operstock_id = o.id')
                ->where('od.id', $id)
                ->get()->getRowArray();
            
            if (!$data_to_delete) throw new \Exception("Data operstock tidak ditemukan.");
                        
            // Kembalikan stok ke gudang asal
            $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? WHERE id_produk = ? AND id_gudang = ?", 
                [$data_to_delete['jumlah_dus_dikirim'], $data_to_delete['jumlah_satuan_dikirim'], $data_to_delete['produk_id'], $data_to_delete['gudang_asal_id']]);
            
            // Kurangi stok dari gudang tujuan
            $this->db->query("UPDATE stok_produk SET jumlah_dus = jumlah_dus - ?, jumlah_satuan = jumlah_satuan - ? WHERE id_produk = ? AND id_gudang = ?", 
                [$data_to_delete['jumlah_dus_dikirim'], $data_to_delete['jumlah_satuan_dikirim'], $data_to_delete['produk_id'], $data_to_delete['gudang_tujuan_id']]);
            
            $this->db->table('operstock_detail')->where('id', $id)->delete();
            
            if ($this->db->transStatus() === false) throw new \Exception('Gagal menghapus data.');
                        
            $this->db->transCommit();
            return ['success' => true, 'message' => 'Riwayat operstock berhasil dihapus!'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function getTransferHistory($id_gudang_asal, $id_gudang_tujuan)
    {
        if ($id_gudang_asal > 0 && $id_gudang_tujuan > 0) {
            return $this->db->table('operstock o')
                ->select('o.no_surat_jalan, o.waktu_kirim, ga.nama_gudang as gudang_asal, gt.nama_gudang as gudang_tujuan, COUNT(od.produk_id) as total_items')
                ->join('gudang ga', 'o.gudang_asal_id = ga.id_gudang')
                ->join('gudang gt', 'o.gudang_tujuan_id = gt.id_gudang')
                ->join('operstock_detail od', 'o.id = od.operstock_id', 'left')
                ->where('o.gudang_asal_id', $id_gudang_asal)
                ->where('o.gudang_tujuan_id', $id_gudang_tujuan)
                ->groupBy('o.id')
                ->orderBy('o.waktu_kirim', 'DESC')
                ->limit(5)
                ->get()->getResultArray();
        }
        
        return [];
    }
}
