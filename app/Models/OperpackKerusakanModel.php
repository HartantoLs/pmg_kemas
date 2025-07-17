<?php

namespace App\Models;

use CodeIgniter\Model;

class OperpackKerusakanModel extends Model
{
    protected $table = 'operpack_kerusakan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['no_surat_jalan', 'waktu_diterima', 'kategori_asal', 'asal', 'status'];

    public function saveKerusakan($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $noSuratJalan = $data['no_surat_jalan'] ?? '';
            $tanggal = $data['tanggal'] ?? date('Y-m-d');
            $waktuDiterima = $tanggal . ' ' . date('H:i:s');
            $kategoriAsal = $data['kategori_asal'] ?? '';
            $asal = $data['asal'] ?? '';

            // Validation
            if (empty($noSuratJalan)) {
                throw new \Exception("No Surat Jalan harus diisi.");
            }

            if (empty($kategoriAsal)) {
                throw new \Exception("Kategori asal harus dipilih.");
            }

            if (empty($asal)) {
                throw new \Exception("Asal pengembalian harus diisi.");
            }

            if (empty($data['items']) || !is_array($data['items'])) {
                throw new \Exception("Harap tambahkan minimal satu item produk.");
            }

            // Check duplicate no_surat_jalan
            $checkQuery = "SELECT COUNT(*) as count FROM operpack_kerusakan WHERE no_surat_jalan = ?";
            $checkResult = $db->query($checkQuery, [$noSuratJalan]);
            $checkRow = $checkResult->getRowArray();
            if ($checkRow['count'] > 0) {
                throw new \Exception("No Surat Jalan sudah digunakan sebelumnya.");
            }

            // Handle warehouse name conversion for Internal category
            $idGudangAsal = null;
            $namaGudangAsal = $asal;

            if ($kategoriAsal === 'Internal') {
                $idGudangAsal = (int)$asal;
                
                $gudangQuery = "SELECT nama_gudang FROM gudang WHERE id_gudang = ? AND tipe_gudang = 'Produksi'";
                $gudangResult = $db->query($gudangQuery, [$idGudangAsal]);
                if ($gudangResult->getNumRows() === 0) {
                    throw new \Exception("Gudang internal tidak valid.");
                }
                $gudangData = $gudangResult->getRowArray();
                $namaGudangAsal = $gudangData['nama_gudang'];
            }

            // Insert main record
            $headerData = [
                'no_surat_jalan' => $noSuratJalan,
                'waktu_diterima' => $waktuDiterima,
                'kategori_asal' => $kategoriAsal,
                'asal' => $namaGudangAsal,
                'status' => 'Diterima'
            ];

            $this->insert($headerData);
            $operpackId = $this->getInsertID();

            if ($operpackId === 0) {
                throw new \Exception("Gagal membuat record kerusakan utama.");
            }

            // Process items
            foreach ($data['items'] as $item) {
                $idProduk = (int)($item['produk'] ?? 0);
                $jumlahDus = (int)($item['jumlah_dus'] ?? 0);
                $jumlahSatuan = (int)($item['jumlah_satuan'] ?? 0);

                if ($idProduk <= 0) {
                    throw new \Exception("ID produk tidak valid.");
                }

                if ($jumlahDus < 0 || $jumlahSatuan < 0) {
                    throw new \Exception("Jumlah dus dan satuan tidak boleh negatif.");
                }

                if ($jumlahDus == 0 && $jumlahSatuan == 0) {
                    continue;
                }

                // Validate product exists
                $produkQuery = "SELECT nama_produk, satuan_per_dus FROM produk WHERE id_produk = ?";
                $produkResult = $db->query($produkQuery, [$idProduk]);
                if ($produkResult->getNumRows() === 0) {
                    throw new \Exception("Produk dengan ID {$idProduk} tidak ditemukan.");
                }
                $produkData = $produkResult->getRowArray();
                $namaProduk = $produkData['nama_produk'];
                $satuanPerDus = (int)$produkData['satuan_per_dus'];

                // Validate and reduce stock if from Internal
                if ($kategoriAsal === 'Internal' && $idGudangAsal) {
                    $cekStokQuery = "SELECT jumlah_dus, jumlah_satuan FROM stok_produk WHERE id_produk = ? AND id_gudang = ?";
                    $stokResult = $db->query($cekStokQuery, [$idProduk, $idGudangAsal]);

                    if ($stokResult->getNumRows() === 0) {
                        throw new \Exception("Produk '{$namaProduk}' tidak tersedia di gudang yang dipilih.");
                    }

                    $stokData = $stokResult->getRowArray();
                    $stokDus = (int)$stokData['jumlah_dus'];
                    $stokSatuan = (int)$stokData['jumlah_satuan'];

                    if ($stokDus < $jumlahDus) {
                        throw new \Exception("Stok dus tidak mencukupi untuk produk '{$namaProduk}'. Stok tersedia: {$stokDus} dus, diminta: {$jumlahDus} dus.");
                    }

                    if ($stokSatuan < $jumlahSatuan) {
                        throw new \Exception("Stok satuan tidak mencukupi untuk produk '{$namaProduk}'. Stok tersedia: {$stokSatuan} satuan, diminta: {$jumlahSatuan} satuan.");
                    }

                    // Reduce stock
                    $kurangStokQuery = "UPDATE stok_produk SET jumlah_dus = jumlah_dus - ?, jumlah_satuan = jumlah_satuan - ? WHERE id_produk = ? AND id_gudang = ?";
                    $db->query($kurangStokQuery, [$jumlahDus, $jumlahSatuan, $idProduk, $idGudangAsal]);
                }

                // Save damage detail
                $totalPcs = ($jumlahDus * $satuanPerDus) + $jumlahSatuan;
                $detailQuery = "INSERT INTO operpack_kerusakan_detail (operpack_id, produk_id, jumlah_dus_kembali, jumlah_satuan_kembali, total_pcs) VALUES (?, ?, ?, ?, ?)";
                $db->query($detailQuery, [$operpackId, $idProduk, $jumlahDus, $jumlahSatuan, $totalPcs]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Gagal menyimpan data transaksi.");
            }

            $successMessage = 'Data kerusakan berhasil disimpan!' . ($kategoriAsal === 'Internal' ? ' Stok gudang internal telah diupdate.' : '');
            return ['success' => true, 'message' => $successMessage, 'operpack_id' => $operpackId];

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function getDamageHistory($kategoriAsal, $asal)
    {
        if (empty($kategoriAsal) || empty($asal)) {
            return [];
        }

        $db = \Config\Database::connect();
        $query = "SELECT ok.no_surat_jalan, ok.waktu_diterima, ok.kategori_asal, ok.asal,
                         COUNT(okd.produk_id) as total_items,
                         SUM(okd.total_pcs) as total_pcs
                  FROM operpack_kerusakan ok 
                  LEFT JOIN operpack_kerusakan_detail okd ON ok.id = okd.operpack_id
                  WHERE ok.kategori_asal = ? AND ok.asal = ?
                  GROUP BY ok.id
                  ORDER BY ok.waktu_diterima DESC 
                  LIMIT 5";

        return $db->query($query, [$kategoriAsal, $asal])->getResultArray();
    }
}
