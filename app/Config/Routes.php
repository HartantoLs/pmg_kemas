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
    $routes->post('hapusriwayat', 'PengemasanController::hapusRiwayat');
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
$routes->group('operstock', ['namespace' => 'App\Controllers'], static function ($routes) {
    // Rute untuk menampilkan halaman
    $routes->get('input', 'OperstockController::input');
    $routes->get('riwayat', 'OperstockController::riwayat');
        
    // Rute untuk proses AJAX (semua huruf kecil)
    $routes->get('getbothstocks', 'OperstockController::getBothStocks');
    $routes->get('gettransferhistory', 'OperstockController::getTransferHistory');
    $routes->get('getstock', 'OperstockController::getStock');
    $routes->post('simpan', 'OperstockController::simpan');
    $routes->post('filterriwayat', 'OperstockController::filterRiwayat');
    $routes->post('getdetailriwayat', 'OperstockController::getDetailRiwayat');
    $routes->post('updateriwayat', 'OperstockController::updateRiwayat');
    $routes->post('hapusriwayat', 'OperstockController::hapusRiwayat');
});


// Operpack Kerusakan routes
$routes->group('operpack_kerusakan', ['namespace' => 'App\Controllers'], static function ($routes) {
    // Rute untuk menampilkan halaman
    $routes->get('input', 'OperpackKerusakanController::form');
    $routes->get('riwayat', 'OperpackKerusakanController::riwayat');
        
    // Rute untuk proses AJAX
    $routes->get('getgudanginternal', 'OperpackKerusakanController::getGudangInternal');
    $routes->get('getstokproduk', 'OperpackKerusakanController::getStokProduk');
    $routes->get('getdamagehistory', 'OperpackKerusakanController::getDamageHistory');
    $routes->get('validatepenjualan', 'OperpackKerusakanController::validatePenjualan');
    $routes->get('getpenjualanproduk', 'OperpackKerusakanController::getPenjualanProduk');
    $routes->post('simpan', 'OperpackKerusakanController::simpan');
    $routes->post('filterriwayat', 'OperpackKerusakanController::filterRiwayat');
    $routes->post('getdetailriwayat', 'OperpackKerusakanController::getDetailRiwayat');
    $routes->post('updateriwayat', 'OperpackKerusakanController::updateRiwayat');
    $routes->post('hapusriwayat', 'OperpackKerusakanController::hapusRiwayat');
    $routes->post('getstock', 'OperpackKerusakanController::getStock');
});


// Operpack Seleksi Routes
$routes->group('operpack_seleksi', function($routes) {
    $routes->get('input', 'OperpackSeleksiController::form');
    $routes->get('riwayat', 'OperpackSeleksiController::riwayat');
    $routes->get('get-stok-seleksi', 'OperpackSeleksiController::getStokSeleksi');
    $routes->post('simpan-seleksi', 'OperpackSeleksiController::simpanSeleksi');
    $routes->post('filter-data', 'OperpackSeleksiController::filterData');
    $routes->post('get-detail', 'OperpackSeleksiController::getDetail');
    $routes->post('update-seleksi', 'OperpackSeleksiController::updateSeleksi');
    $routes->post('delete-seleksi', 'OperpackSeleksiController::deleteSeleksi');
});


// Operpack Kemas Ulang Routes
$routes->group('operpack_kemas_ulang', function($routes) {
    $routes->get('input', 'OperpackKemasUlangController::input');
    $routes->get('riwayat', 'OperpackKemasUlangController::riwayat');
    $routes->get('get-stok-repack', 'OperpackKemasUlangController::getStokRepack');
    $routes->post('simpan-repack', 'OperpackKemasUlangController::simpanRepack');
    $routes->post('filter-data', 'OperpackKemasUlangController::filterData');
    $routes->post('get-detail', 'OperpackKemasUlangController::getDetail');
    $routes->post('update-kemas-ulang', 'OperpackKemasUlangController::updateKemasUlang');
    $routes->post('delete-kemas-ulang', 'OperpackKemasUlangController::deleteKemasUlang');
});

// Stok Awal Bulan Routes
$routes->group('stok_awal_bulan', function($routes) {
    $routes->get('form', 'StokAwalBulanController::form');
    $routes->post('calculateBeginningStock', 'StokAwalBulanController::calculateBeginningStock');
    $routes->post('saveOpname', 'StokAwalBulanController::saveOpname');
});

// Fisik Harian Routes
$routes->group('fisik_harian', function($routes) {
    $routes->get('form', 'FisikHarianController::form');
    $routes->get('riwayat', 'FisikHarianController::riwayat');
    $routes->post('saveFisikHarian', 'FisikHarianController::saveFisikHarian');
    $routes->post('filterData', 'FisikHarianController::filterData');
});

// Laporan routes
$routes->group('laporan', ['namespace' => 'App\Controllers'], static function ($routes) {
    // Laporan Perbandingan Stok
    $routes->get('perbandingan-stok', 'LaporanController::perbandinganStok');
    $routes->get('getcomparisondata', 'LaporanController::getComparisonData');
    $routes->post('exportcsv', 'LaporanController::exportCSV');
    $routes->get('printlaporan', 'LaporanController::printLaporan');
    
    // Laporan Kartu Stok
    $routes->get('kartu-stok', 'LaporanController::kartuStok');
    $routes->get('getkartustokdata', 'LaporanController::getKartuStokData');
    $routes->post('exportkartustokcsv', 'LaporanController::exportKartuStokCSV');
    
    // Laporan Mutasi Stok
    $routes->get('mutasi-stok', 'LaporanController::mutasiStok');
    $routes->get('getmutasidata', 'LaporanController::getMutasiData');
    $routes->post('exportmutasicsv', 'LaporanController::exportMutasiCSV');
    
    // Laporan Stok Saat Ini
    $routes->get('stok-saat-ini', 'LaporanController::stokSaatIni');
    $routes->post('filterstok', 'LaporanController::filterStok');
    $routes->post('exportstokcsv', 'LaporanController::exportStokCSV');
    
    // Laporan Overpack
    $routes->get('overpack', 'LaporanController::laporanOverpack');
    $routes->get('getoverpackdata', 'LaporanController::getOverpackData');
    $routes->post('exportoverpackcsv', 'LaporanController::exportOverpackCSV');
});


// Admin Routes
$routes->group('admin', function($routes) {
    $routes->get('/', 'AdminController::index');
    
    // Gudang routes
    $routes->get('getgudanglist', 'AdminController::getGudangList');
    $routes->post('savegudang', 'AdminController::saveGudang');
    $routes->post('deletegudang', 'AdminController::deleteGudang');
    
    // Produk routes
    $routes->get('getproduklist', 'AdminController::getProdukList');
    $routes->post('saveproduk', 'AdminController::saveProduk');
    $routes->post('deleteproduk', 'AdminController::deleteProduk');
    
    // Jenis Produksi routes
    $routes->get('getjenisproduksilist', 'AdminController::getJenisProduksiList');
    $routes->get('getjenisproduksidetail', 'AdminController::getJenisProduksiDetail');
    $routes->post('savejenisproduksi', 'AdminController::saveJenisProduksi');
    $routes->post('deletejenisproduksi', 'AdminController::deleteJenisProduksi');
    
    // Barang routes
    $routes->get('getbaranglist', 'AdminController::getBarangList');
});
