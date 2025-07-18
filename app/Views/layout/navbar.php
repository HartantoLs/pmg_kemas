  <?php
  // Mengambil service URI dari CodeIgniter untuk mendapatkan path saat ini
  $uri = service('uri');
  // Mengambil segmen URL dengan aman. Contoh: /penjualan/riwayat -> $segment1='penjualan', $segment2='riwayat'
  $segment1 = $uri->getSegment(1) ?? '';
  $segment2 = $uri->getSegment(2) ?? '';

  // Daftar segmen URL yang termasuk dalam kategori 'Form Input'
  $form_input_segments = [
      'pengemasan', 'penjualan', 'operstock', 'operpack_kerusakan', 
      'seleksi', 'kemas-ulang', 'stok-opname', 'fisik-harian', 'pengadaan'
  ];
  ?>
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?= site_url('/') ?>">📊 Dashboard PMG</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php if (in_array($segment1, $form_input_segments) && $segment2 !== 'riwayat') echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">📝 Form Input</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= site_url('pengemasan') ?>"><i class="fa-solid fa-box fa-fw"></i> Input Pengemasan</a></li>
              <li><a class="dropdown-item" href="<?= site_url('penjualan/input') ?>"><i class="fa-solid fa-cart-arrow-down fa-fw"></i> Input Penjualan</a></li>
              <li><a class="dropdown-item" href="<?= site_url('operstock/input') ?>"><i class="fa-solid fa-right-left fa-fw"></i> Input Operstock</a></li>
              <li><a class="dropdown-item" href="<?= site_url('operpack_kerusakan/input') ?>"><i class="fa-solid fa-heart-crack fa-fw"></i> Input Overpack Kerusakan</a></li>
              <li><a class="dropdown-item" href="<?= site_url('seleksi/input') ?>"><i class="fa-solid fa-clipboard-check fa-fw"></i> Input Seleksi</a></li>
              <li><a class="dropdown-item" href="<?= site_url('kemas_ulang/input') ?>"><i class="fa-solid fa-box-open fa-fw"></i> Input Kemas Ulang</a></li>
              <li><a class="dropdown-item" href="<?= site_url('fisik_harian/input') ?>"><i class="fa-solid fa-clipboard-list fa-fw"></i> Input Fisik Harian</a></li>
              <li><a class="dropdown-item" href="<?= site_url('stok_opname/input') ?>"><i class="fa-solid fa-warehouse fa-fw"></i> Input Stok Opname</a></li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php if ($segment1 === 'laporan') echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">📄 Laporan</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= site_url('laporan/lihat-stok') ?>"><i class="fa-solid fa-boxes-stacked fa-fw"></i> Lihat Stok</a></li>
              <li><a class="dropdown-item" href="<?= site_url('laporan/mutasi') ?>"><i class="fa-solid fa-chart-line fa-fw"></i> Laporan Mutasi Produk</a></li>
              <li><a class="dropdown-item" href="<?= site_url('laporan/kartu-stok') ?>"><i class="fa-solid fa-book fa-fw"></i> Laporan Kartu Stok</a></li>
              <li><a class="dropdown-item" href="<?= site_url('laporan/perbandingan') ?>"><i class="fa-solid fa-scale-balanced fa-fw"></i> Laporan Perbandingan</a></li>
              <li><a class="dropdown-item" href="<?= site_url('laporan/overpack') ?>"><i class="fa-solid fa-recycle fa-fw"></i> Laporan Overpack</a></li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php if ($segment2 === 'riwayat' || strpos($uri->getPath(), 'riwayat-') === 0) echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">⏳ Riwayat</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= site_url('pengemasan/riwayat') ?>"><i class="fa-solid fa-box fa-fw"></i> Riwayat Pengemasan</a></li>
              <li><a class="dropdown-item" href="<?= site_url('penjualan/riwayat') ?>"><i class="fa-solid fa-receipt fa-fw"></i> Riwayat Penjualan</a></li>
              <li><a class="dropdown-item" href="<?= site_url('operstock/riwayat') ?>"><i class="fa-solid fa-truck-ramp-box fa-fw"></i> Riwayat Operstock</a></li>
              <li><a class="dropdown-item" href="<?= site_url('operpack_kerusakan/riwayat') ?>"><i class="fa-solid fa-screwdriver-wrench fa-fw"></i> Riwayat Overpack</a></li>
              <li><a class="dropdown-item" href="<?= site_url('seleksi/riwayat') ?>"><i class="fa-solid fa-check-double fa-fw"></i> Riwayat Seleksi</a></li>
              <li><a class="dropdown-item" href="<?= site_url('kemas_ulang/riwayat') ?>"><i class="fa-solid fa-boxes-packing fa-fw"></i> Riwayat Kemas Ulang</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
