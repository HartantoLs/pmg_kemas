<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $allowedFields = ['nama_produk', 'satuan_per_dus'];

    public function getProdukInfo($idProduk)
    {
        $produk = $this->find($idProduk);
        return $produk ? [
            'satuan_per_dus' => $produk['satuan_per_dus'],
            'nama_produk' => $produk['nama_produk']
        ] : [
            'satuan_per_dus' => 1,
            'nama_produk' => ''
        ];
    }

    public function getCurrentStock($idGudang, $idProduk)
    {
        $db = \Config\Database::connect();
        $query = "SELECT jumlah_dus, jumlah_satuan FROM stok_produk WHERE id_gudang = ? AND id_produk = ?";
        $result = $db->query($query, [$idGudang, $idProduk]);
        return $result->getRowArray() ?? ['jumlah_dus' => 0, 'jumlah_satuan' => 0];
    }
}
