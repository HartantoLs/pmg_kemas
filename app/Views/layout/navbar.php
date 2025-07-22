<?php
// Mengambil service URI dari CodeIgniter untuk mendapatkan path saat ini
$uri = service('uri');
// Mengambil semua segments dalam bentuk array
$segments = $uri->getSegments();

// Mengambil segmen URL dengan aman menggunakan array indexing
$segment1 = isset($segments[0]) ? $segments[0] : '';
$segment2 = isset($segments[1]) ? $segments[1] : '';



// Mendapatkan full path untuk pengecekan yang lebih akurat
$currentPath = $uri->getPath();

$isDashboard = ($currentPath === '/' || $segment1 === '' || $segment1 === 'dashboard');

// Daftar segmen URL yang termasuk dalam kategori 'Form Input'
$form_input_segments = [
    'pengemasan', 'penjualan', 'operstock', 'operpack_kerusakan', 
    'operpack_seleksi', 'kemas-ulang', 'stok-opname', 'fisik-harian', 'pengadaan'
];

// Daftar segmen URL yang termasuk dalam kategori 'Admin Panel'
$admin_segments = [
    'admin', 'users', 'settings', 'permissions', 'logs'
];
?>
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid">
   <a class="navbar-brand <?php if ($isDashboard) echo 'active'; ?>" href="<?= site_url('/') ?>">Dashboard PMG</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php if (in_array($segment1, $form_input_segments) && $segment2 !== 'riwayat') echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">Form Input</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= site_url('pengemasan') ?>"><i class="fa-solid fa-box fa-fw"></i> Input Pengemasan</a></li>
            <li><a class="dropdown-item" href="<?= site_url('penjualan/input') ?>"><i class="fa-solid fa-cart-arrow-down fa-fw"></i> Input Penjualan</a></li>
            <li><a class="dropdown-item" href="<?= site_url('operstock/input') ?>"><i class="fa-solid fa-right-left fa-fw"></i> Input Operstock</a></li>
            <li><a class="dropdown-item" href="<?= site_url('operpack_kerusakan/input') ?>"><i class="fa-solid fa-screwdriver-wrench fa-fw"></i> Input Overpack Kerusakan</a></li>
            <li><a class="dropdown-item" href="<?= site_url('operpack_seleksi/input') ?>"><i class="fa-solid fa-clipboard-check fa-fw"></i> Input Seleksi</a></li>
            <li><a class="dropdown-item" href="<?= site_url('operpack_kemas_ulang/input') ?>"><i class="fa-solid fa-box-open fa-fw"></i> Input Kemas Ulang</a></li>
            <li><a class="dropdown-item" href="<?= site_url('fisik_harian/form') ?>"><i class="fa-solid fa-clipboard-list fa-fw"></i> Input Fisik Harian</a></li>
            <li><a class="dropdown-item" href="<?= site_url('stok_awal_bulan/form') ?>"><i class="fa-solid fa-warehouse fa-fw"></i> Input Stok Opname</a></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php if ($segment1 === 'laporan') echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">Laporan</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= site_url('laporan/stok-saat-ini') ?>"><i class="fa-solid fa-boxes-stacked fa-fw"></i> Lihat Stok</a></li>
            <li><a class="dropdown-item" href="<?= site_url('laporan/mutasi-stok') ?>"><i class="fa-solid fa-chart-line fa-fw"></i> Laporan Mutasi Produk</a></li>
            <li><a class="dropdown-item" href="<?= site_url('laporan/kartu-stok') ?>"><i class="fa-solid fa-book fa-fw"></i> Laporan Kartu Stok</a></li>
            <li><a class="dropdown-item" href="<?= site_url('laporan/perbandingan-stok') ?>"><i class="fa-solid fa-scale-balanced fa-fw"></i> Laporan Perbandingan</a></li>
            <li><a class="dropdown-item" href="<?= site_url('laporan/overpack') ?>"><i class="fa-solid fa-recycle fa-fw"></i> Laporan Overpack</a></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php if ($segment2 === 'riwayat' || strpos($currentPath, 'riwayat') !== false) echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">Riwayat</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= site_url('pengemasan/riwayat') ?>"><i class="fa-solid fa-box fa-fw"></i> Riwayat Pengemasan</a></li>
            <li><a class="dropdown-item" href="<?= site_url('penjualan/riwayat') ?>"><i class="fa-solid fa-cart-arrow-down fa-fw"></i> Riwayat Penjualan</a></li>
            <li><a class="dropdown-item" href="<?= site_url('operstock/riwayat') ?>"><i class="fa-solid fa-right-left fa-fw"></i> Riwayat Operstock</a></li>
            <li><a class="dropdown-item" href="<?= site_url('operpack_kerusakan/riwayat') ?>"><i class="fa-solid fa-screwdriver-wrench fa-fw"></i> Riwayat Overpack</a></li>
            <li><a class="dropdown-item" href="<?= site_url('operpack_seleksi/riwayat') ?>"><i class="fa-solid fa-clipboard-check fa-fw"></i> Riwayat Seleksi</a></li>
            <li><a class="dropdown-item" href="<?= site_url('operpack_kemas_ulang/riwayat') ?>"><i class="fa-solid fa-box-open fa-fw"></i> Riwayat Kemas Ulang</a></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php if (in_array($segment1, $admin_segments)) echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">Admin Panel</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= site_url('admin') ?>"><i class="fa-solid fa-tachometer-alt fa-fw"></i> Dashboard Admin</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>