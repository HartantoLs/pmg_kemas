<?php

namespace App\Models;

use CodeIgniter\Model;

class KemasUlangModel extends Model
{
    protected $table = 'operpack_kemas_ulang';
    protected $primaryKey = 'id';
    protected $allowedFields = ['produk_id', 'tanggal', 'jumlah_kemas'];

    public function getStokRepack($idProduk)
    {
        $db = \Config\Database::connect();
        $query = "SELECT 
                     vso.hasil_seleksi_aman,
                     vso.hasil_kemas_ulang,
                     (vso.hasil_seleksi_aman - vso.hasil_kemas_ulang) as stok_aman_siap_repack_pcs,
                     p.satuan_per_dus,
                     p.nama_produk
                  FROM view_stok_overpack vso
                  JOIN produk p ON vso.id_produk = p.id_produk
                  WHERE vso.id_produk = ?";
        
        $result = $db->query($query, [$idProduk]);
        $data = $result->getRowArray();

        if ($data) {
            $satuanPerDus = (int)$data['satuan_per_dus'];
            $stokPcs = (int)$data['stok_aman_siap_repack_pcs'];
            $stokPcs = $stokPcs < 0 ? 0 : $stokPcs;

            if ($satuanPerDus > 1) {
                $maxUnit = floor($stokPcs / $satuanPerDus);
                $sisaPcs = $stokPcs % $satuanPerDus;
                $unitType = 'dus';
                $unitLabel = 'Dus';
            } else {
                $maxUnit = $stokPcs;
                $sisaPcs = 0;
                $unitType = 'satuan';
                $unitLabel = 'Satuan/Pcs';
            }

            return [
                'hasil_seleksi_aman' => (int)$data['hasil_seleksi_aman'],
                'hasil_kemas_ulang' => (int)$data['hasil_kemas_ulang'],
                'stok_aman_siap_repack_pcs' => $stokPcs,
                'satuan_per_dus' => $satuanPerDus,
                'max_unit' => $maxUnit,
                'sisa_pcs' => $sisaPcs,
                'unit_type' => $unitType,
                'unit_label' => $unitLabel,
                'nama_produk' => $data['nama_produk']
            ];
        }

        return [
            'hasil_seleksi_aman' => 0,
            'hasil_kemas_ulang' => 0,
            'stok_aman_siap_repack_pcs' => 0,
            'satuan_per_dus' => 1,
            'max_unit' => 0,
            'sisa_pcs' => 0,
            'unit_type' => 'satuan',
            'unit_label' => 'Satuan/Pcs',
            'nama_produk' => ''
        ];
    }

    public function saveRepack($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $idProduk = (int)$data['id_produk'];
            $tanggal = $data['tanggal'];
            $jumlahKemasUnit = (int)$data['jumlah_kemas_unit'];

            if ($jumlahKemasUnit <= 0) {
                throw new \Exception("Jumlah kemas ulang harus lebih dari 0.");
            }

            // Validate stock and conversion
            $stokData = $this->getStokRepack($idProduk);
            
            if (!$stokData || $stokData['max_unit'] == 0) {
                throw new \Exception("Data produk tidak ditemukan atau stok tidak tersedia.");
            }

            $stokPcsTersedia = $stokData['stok_aman_siap_repack_pcs'];
            $satuanPerDus = $stokData['satuan_per_dus'];

            // Convert input to PCS
            if ($satuanPerDus > 1) {
                $jumlahKemasPcs = $jumlahKemasUnit * $satuanPerDus;
                $maxUnitTersedia = $stokData['max_unit'];
                $unitLabel = 'dus';

                if ($jumlahKemasUnit > $maxUnitTersedia) {
                    throw new \Exception("Jumlah yang dikemas ($jumlahKemasUnit dus) melebihi dus yang tersedia ($maxUnitTersedia dus).");
                }
            } else {
                $jumlahKemasPcs = $jumlahKemasUnit;
                $maxUnitTersedia = $stokData['max_unit'];
                $unitLabel = 'satuan';

                if ($jumlahKemasUnit > $maxUnitTersedia) {
                    throw new \Exception("Jumlah yang dikemas ($jumlahKemasUnit satuan) melebihi satuan yang tersedia ($maxUnitTersedia satuan).");
                }
            }

            // Save log
            $logData = [
                'produk_id' => $idProduk,
                'tanggal' => $tanggal,
                'jumlah_kemas' => $jumlahKemasPcs
            ];
            $this->insert($logData);

            // Update stock in Overpack warehouse
            $gudangQuery = "SELECT id_gudang FROM gudang WHERE nama_gudang = 'Overpack' LIMIT 1";
            $gudangResult = $db->query($gudangQuery);
            if ($gudangResult->getNumRows() == 0) {
                throw new \Exception("Gudang 'Overpack' tidak ditemukan di database.");
            }
            $idGudangOverpack = $gudangResult->getRowArray()['id_gudang'];

            if ($satuanPerDus == 1) {
                $dusTambah = 0;
                $satuanTambah = $jumlahKemasPcs;
                $updateStokQuery = "UPDATE stok_produk SET jumlah_satuan = jumlah_satuan + ? WHERE id_produk = ? AND id_gudang = ?";
                $db->query($updateStokQuery, [$satuanTambah, $idProduk, $idGudangOverpack]);
                $updateInfo = "Stok di Gudang Overpack telah ditambahkan: $satuanTambah satuan.";
            } else {
                $dusTambah = floor($jumlahKemasPcs / $satuanPerDus);
                $satuanTambah = $jumlahKemasPcs % $satuanPerDus;
                $updateStokQuery = "UPDATE stok_produk SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? WHERE id_produk = ? AND id_gudang = ?";
                $db->query($updateStokQuery, [$dusTambah, $satuanTambah, $idProduk, $idGudangOverpack]);
                $updateInfo = "Stok di Gudang Overpack telah ditambahkan: $dusTambah dus + $satuanTambah satuan.";
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Gagal menyimpan data transaksi.");
            }

            $successMessage = "Data kemas ulang berhasil disimpan! ";
            $successMessage .= "Input: $jumlahKemasUnit $unitLabel = $jumlahKemasPcs pcs. ";
            $successMessage .= $updateInfo;

            return ['success' => true, 'message' => $successMessage];

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }
}
