<?php

namespace App\Models;

use CodeIgniter\Model;

class StokOpnameModel extends Model
{
    protected $table = 'stok_opname';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tanggal_opname', 'produk_id', 'gudang_id', 'jumlah_dus_opname', 'jumlah_satuan_opname'];

    public function saveStokOpname($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $tanggalOpname = $data['tanggal_opname'] ?? '';
            $items = $data['items'] ?? [];

            if (empty($tanggalOpname) || empty($items)) {
                throw new \Exception('Data tidak lengkap.');
            }

            $savedCount = 0;
            
            foreach ($items as $produkId => $gudangData) {
                foreach ($gudangData as $gudangId => $jumlah) {
                    $dus = intval($jumlah['dus'] ?? 0);
                    $satuan = intval($jumlah['satuan'] ?? 0);

                    // Use INSERT ... ON DUPLICATE KEY UPDATE
                    $query = "INSERT INTO stok_opname (tanggal_opname, produk_id, gudang_id, jumlah_dus_opname, jumlah_satuan_opname) 
                             VALUES (?, ?, ?, ?, ?) 
                             ON DUPLICATE KEY UPDATE 
                             jumlah_dus_opname = VALUES(jumlah_dus_opname), 
                             jumlah_satuan_opname = VALUES(jumlah_satuan_opname)";
                    
                    $db->query($query, [$tanggalOpname, $produkId, $gudangId, $dus, $satuan]);
                    $savedCount++;
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Gagal menyimpan data transaksi.");
            }

            return ['success' => true, 'message' => "Data stock opname berhasil disimpan! ($savedCount records)"];

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function getExistingData($year, $month)
    {
        $db = \Config\Database::connect();
        $query = "SELECT produk_id, gudang_id, jumlah_dus_opname, jumlah_satuan_opname 
                  FROM stok_opname 
                  WHERE YEAR(tanggal_opname) = ? AND MONTH(tanggal_opname) = ?";
        
        $result = $db->query($query, [$year, $month]);
        $existingData = [];
        
        foreach ($result->getResultArray() as $row) {
            $existingData[$row['produk_id']][$row['gudang_id']] = [
                'dus' => $row['jumlah_dus_opname'],
                'satuan' => $row['jumlah_satuan_opname']
            ];
        }
        
        return $existingData;
    }

    public function getStokAktual()
    {
        $db = \Config\Database::connect();
        $query = "SELECT sp.id_produk, sp.id_gudang, sp.jumlah_dus, sp.jumlah_satuan, sp.last_updated
                  FROM stok_produk sp
                  ORDER BY sp.id_produk, sp.id_gudang";
        
        $result = $db->query($query);
        $stokAktualMap = [];
        
        foreach ($result->getResultArray() as $row) {
            $stokAktualMap[$row['id_produk']][$row['id_gudang']] = $row;
        }
        
        return $stokAktualMap;
    }
}
