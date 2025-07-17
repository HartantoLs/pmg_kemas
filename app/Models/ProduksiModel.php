<?php

namespace App\Models;

use CodeIgniter\Model;

class ProduksiModel extends Model
{
    protected $table = 'tbl_jenis_produksi';
    protected $primaryKey = 'nom_jenis_produksi';
    protected $allowedFields = ['jenis_produksi', 'group_jenis_produksi'];

    public function getJenisProduksi()
    {
        return $this->orderBy('jenis_produksi', 'DESC')->findAll();
    }

    public function getInfoProduksi($nomJenisProduksi)
    {
        $db = \Config\Database::connect();
        
        // Get raw materials
        $bahanQuery = "SELECT b.nama_barang, r.jumlah 
                      FROM tbl_rinci_jenis_produksi r 
                      JOIN tbl_barang b ON r.kode_barang = b.kode_barang 
                      WHERE r.nom_jenis_produksi = ?";
        $bahanResult = $db->query($bahanQuery, [$nomJenisProduksi]);
        $bahanBaku = $bahanResult->getResultArray();

        // Get unit info
        $unitQuery = "SELECT p.satuan_per_dus 
                     FROM produk p 
                     JOIN tbl_jenis_produksi j ON p.nama_produk = j.group_jenis_produksi 
                     WHERE j.nom_jenis_produksi = ?";
        $unitResult = $db->query($unitQuery, [$nomJenisProduksi]);
        $unitData = $unitResult->getRowArray();

        $unitLabel = 'Dus';
        if ($unitData && (int)$unitData['satuan_per_dus'] <= 1) {
            $unitLabel = 'Satuan';
        }

        return [
            'bahan_baku' => $bahanBaku,
            'unit_label' => $unitLabel
        ];
    }
}
