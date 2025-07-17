<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Mengambil data untuk kartu statistik utama.
     */
    public function getStats()
    {
        $stats = [];

        // Total Jenis Produk
        $stats['total_produk'] = $this->db->table('produk')->countAllResults();

        // Penjualan Bulan Ini (dalam Pcs)
        $query_penjualan = "SELECT SUM((pd.jumlah_dus * IFNULL(pr.satuan_per_dus, 1)) + pd.jumlah_satuan) as total_pcs 
                            FROM penjualan p 
                            JOIN penjualan_detail pd ON p.id = pd.penjualan_id 
                            JOIN produk pr ON pd.produk_id = pr.id_produk 
                            WHERE MONTH(p.tanggal) = MONTH(CURDATE()) AND YEAR(p.tanggal) = YEAR(CURDATE())";
        $penjualan_result = $this->db->query($query_penjualan)->getRow();
        $stats['penjualan_bulan_ini'] = $penjualan_result->total_pcs ?? 0;

        // Produk dengan Stok Menipis
        $query_stok_menipis = "SELECT COUNT(*) as total FROM (
                                SELECT id_produk FROM stok_produk 
                                GROUP BY id_produk 
                                HAVING SUM(jumlah_dus) < 5 AND SUM(jumlah_dus) > 0
                               ) as low_stock_products";
        $stok_menipis_result = $this->db->query($query_stok_menipis)->getRow();
        $stats['stok_menipis'] = $stok_menipis_result->total ?? 0;

        return $stats;
    }

    /**
     * Mengambil 5 aktivitas transaksi terbaru dari berbagai tabel.
     */
    public function getRecentActivity()
    {
        $query_aktivitas = "
            (SELECT p.tanggal as waktu, 'Penjualan' as jenis, CONCAT('Ke: ', p.customer, ' (SJ: ', p.no_surat_jalan, ')') as detail, 'fa-cart-plus' as icon, 'text-success' as color FROM penjualan p ORDER BY p.tanggal DESC LIMIT 2) 
            UNION ALL 
            (SELECT o.waktu_kirim as waktu, 'Operstock' as jenis, CONCAT('Dari ', ga.nama_gudang, ' ke ', gt.nama_gudang) as detail, 'fa-right-left' as icon, 'text-info' as color FROM operstock o JOIN gudang ga ON o.gudang_asal_id = ga.id_gudang JOIN gudang gt ON o.gudang_tujuan_id = gt.id_gudang ORDER BY o.waktu_kirim DESC LIMIT 2) 
            UNION ALL 
            (SELECT ok.waktu_diterima as waktu, 'Barang Rusak' as jenis, CONCAT('Dari: ', ok.asal, ' (SJ: ', ok.no_surat_jalan, ')') as detail, 'fa-triangle-exclamation' as icon, 'text-danger' as color FROM operpack_kerusakan ok ORDER BY ok.waktu_diterima DESC LIMIT 1) 
            ORDER BY waktu DESC LIMIT 5";
        
        return $this->db->query($query_aktivitas)->getResultArray();
    }
}