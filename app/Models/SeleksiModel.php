<?php

namespace App\Models;

use CodeIgniter\Model;

class SeleksiModel extends Model
{
    protected $table = 'operpack_seleksi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['produk_id', 'tanggal', 'pcs_aman', 'pcs_curah'];

    public function getStokSeleksi($idProduk)
    {
        $db = \Config\Database::connect();
        $query = "SELECT belum_seleksi FROM view_stok_overpack WHERE id_produk = ?";
        $result = $db->query($query, [$idProduk]);
        $stok = $result->getRowArray();
        
        return $stok ?? ['belum_seleksi' => 0];
    }

    public function saveSeleksi($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $idProduk = (int)$data['id_produk'];
            $tanggal = $data['tanggal'];
            $pcsAman = (int)$data['pcs_aman'];
            $pcsCurah = (int)$data['pcs_curah'];
            $totalInput = $pcsAman + $pcsCurah;

            // Basic validation
            if ($pcsAman < 0 || $pcsCurah < 0 || $totalInput == 0) {
                throw new \Exception("Jumlah pcs aman atau curah harus diisi dengan benar.");
            }

            // Check available stock
            $stokData = $this->getStokSeleksi($idProduk);
            $stokTersedia = $stokData ? (int)$stokData['belum_seleksi'] : 0;

            // Validate stock
            if ($totalInput > $stokTersedia) {
                throw new \Exception("Jumlah yang diinput ($totalInput pcs) melebihi stok yang tersedia ($stokTersedia pcs).");
            }

            // Save data
            $seleksiData = [
                'produk_id' => $idProduk,
                'tanggal' => $tanggal,
                'pcs_aman' => $pcsAman,
                'pcs_curah' => $pcsCurah
            ];

            $this->insert($seleksiData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Gagal menyimpan data transaksi.");
            }

            return ['success' => true, 'message' => 'Data seleksi berhasil disimpan!'];

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }
}
