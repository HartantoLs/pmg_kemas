<?php

namespace App\Models;

use CodeIgniter\Model;

class OperstockModel extends Model
{
    protected $table = 'operstock';
    protected $primaryKey = 'id';
    protected $allowedFields = ['no_surat_jalan', 'gudang_asal_id', 'gudang_tujuan_id', 'waktu_kirim'];

    public function saveOperstock($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $gudangAsalId = (int)$data['gudang_asal'];
            $gudangTujuanId = (int)$data['gudang_tujuan'];
            $waktuKirim = $data['tanggal'] . ' ' . date('H:i:s');

            if ($gudangAsalId === $gudangTujuanId) {
                throw new \Exception("Gudang asal dan tujuan tidak boleh sama.");
            }

            if (empty($data['items'])) {
                throw new \Exception("Harap tambahkan minimal satu item produk untuk dipindahkan.");
            }

            // Insert main record
            $operstockData = [
                'no_surat_jalan' => $data['no_surat_jalan'],
                'gudang_asal_id' => $gudangAsalId,
                'gudang_tujuan_id' => $gudangTujuanId,
                'waktu_kirim' => $waktuKirim
            ];

            $this->insert($operstockData);
            $operstockId = $this->getInsertID();

            if ($operstockId === 0) {
                throw new \Exception("Gagal membuat record operstock utama.");
            }

            // Process items
            foreach ($data['items'] as $item) {
                $idProduk = (int)($item['produk'] ?? 0);
                $jumlahDus = (int)($item['jumlah_dus'] ?? 0);
                $jumlahSatuan = (int)($item['jumlah_satuan'] ?? 0);

                if ($idProduk > 0 && ($jumlahDus > 0 || $jumlahSatuan > 0)) {
                    // Reduce stock from source warehouse
                    $kurangStokQuery = "UPDATE stok_produk 
                                       SET jumlah_dus = jumlah_dus - ?, jumlah_satuan = jumlah_satuan - ? 
                                       WHERE id_produk = ? AND id_gudang = ? AND jumlah_dus >= ? AND jumlah_satuan >= ?";
                    $db->query($kurangStokQuery, [$jumlahDus, $jumlahSatuan, $idProduk, $gudangAsalId, $jumlahDus, $jumlahSatuan]);

                    if ($db->affectedRows() === 0) {
                        throw new \Exception("Gagal mengurangi stok dari gudang asal. Stok tidak mencukupi atau produk tidak ada.");
                    }

                    // Check if product exists in destination warehouse
                    $cekStokQuery = "SELECT id_stok FROM stok_produk WHERE id_produk = ? AND id_gudang = ?";
                    $cekResult = $db->query($cekStokQuery, [$idProduk, $gudangTujuanId]);

                    if ($cekResult->getNumRows() > 0) {
                        // Update existing stock
                        $tambahStokQuery = "UPDATE stok_produk 
                                           SET jumlah_dus = jumlah_dus + ?, jumlah_satuan = jumlah_satuan + ? 
                                           WHERE id_produk = ? AND id_gudang = ?";
                        $db->query($tambahStokQuery, [$jumlahDus, $jumlahSatuan, $idProduk, $gudangTujuanId]);
                    } else {
                        // Insert new stock record
                        $insertStokQuery = "INSERT INTO stok_produk (id_produk, id_gudang, jumlah_dus, jumlah_satuan) 
                                           VALUES (?, ?, ?, ?)";
                        $db->query($insertStokQuery, [$idProduk, $gudangTujuanId, $jumlahDus, $jumlahSatuan]);
                    }

                    // Insert detail record
                    $detailQuery = "INSERT INTO operstock_detail (operstock_id, produk_id, jumlah_dus_dikirim, jumlah_satuan_dikirim) 
                                   VALUES (?, ?, ?, ?)";
                    $db->query($detailQuery, [$operstockId, $idProduk, $jumlahDus, $jumlahSatuan]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Gagal menyimpan data transaksi.");
            }

            return ['success' => true, 'message' => 'Perpindahan stok berhasil disimpan!', 'operstock_id' => $operstockId];

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function getTransferHistory($idGudangAsal, $idGudangTujuan)
    {
        if ($idGudangAsal <= 0 || $idGudangTujuan <= 0) {
            return [];
        }

        $db = \Config\Database::connect();
        $query = "SELECT o.no_surat_jalan, o.waktu_kirim,
                         ga.nama_gudang as gudang_asal, gt.nama_gudang as gudang_tujuan,
                         COUNT(od.produk_id) as total_items
                  FROM operstock o 
                  JOIN gudang ga ON o.gudang_asal_id = ga.id_gudang
                  JOIN gudang gt ON o.gudang_tujuan_id = gt.id_gudang
                  LEFT JOIN operstock_detail od ON o.id = od.operstock_id
                  WHERE o.gudang_asal_id = ? AND o.gudang_tujuan_id = ?
                  GROUP BY o.id
                  ORDER BY o.waktu_kirim DESC 
                  LIMIT 5";

        return $db->query($query, [$idGudangAsal, $idGudangTujuan])->getResultArray();
    }
}
