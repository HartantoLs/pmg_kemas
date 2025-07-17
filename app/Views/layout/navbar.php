<?php
// Mengambil service URI dari CodeIgniter untuk mendapatkan path saat ini
$uri = service('uri');
$current_path = "/" . $uri->getPath();

// Fungsi helper untuk menentukan segmen URL mana yang aktif
function isActive($path_segment, $current_path) {
    // Mencocokkan segmen seperti '/pengemasan/', '/laporan/', dll.
    return strpos($current_path, $path_segment) === 0;
}
?>
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= site_url('/') ?>">Dashboard PMG</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php if (isActive('/pengemasan', $current_path)) echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">Form Input</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= site_url('pengemasan') ?>"><i class="fa-solid fa-box fa-fw"></i> Input Pengemasan</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-cart-arrow-down fa-fw"></i> Input Penjualan</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-right-left fa-fw"></i> Input Operstock</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-heart-crack fa-fw"></i> Input Overpack Kerusakan</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-clipboard-check fa-fw"></i> Input Seleksi</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-box-open fa-fw"></i> Input Kemas Ulang</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-clipboard-list fa-fw"></i> Input Fisik Harian</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-warehouse fa-fw"></i> Input Stok Opname</a></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php if (isActive('/laporan', $current_path)) echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">Laporan</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-boxes-stacked fa-fw"></i> Lihat Stok</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-chart-line fa-fw"></i> Laporan Mutasi Produk</a></li>
            </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php if (isActive('/pengemasan/riwayat', $current_path)) echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">Riwayat</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= site_url('pengemasan/riwayat') ?>"><i class="fa-solid fa-box fa-fw"></i> Riwayat Pengemasan</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-receipt fa-fw"></i> Riwayat Penjualan</a></li>
            </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>