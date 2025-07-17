<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'DashboardController::index');

// Dashboard routes
$routes->get('dashboard', 'DashboardController::index');

// File: app/Config/Routes.php (Versi Baru)

$routes->group('pengemasan', static function ($routes) {
    $routes->get('/', 'PengemasanController::index');
    $routes->get('riwayat', 'PengemasanController::riwayat');

    // AJAX Routes
    $routes->get('getgudang', 'PengemasanController::getGudang');
    $routes->get('getjenisproduksi', 'PengemasanController::getJenisProduksi');
    $routes->get('getmesin', 'PengemasanController::getMesin');
    $routes->get('getinfoproduksi', 'PengemasanController::getInfoProduksi');

    $routes->post('simpan', 'PengemasanController::simpan');
    $routes->post('filterriwayat', 'PengemasanController::filterRiwayat');
    $routes->post('getdetailriwayat', 'PengemasanController::getDetailRiwayat');
    $routes->post('updateriwayat', 'PengemasanController::updateRiwayat');
    $routes->post('hapusRiwayat', 'PengemasanController::hapusRiwayat');
});

// Penjualan routes
$routes->group('penjualan', ['namespace' => 'App\Controllers'], static function ($routes) {
    $routes->get('input', 'PenjualanController::input');
    $routes->get('riwayat', 'PenjualanController::riwayat');
    
    // Rute untuk AJAX
    $routes->get('getprodukinfo', 'PenjualanController::getProdukInfo');
    $routes->get('getstokpadatanggal', 'PenjualanController::getStokPadaTanggal');
    $routes->get('getcustomerhistory', 'PenjualanController::getCustomerHistory');
    $routes->post('simpan', 'PenjualanController::simpan');
    $routes->post('filterriwayat', 'PenjualanController::filterRiwayat');
    $routes->post('getdetailriwayat', 'PenjualanController::getDetailRiwayat');
    $routes->post('updateriwayat', 'PenjualanController::updateRiwayat');
    $routes->post('hapusriwayat', 'PenjualanController::hapusRiwayat');
});

// Operstock routes
$routes->get('operstock', 'OperstockController::index');
$routes->post('operstock/save', 'OperstockController::save');
$routes->get('operstock/get-both-stocks', 'OperstockController::getBothStocks');
$routes->get('operstock/get-transfer-history', 'OperstockController::getTransferHistory');

// Operpack Kerusakan routes
$routes->get('operpack-kerusakan', 'OperpackKerusakanController::index');
$routes->post('operpack-kerusakan/save', 'OperpackKerusakanController::save');
$routes->get('operpack-kerusakan/get-gudang-internal', 'OperpackKerusakanController::getGudangInternal');
$routes->get('operpack-kerusakan/get-stok-produk', 'OperpackKerusakanController::getStokProduk');
$routes->get('operpack-kerusakan/get-damage-history', 'OperpackKerusakanController::getDamageHistory');

// Seleksi routes
$routes->get('seleksi', 'SeleksiController::index');
$routes->post('seleksi/save', 'SeleksiController::save');
$routes->get('seleksi/get-stok-seleksi', 'SeleksiController::getStokSeleksi');

// Kemas Ulang routes
$routes->get('kemas-ulang', 'KemasUlangController::index');
$routes->post('kemas-ulang/save', 'KemasUlangController::save');
$routes->get('kemas-ulang/get-stok-repack', 'KemasUlangController::getStokRepack');

// Stok Opname routes
$routes->get('stok-opname', 'StokOpnameController::index');
$routes->post('stok-opname/save', 'StokOpnameController::save');

// Other routes
$routes->get('pengadaan', 'ProdukController::pengadaan');
$routes->get('laporan', 'RiwayatController::laporan');
$routes->get('lihat-stok', 'RiwayatController::lihatStok');
$routes->get('fisik-harian', 'RiwayatController::fisikHarian');
$routes->get('riwayat-pengemasan', 'RiwayatController::riwayatPengemasan');
$routes->get('riwayat-penjualan', 'RiwayatController::riwayatPenjualan');
$routes->get('riwayat-operstok', 'RiwayatController::riwayatOperstok');
$routes->get('riwayat-operpack', 'RiwayatController::riwayatOperpack');
$routes->get('riwayat-seleksi', 'RiwayatController::riwayatSeleksi');
$routes->get('riwayat-kemas-ulang', 'RiwayatController::riwayatKemasUlang');

// Laporan routes
$routes->get('laporan/kartu-stok', 'LaporanController::kartuStok');
$routes->get('laporan/mutasi', 'LaporanController::mutasi');
$routes->get('laporan/overpack', 'LaporanController::overpack');
$routes->get('laporan/perbandingan', 'LaporanController::perbandingan');
$routes->get('laporan/lihat-stok', 'LaporanController::lihatStok');