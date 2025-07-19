<?php

namespace App\Models;

use CodeIgniter\Model;

class FisikHarianModel extends Model
{
    protected $table = 'log_perbandingan_stok';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tanggal_cek', 'id_produk', 'id_gudang', 'fisik_dus', 'fisik_satuan', 'sistem_dus', 'sistem_satuan'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getDataByDate($tanggal_cek)
    {
        return $this->where('tanggal_cek', $tanggal_cek)->findAll();
    }

    public function savePerbandingan($data)
    {
        $existing = $this->where([
            'tanggal_cek' => $data['tanggal_cek'],
            'id_produk' => $data['id_produk'],
            'id_gudang' => $data['id_gudang']
        ])->first();

        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    public function getFilteredData($tanggal_dari = null, $tanggal_sampai = null, $produk_id = null, $gudang_id = null)
    {
        $builder = $this->db->table('log_perbandingan_stok lps')
                           ->select('lps.*, p.nama_produk, g.nama_gudang, p.satuan_per_dus')
                           ->join('produk p', 'p.id_produk = lps.id_produk')
                           ->join('gudang g', 'g.id_gudang = lps.id_gudang')
                           ->orderBy('lps.tanggal_cek', 'DESC')
                           ->orderBy('p.nama_produk', 'ASC')
                           ->orderBy('g.nama_gudang', 'ASC');

        if ($tanggal_dari) {
            $builder->where('lps.tanggal_cek >=', $tanggal_dari);
        }

        if ($tanggal_sampai) {
            $builder->where('lps.tanggal_cek <=', $tanggal_sampai);
        }

        if ($produk_id && $produk_id != '') {
            $builder->where('lps.id_produk', $produk_id);
        }

        if ($gudang_id && $gudang_id != '') {
            $builder->where('lps.id_gudang', $gudang_id);
        }

        return $builder->get()->getResultArray();
    }

    public function getSelisihData($tanggal_dari = null, $tanggal_sampai = null)
    {
        $builder = $this->db->table('log_perbandingan_stok lps')
                           ->select('lps.*, p.nama_produk, g.nama_gudang, p.satuan_per_dus,
                                    (lps.fisik_dus - lps.sistem_dus) as selisih_dus,
                                    (lps.fisik_satuan - lps.sistem_satuan) as selisih_satuan')
                           ->join('produk p', 'p.id_produk = lps.id_produk')
                           ->join('gudang g', 'g.id_gudang = lps.id_gudang')
                           ->where('(lps.fisik_dus != lps.sistem_dus OR lps.fisik_satuan != lps.sistem_satuan)')
                           ->orderBy('lps.tanggal_cek', 'DESC');

        if ($tanggal_dari) {
            $builder->where('lps.tanggal_cek >=', $tanggal_dari);
        }

        if ($tanggal_sampai) {
            $builder->where('lps.tanggal_cek <=', $tanggal_sampai);
        }

        return $builder->get()->getResultArray();
    }
}
