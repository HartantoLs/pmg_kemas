<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'DashboardController::index');

// Dashboard routes
$routes->get('dashboard', 'DashboardController::index');

// Pengemasan routes
$routes->get('pengemasan', 'PengemasanController::index');
$routes->post('pengemasan/save', 'PengemasanController::save');
$routes->get('pengemasan/get-produksi-types', 'PengemasanController::getProduksiTypes');
$routes->get('pengemasan/get-machines', 'PengemasanController::getMachines');

// Penjualan routes
$routes->get('penjualan', 'PenjualanController::index');
$routes->post('penjualan/save', 'PenjualanController::save');
$routes->get('penjualan/get-produk-info', 'PenjualanController::getProdukInfo');
$routes->get('penjualan/get-current-stock', 'PenjualanController::getCurrentStock');
$routes->get('penjualan/get-customer-history', 'PenjualanController::getCustomerHistory');

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