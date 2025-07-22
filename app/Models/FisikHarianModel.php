<?php

namespace App\Models;

use CodeIgniter\Model;

class FisikHarianModel extends Model
{
    protected $table = 'log_perbandingan_stok';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tanggal_cek', 'id_produk', 'id_gudang', 'fisik_dus', 'fisik_satuan', 'sistem_dus', 'sistem_satuan', 'selisih_dus', 'selisih_satuan'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getDataByDate($tanggal_cek)
    {
        return $this->where('tanggal_cek', $tanggal_cek)->findAll();
    }

     public function savePerbandingan($data)
    {
        // Hitung selisih sebelum menyimpan
        $data['selisih_dus'] = $data['fisik_dus'] - $data['sistem_dus'];
        $data['selisih_satuan'] = $data['fisik_satuan'] - $data['sistem_satuan'];

        $sql = "INSERT INTO log_perbandingan_stok 
                (tanggal_cek, id_produk, id_gudang, fisik_dus, fisik_satuan, sistem_dus, sistem_satuan, selisih_dus, selisih_satuan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                    fisik_dus = VALUES(fisik_dus), 
                    fisik_satuan = VALUES(fisik_satuan), 
                    sistem_dus = VALUES(sistem_dus), 
                    sistem_satuan = VALUES(sistem_satuan),
                    selisih_dus = VALUES(selisih_dus),
                    selisih_satuan = VALUES(selisih_satuan),
                    updated_at = NOW()";

        return $this->db->query($sql, [
            $data['tanggal_cek'],
            $data['id_produk'], 
            $data['id_gudang'],
            $data['fisik_dus'],
            $data['fisik_satuan'],
            $data['sistem_dus'],
            $data['sistem_satuan'],
            $data['selisih_dus'],
            $data['selisih_satuan']
        ]);
    }

    public function getCurrentStock()
    {
        return $this->db->table('stok_produk')
                       ->select('id_produk, id_gudang, jumlah_dus, jumlah_satuan')
                       ->get()
                       ->getResultArray();
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
