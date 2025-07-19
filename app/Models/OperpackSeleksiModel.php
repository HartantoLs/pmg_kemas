<?php

namespace App\Models;

use CodeIgniter\Model;

class OperpackSeleksiModel extends Model
{
    protected $table = 'operpack_seleksi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['produk_id', 'tanggal', 'pcs_aman', 'pcs_curah'];
    protected $useTimestamps = false; // Asumsi tidak ada kolom created_at/updated_at

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getStokSiapRepack(int $produk_id): array
    {
        $result = $this->db->table('operpack_seleksi os')
            ->select('(COALESCE(SUM(os.pcs_aman), 0) - COALESCE(SUM(oku.jumlah_kemas), 0)) AS stok_siap_repack')
            ->join('operpack_kemas_ulang oku', 'os.produk_id = oku.produk_id', 'left')
            ->where('os.produk_id', $produk_id)
            ->groupBy('os.produk_id')
            ->get()->getRowArray();

        return $result ? $result : ['stok_siap_repack' => 0];
    }

    /**
     * Mengambil stok 'belum_seleksi' dari view untuk produk tertentu.
     * Mereplikasi fungsi AJAX `get_stok_seleksi`.
     */
    public function getStokBelumSeleksi(int $produk_id)
    {
        if ($produk_id <= 0) {
            return ['belum_seleksi' => 0];
        }

        $result = $this->db->table('view_stok_overpack')
            ->select('belum_seleksi')
            ->where('id_produk', $produk_id)
            ->get()->getRowArray();
            
        return $result ?? ['belum_seleksi' => 0];
    }

    /**
     * Menyimpan data seleksi baru dengan validasi stok.
     * Mereplikasi fungsi AJAX `simpan_seleksi`.
     */
    public function simpanSeleksi(array $data)
    {
        $this->db->transBegin();
        try {
            $id_produk = (int)$data['id_produk'];
            $pcs_aman = (int)$data['pcs_aman'];
            $pcs_curah = (int)$data['pcs_curah'];
            $total_input = $pcs_aman + $pcs_curah;

            if ($total_input <= 0) {
                throw new \Exception("Jumlah pcs aman atau curah harus diisi dan lebih dari nol.");
            }

            $stok_data = $this->getStokBelumSeleksi($id_produk);
            $stok_tersedia = (int)($stok_data['belum_seleksi'] ?? 0);

            if ($total_input > $stok_tersedia) {
                throw new \Exception("Jumlah input ({$total_input} pcs) melebihi stok yang tersedia ({$stok_tersedia} pcs).");
            }

            $this->insert($data);

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data ke database.');
            }

            $this->db->transCommit();
            return ['success' => true, 'message' => 'Data seleksi berhasil disimpan!'];

        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Mengambil data riwayat seleksi untuk ditampilkan di tabel.
     */
    public function getRiwayat(array $filters)
    {
        $builder = $this->db->table('operpack_seleksi s')
            ->select('s.id, s.tanggal, pr.nama_produk, s.pcs_aman, s.pcs_curah')
            ->join('produk pr', 's.produk_id = pr.id_produk')
            ->where('s.tanggal >=', $filters['tanggal_mulai'])
            ->where('s.tanggal <=', $filters['tanggal_akhir']);

        if ($filters['produk_id'] !== 'semua') {
            $builder->where('s.produk_id', (int)$filters['produk_id']);
        }

        return $builder->orderBy('s.tanggal', 'DESC')->orderBy('s.id', 'DESC')->get()->getResultArray();
    }

    /**
     * Mengambil detail satu item seleksi untuk diedit.
     */
    public function getDetailWithStok(int $id)
    {
        $data = $this->db->table('operpack_seleksi s')
            ->select('s.id, s.produk_id, s.tanggal, s.pcs_aman, s.pcs_curah, p.nama_produk')
            ->join('produk p', 's.produk_id = p.id_produk')
            ->where('s.id', $id)
            ->get()->getRowArray();
        
        if ($data) {
            // Hitung stok yang bisa dipakai untuk edit (kembalikan sementara)
            $stok_sekarang = $this->getStokBelumSeleksi($data['produk_id']);
            $data['stok_tersedia_untuk_edit'] = ($stok_sekarang['belum_seleksi'] ?? 0) + (int)$data['pcs_aman'] + (int)$data['pcs_curah'];

            // Ambil juga stok rusak belum seleksi dari view_stok_overpack
            $stok = $this->db->table('view_stok_overpack')
                ->select('belum_seleksi')
                ->where('id_produk', $data['produk_id'])
                ->get()->getRowArray();

            $data['stok_rusak_belum_seleksi'] = $stok ? (int)$stok['belum_seleksi'] : 0;
        }

        return $data;
    }

    /**
     * Memperbarui data seleksi yang sudah ada.
     */
    public function updateSeleksi(array $data)
    {
        $this->db->transBegin();
        try {
            $id = (int)$data['id'];
            $new_aman = (int)$data['pcs_aman'];
            $new_curah = (int)$data['pcs_curah'];
            $total_baru = $new_aman + $new_curah;

            $old_data = $this->find($id);
            if (!$old_data) throw new \Exception("Data seleksi tidak ditemukan.");
            
            // Fix: Menggunakan getDetailWithStok untuk mendapatkan stok_tersedia_untuk_edit
            $detail_data = $this->getDetailWithStok($id);
            $stok_tersedia = $detail_data['stok_tersedia_untuk_edit'] ?? 0;

            if ($total_baru > $stok_tersedia) {
                throw new \Exception("Jumlah input ({$total_baru} pcs) melebihi stok yang tersedia ({$stok_tersedia} pcs).");
            }
            
            // Validasi jika ada produk yang sudah dikemas ulang
            $selisih_aman = $new_aman - (int)$old_data['pcs_aman'];
            if ($selisih_aman < 0) { // Jika jumlah pcs aman dikurangi
                $stok_siap_repack_data = $this->getStokSiapRepack($old_data['produk_id']);
                $stok_siap_repack = (int)($stok_siap_repack_data['stok_siap_repack'] ?? 0);
                
                if (abs($selisih_aman) > $stok_siap_repack) {
                    throw new \Exception("Gagal mengurangi Pcs Aman. Sebagian produk kemungkinan sudah dikemas ulang.");
                }
            }

            $this->update($id, ['pcs_aman' => $new_aman, 'pcs_curah' => $new_curah]);

            if ($this->db->transStatus() === false) throw new \Exception('Gagal memperbarui data.');

            $this->db->transCommit();
            return ['success' => true, 'message' => 'Riwayat seleksi berhasil diperbarui!'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }
    
    /**
     * Menghapus data seleksi.
     */
    public function hapusSeleksi(int $id)
    {
        $this->db->transBegin();
        try {
            // Validasi jika ada produk yang sudah dikemas ulang
            $data_to_delete = $this->find($id);
            if (!$data_to_delete) {
                throw new \Exception('Data tidak ditemukan.');
            }
            
            $stok_siap_repack_data = $this->getStokSiapRepack($data_to_delete['produk_id']);
            $stok_siap_repack = (int)($stok_siap_repack_data['stok_siap_repack'] ?? 0);
            
            if ((int)$data_to_delete['pcs_aman'] > $stok_siap_repack) {
                throw new \Exception('Gagal menghapus. Sebagian Pcs Aman dari log ini kemungkinan sudah dikemas ulang.');
            }

            $this->delete($id);
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menghapus data dari database.');
            }

            $this->db->transCommit();
            return ['success' => true, 'message' => 'Riwayat seleksi berhasil dihapus!'];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }
}