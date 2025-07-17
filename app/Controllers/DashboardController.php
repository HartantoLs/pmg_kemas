<?php

namespace App\Controllers;

use App\Models\DashboardModel;

class DashboardController extends BaseController
{
    /**
     * Menampilkan halaman utama dashboard.
     */
    public function index()
    {
        $dashboardModel = new DashboardModel();

        // Ambil data dari model
        $stats = $dashboardModel->getStats();
        $aktivitas_terbaru = $dashboardModel->getRecentActivity();

        // Logika sapaan berdasarkan waktu
        date_default_timezone_set('Asia/Jakarta');
        $sapaan = "Selamat Datang";
        $jam = date('H');
        if ($jam >= 5 && $jam < 12) { $sapaan = "Selamat Pagi"; } 
        elseif ($jam >= 12 && $jam < 15) { $sapaan = "Selamat Siang"; } 
        elseif ($jam >= 15 && $jam < 18) { $sapaan = "Selamat Sore"; } 
        else { $sapaan = "Selamat Malam"; }

        // Siapkan data untuk dikirim ke view
        $data = [
            'page_title' => 'Dashboard',
            'sapaan' => $sapaan,
            'total_produk' => $stats['total_produk'],
            'penjualan_bulan_ini_formatted' => number_format($stats['penjualan_bulan_ini']) . " Pcs",
            'stok_menipis' => $stats['stok_menipis'],
            'aktivitas_terbaru' => $aktivitas_terbaru
        ];

        return view('dashboard/index', $data);
    }
}