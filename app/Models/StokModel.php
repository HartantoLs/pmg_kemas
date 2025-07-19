<?php

namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Menghitung stok produk di gudang pada tanggal tertentu secara historis.
     * Menggunakan saldo awal bulan + total mutasi hingga tanggal tersebut dari v_semua_transaksi.
     */
    public function getHistoricalStock(int $produk_id, int $gudang_id, string $tanggal)
    {
        $startOfMonth = date('Y-m-01', strtotime($tanggal));

        // 1. Ambil saldo awal bulan sebagai dasar
        $sab = $this->db->table('stok_awal_bulan')
            ->select('jumlah_dus_opname as dus, jumlah_satuan_opname as satuan')
            ->where('produk_id', $produk_id)
            ->where('gudang_id', $gudang_id)
            ->where('DATE_FORMAT(tanggal_opname, "%Y-%m") =', date('Y-m', strtotime($tanggal)))
            ->get()->getRowArray();
            
        $stok_dus = $sab['dus'] ?? 0;
        $stok_satuan = $sab['satuan'] ?? 0;

        // 2. Hitung total mutasi dari awal bulan hingga tanggal yang diminta menggunakan VIEW
        $mutasi = $this->db->table('v_semua_transaksi')
            ->select('COALESCE(SUM(perubahan_dus), 0) as total_dus, COALESCE(SUM(perubahan_satuan), 0) as total_satuan')
            ->where('produk_id', $produk_id)
            ->where('gudang_id', $gudang_id)
            ->where('tanggal_transaksi >=', $startOfMonth)
            ->where('tanggal_transaksi <=', $tanggal)
            ->get()->getRowArray();
        
        // 3. Kalkulasi Stok Akhir Historis
        $stok_dus += $mutasi['total_dus'];
        $stok_satuan += $mutasi['total_satuan'];

        return ['dus' => $stok_dus, 'satuan' => $stok_satuan];
    }

    public function getStokOverpack($produk_id)
    {
        return $this->db->table('view_stok_overpack')
            ->where('id_produk', $produk_id)
            ->get()
            ->getRowArray();
    }

}