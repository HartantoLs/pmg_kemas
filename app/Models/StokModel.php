<?php

namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table            = 'v_stok_pembukuan';
    protected $primaryKey       = 'id_produk'; // HARUS diisi, walaupun view gabungan
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = []; // View tidak digunakan untuk insert/update

    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;
}
