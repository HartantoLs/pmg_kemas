<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';

    /**
     * Mengambil semua produk untuk dropdown filter.
     */
    public function getProdukList()
    {
        return $this->orderBy('nama_produk', 'ASC')->findAll();
    }

    /**
     * Mengambil daftar jenis produksi dari tabel lama.
     */
    public function getJenisProduksi()
    {
        return $this->db->table('tbl_jenis_produksi')
            ->select('nom_jenis_produksi, jenis_produksi')
            ->orderBy('jenis_produksi', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Mengambil info resep dan unit produk berdasarkan nom_jenis.
     */
    public function getInfoProduksi(int $nom_jenis)
    {
        $response = ['bahan_baku' => [], 'unit_label' => 'Dus'];
        if ($nom_jenis <= 0) {
            return $response;
        }

        $response['bahan_baku'] = $this->db->table('tbl_rinci_jenis_produksi r')
            ->select('b.nama_barang, r.jumlah')
            ->join('tbl_barang b', 'r.kode_barang = b.kode_barang')
            ->where('r.nom_jenis_produksi', $nom_jenis)
            ->get()->getResultArray();

        $data_unit = $this->db->table('produk p')
            ->select('p.satuan_per_dus')
            ->join('tbl_jenis_produksi j', 'p.nama_produk = j.group_jenis_produksi')
            ->where('j.nom_jenis_produksi', $nom_jenis)
            ->get()->getRowArray();
            
        if ($data_unit && (int)$data_unit['satuan_per_dus'] <= 1) {
            $response['unit_label'] = 'Satuan';
        }

        return $response;
    }

    /**
     * Mengambil daftar mesin berdasarkan lokasi gudang dari tabel lama.
     */
    public function getMesinByGudang(string $nama_gudang)
    {
        if (empty($nama_gudang)) return [];
        return $this->db->table('tbl_supcus')
            ->select('kode_supcus, nama_supcus')
            ->where('jenis', 'Mesin')
            ->where('lokasi', $nama_gudang)
            ->orderBy('nama_supcus', 'ASC')
            ->get()->getResultArray();
    }
}