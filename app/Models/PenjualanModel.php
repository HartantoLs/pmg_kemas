<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['no_surat_jalan', 'pelat_mobil', 'customer', 'tanggal'];

    public function savePenjualan($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert main record
            $penjualanData = [
                'no_surat_jalan' => $data['no_surat_jalan'],
                'pelat_mobil' => $data['pelat_mobil'],
                'customer' => $data['customer'],
                'tanggal' => $data['tanggal']
            ];
            
            $this->insert($penjualanData);
            $penjualanId = $this->getInsertID();

            if ($penjualanId === 0) {
                throw new \Exception("Gagal membuat record penjualan utama.");
            }

            // Process items
            foreach ($data['items'] as $item) {
                $idProduk = (int)($item['produk'] ?? 0);
                $idGudang = (int)($item['gudang'] ?? 0);
                $jumlahDus = (int)($item['jumlah_dus'] ?? 0);
                $jumlahSatuan = (int)($item['jumlah_satuan'] ?? 0);

                if ($idProduk > 0 && $idGudang > 0 && ($jumlahDus > 0 || $jumlahSatuan > 0)) {
                    // Update stock
                    $perubahanDus = -$jumlahDus;
                    $perubahanSatuan = -$jumlahSatuan;

                    $updateStokQuery = "UPDATE stok_produk 
                                       SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? 
                                       WHERE id_produk = ? AND id_gudang = ?";
                    $db->query($updateStokQuery, [$perubahanDus, $perubahanSatuan, $idProduk, $idGudang]);

                    if ($db->affectedRows() === 0) {
                        throw new \Exception("Gagal update stok untuk produk di gudang yang dipilih.");
                    }

                    // Insert detail
                    $detailQuery = "INSERT INTO penjualan_detail (penjualan_id, produk_id, gudang_id, jumlah_dus, jumlah_satuan) 
                                   VALUES (?, ?, ?, ?, ?)";
                    $db->query($detailQuery, [$penjualanId, $idProduk, $idGudang, $jumlahDus, $jumlahSatuan]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Gagal menyimpan data transaksi.");
            }

            return ['success' => true, 'message' => 'Penjualan berhasil disimpan!', 'penjualan_id' => $penjualanId];

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function getCustomerHistory($customer)
    {
        if (empty($customer)) {
            return [];
        }

        return $this->select('no_surat_jalan, tanggal, pelat_mobil')
                   ->like('customer', $customer)
                   ->orderBy('tanggal', 'DESC')
                   ->limit(5)
                   ->findAll();
    }
}
