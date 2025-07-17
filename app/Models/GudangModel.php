<?php

namespace App\Models;

use CodeIgniter\Model;

class GudangModel extends Model
{
    protected $table = 'gudang';
    protected $primaryKey = 'id_gudang';
    protected $allowedFields = ['nama_gudang', 'tipe_gudang'];

    public function getGudangProduksi()
    {
        return $this->where('tipe_gudang', 'Produksi')
                   ->orderBy('nama_gudang')
                   ->findAll();
    }
}
