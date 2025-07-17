<?php

namespace App\Models;

use CodeIgniter\Model;

class GudangModel extends Model
{
    protected $table = 'gudang';
    protected $primaryKey = 'id_gudang';

    /**
     * Mengambil semua gudang untuk dropdown filter.
     */
    public function getGudangList()
    {
        return $this->orderBy('nama_gudang', 'ASC')->findAll();
    }

    /**
     * Mengambil gudang yang bertipe 'Produksi'.
     */
    public function getGudangProduksi()
    {
        return $this->where('tipe_gudang', 'Produksi')
                    ->orderBy('nama_gudang', 'ASC')
                    ->findAll();
    }
}