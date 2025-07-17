<?php

namespace App\Models;

use CodeIgniter\Model;

class MesinModel extends Model
{
    protected $table = 'tbl_supcus';
    protected $primaryKey = 'kode_supcus';
    protected $allowedFields = ['nama_supcus', 'jenis', 'lokasi'];

    public function getMesinByLokasi($namaGudang)
    {
        return $this->where('jenis', 'Mesin')
                   ->where('lokasi', $namaGudang)
                   ->orderBy('nama_supcus')
                   ->findAll();
    }
}
