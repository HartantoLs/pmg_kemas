<?php

namespace App\Models;

use CodeIgniter\Model;

class PengemasanModel extends Model
{
    protected $table = 'pengemasan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'tanggal', 'shift', 'gudang_id', 'mesin', 'produk_id', 
        'jumlah_dus', 'jumlah_satuan'
    ];

    public function savePengemasan($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($data['items'] as $item) {
                if (empty($item['jumlah']) || $item['jumlah'] <= 0 || 
                    empty($item['gudang']) || empty($item['mesin']) || 
                    empty($item['jenis_produksi'])) {
                    continue;
                }

                $idGudang = $item['gudang'];
                $kodeMesin = $item['mesin'];
                $nomJenisProduksi = $item['jenis_produksi'];
                $jumlahInput = (int)$item['jumlah'];

                // Get product info
                $produkQuery = "SELECT p.id_produk, p.satuan_per_dus 
                               FROM produk p 
                               JOIN tbl_jenis_produksi j ON p.nama_produk = j.group_jenis_produksi 
                               WHERE j.nom_jenis_produksi = ?";
                $produkResult = $db->query($produkQuery, [$nomJenisProduksi]);
                $dataProduk = $produkResult->getRowArray();

                if (!$dataProduk) {
                    throw new \Exception("Jenis produksi pada salah satu item tidak memiliki produk hasil yang cocok.");
                }

                $idProduk = $dataProduk['id_produk'];
                $satuanPerDus = (int)$dataProduk['satuan_per_dus'];

                // Calculate stock changes
                $perubahanDus = ($satuanPerDus > 1) ? $jumlahInput : 0;
                $perubahanSatuan = ($satuanPerDus <= 1) ? $jumlahInput : 0;

                // Update stock
                $stokQuery = "SELECT id_stok, jumlah_dus, jumlah_satuan 
                             FROM stok_produk 
                             WHERE id_produk = ? AND id_gudang = ? 
                             FOR UPDATE";
                $stokResult = $db->query($stokQuery, [$idProduk, $idGudang]);
                $stokLama = $stokResult->getRowArray();

                $saldoAkhirDus = ($stokLama['jumlah_dus'] ?? 0) + $perubahanDus;
                $saldoAkhirSatuan = ($stokLama['jumlah_satuan'] ?? 0) + $perubahanSatuan;

                if ($stokLama) {
                    $updateStokQuery = "UPDATE stok_produk 
                                       SET jumlah_dus = ?, jumlah_satuan = ?, last_updated = NOW() 
                                       WHERE id_stok = ?";
                    $db->query($updateStokQuery, [$saldoAkhirDus, $saldoAkhirSatuan, $stokLama['id_stok']]);
                } else {
                    $insertStokQuery = "INSERT INTO stok_produk (id_produk, id_gudang, jumlah_dus, jumlah_satuan, last_updated) 
                                       VALUES (?, ?, ?, ?, NOW())";
                    $db->query($insertStokQuery, [$idProduk, $idGudang, $saldoAkhirDus, $saldoAkhirSatuan]);
                }

                // Insert packaging record
                $this->insert([
                    'tanggal' => $data['tanggal'],
                    'shift' => $data['shift'],
                    'gudang_id' => $idGudang,
                    'mesin' => $kodeMesin,
                    'produk_id' => $idProduk,
                    'jumlah_dus' => $perubahanDus,
                    'jumlah_satuan' => $perubahanSatuan
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Gagal menyimpan data transaksi.");
            }

            return ['status' => 'success', 'message' => 'Data pengemasan berhasil disimpan!'];

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }
}
