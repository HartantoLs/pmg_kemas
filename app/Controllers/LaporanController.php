<?php

namespace App\Controllers;

use App\Models\LaporanKartuStokModel;
use App\Models\LaporanMutasiModel;
use App\Models\LaporanStokModel;
use App\Models\LaporanOverpackModel;
use App\Models\LaporanPerbandinganModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;

class LaporanController extends BaseController
{
    protected $kartuStokModel;
    protected $mutasiModel;
    protected $stokModel;
    protected $overpackModel;
    protected $perbandinganModel;
    protected $gudangModel;
    protected $produkModel;

    public function __construct()
    {
        $this->kartuStokModel = new LaporanKartuStokModel();
        $this->mutasiModel = new LaporanMutasiModel();
        $this->stokModel = new LaporanStokModel();
        $this->overpackModel = new LaporanOverpackModel();
        $this->perbandinganModel = new LaporanPerbandinganModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
    }

    /**
     * Halaman Laporan Perbandingan Stok
     */
    public function perbandinganStok()
    {
        $data = [
            'page_title' => 'Laporan Perbandingan Stok',
            'gudang_list' => $this->gudangModel->getGudangList(),
            'produk_list' => $this->produkModel->getProdukList(),
            'selected_date' => $this->request->getGet('tanggal') ?? date('Y-m-d'),
            'filter_gudang' => $this->request->getGet('id_gudang') ?? 'semua',
            'filter_produk' => $this->request->getGet('produk_id') ?? 'semua',
        ];

        return view('laporan/perbandingan_stok', $data);
    }

    /**
     * AJAX: Mengambil data perbandingan stok
     */
    public function getComparisonData()
    {
        try {
            $filters = [
                'tanggal' => $this->request->getGet('tanggal') ?? date('Y-m-d'),
                'id_gudang' => $this->request->getGet('id_gudang') ?? 'semua',
                'produk_id' => $this->request->getGet('produk_id') ?? 'semua'
            ];

            $report_data = $this->perbandinganModel->getPerbandinganStok($filters);
            $stats = $this->perbandinganModel->calculateStatistics($report_data);
            $filter_names = $this->perbandinganModel->getFilterNames($filters);

            return $this->response->setJSON([
                'success' => true,
                'report_data' => $report_data,
                'stats' => $stats,
                'filters' => $filter_names
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getComparisonData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export Perbandingan Stok ke CSV
     */
    public function exportCSV()
    {
        try {
            $filters = [
                'tanggal' => $this->request->getPost('tanggal') ?? date('Y-m-d'),
                'id_gudang' => $this->request->getPost('id_gudang') ?? 'semua',
                'produk_id' => $this->request->getPost('produk_id') ?? 'semua'
            ];

            $report_data = $this->perbandinganModel->getPerbandinganStok($filters);
            $csv_data = $this->perbandinganModel->exportToCSV($report_data, $filters);

            $filename = 'perbandingan_stok_' . date('Y-m-d_H-i-s') . '.csv';
            
            $this->response->setHeader('Content-Type', 'text/csv');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            foreach ($csv_data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
            
            return $this->response;

        } catch (\Exception $e) {
            log_message('error', 'Error in exportCSV: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal export CSV: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Print Laporan Perbandingan Stok
     */
    public function printLaporan()
    {
        try {
            $filters = [
                'tanggal' => $this->request->getGet('tanggal') ?? date('Y-m-d'),
                'id_gudang' => $this->request->getGet('id_gudang') ?? 'semua',
                'produk_id' => $this->request->getGet('produk_id') ?? 'semua'
            ];

            $report_data = $this->perbandinganModel->getPerbandinganStok($filters);
            $stats = $this->perbandinganModel->calculateStatistics($report_data);
            $filter_names = $this->perbandinganModel->getFilterNames($filters);

            $data = [
                'report_data' => $report_data,
                'stats' => $stats,
                'filters' => $filter_names
            ];

            return view('laporan/perbandingan_stok_print', $data);

        } catch (\Exception $e) {
            log_message('error', 'Error in printLaporan: ' . $e->getMessage());
            return view('errors/html/error_general', ['message' => 'Gagal memuat halaman print']);
        }
    }

    /**
     * Halaman Laporan Kartu Stok
     */
    public function kartuStok()
    {
        $data = [
            'page_title' => 'Laporan Kartu Stok Produk',
            'gudang_list' => $this->gudangModel->getGudangList(),
            'produk_list' => $this->produkModel->getProdukList(),
            'tgl_mulai' => $this->request->getGet('tanggal_mulai') ?? date('Y-m-01'),
            'tgl_akhir' => $this->request->getGet('tanggal_akhir') ?? date('Y-m-t'),
            'filter_gudang' => $this->request->getGet('gudang_id') ?? 'semua',
            'filter_produk' => $this->request->getGet('produk_id') ?? null,
        ];

        return view('laporan/kartu_stok', $data);
    }

    /**
     * AJAX: Mengambil data kartu stok
     */
    public function getKartuStokData()
    {
        try {
            $filter_produk = $this->request->getGet('produk_id');
            $tgl_mulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
            $tgl_akhir = $this->request->getGet('tanggal_akhir') ?? date('Y-m-t');
            $filter_gudang = $this->request->getGet('gudang_id') ?? 'semua';

            if (!$filter_produk) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Produk harus dipilih'
                ]);
            }

            // Ambil info produk
            $produk_info = $this->produkModel->find($filter_produk);
            if (!$produk_info) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ]);
            }

            // Ambil info gudang
            $gudang_name = 'Semua Gudang';
            if ($filter_gudang !== 'semua') {
                $gudang_info = $this->gudangModel->find($filter_gudang);
                if ($gudang_info) {
                    $gudang_name = $gudang_info['nama_gudang'];
                }
            }

            // Hitung saldo awal
            $saldo_awal = $this->kartuStokModel->getSaldoAwal($filter_produk, $tgl_mulai, $filter_gudang);

            // Ambil detail transaksi
            $transaksi = $this->kartuStokModel->getDetailTransaksi($filter_produk, $tgl_mulai, $tgl_akhir, $filter_gudang);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'produk_info' => $produk_info,
                    'gudang_name' => $gudang_name,
                    'saldo_awal' => $saldo_awal,
                    'transaksi' => $transaksi,
                    'periode' => date('d/m/Y', strtotime($tgl_mulai)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir)),
                    'total_transaksi' => count($transaksi)
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getKartuStokData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export Kartu Stok ke CSV
     */
    public function exportKartuStokCSV()
    {
        try {
            $filter_produk = $this->request->getPost('produk_id');
            $tgl_mulai = $this->request->getPost('tanggal_mulai') ?? date('Y-m-01');
            $tgl_akhir = $this->request->getPost('tanggal_akhir') ?? date('Y-m-t');
            $filter_gudang = $this->request->getPost('gudang_id') ?? 'semua';

            if (!$filter_produk) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Produk harus dipilih'
                ]);
            }

            $produk_info = $this->produkModel->find($filter_produk);
            $saldo_awal = $this->kartuStokModel->getSaldoAwal($filter_produk, $tgl_mulai, $filter_gudang);
            $transaksi = $this->kartuStokModel->getDetailTransaksi($filter_produk, $tgl_mulai, $tgl_akhir, $filter_gudang);

            $gudang_name = 'Semua Gudang';
            if ($filter_gudang !== 'semua') {
                $gudang_info = $this->gudangModel->find($filter_gudang);
                if ($gudang_info) {
                    $gudang_name = $gudang_info['nama_gudang'];
                }
            }

            $filters = [
                'nama_produk' => $produk_info['nama_produk'],
                'nama_gudang' => $gudang_name,
                'periode' => date('d/m/Y', strtotime($tgl_mulai)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir)),
                'saldo_awal_dus' => $saldo_awal['dus'],
                'saldo_awal_satuan' => $saldo_awal['satuan']
            ];

            $csv_data = $this->kartuStokModel->exportToCSV($transaksi, $filters);

            $filename = 'kartu_stok_' . date('Y-m-d_H-i-s') . '.csv';
            
            $this->response->setHeader('Content-Type', 'text/csv');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            foreach ($csv_data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
            
            return $this->response;

        } catch (\Exception $e) {
            log_message('error', 'Error in exportKartuStokCSV: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal export CSV: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman Laporan Mutasi Stok
     */
    public function mutasiStok()
    {
        $data = [
            'page_title' => 'Laporan Mutasi Stok Per Produk',
            'gudang_list' => $this->gudangModel->getGudangList(),
            'produk_list' => $this->produkModel->getProdukList(),
            'tipe_laporan' => $this->request->getGet('tipe_laporan') ?? 'harian',
            'tgl_laporan' => $this->request->getGet('tanggal') ?? date('Y-m-d'),
            'tgl_mulai' => $this->request->getGet('tanggal_mulai') ?? date('Y-m-01'),
            'tgl_akhir' => $this->request->getGet('tanggal_akhir') ?? date('Y-m-t'),
            'filter_gudang' => $this->request->getGet('gudang_id') ?? 'semua',
            'filter_produk' => $this->request->getGet('produk_id') ?? 'semua',
        ];

        return view('laporan/mutasi_stok', $data);
    }

    /**
     * AJAX: Mengambil data mutasi stok
     */
    public function getMutasiData()
    {
        try {
            $filters = [
                'tipe_laporan' => $this->request->getGet('tipe_laporan') ?? 'harian',
                'tanggal' => $this->request->getGet('tanggal') ?? date('Y-m-d'),
                'tanggal_mulai' => $this->request->getGet('tanggal_mulai') ?? date('Y-m-01'),
                'tanggal_akhir' => $this->request->getGet('tanggal_akhir') ?? date('Y-m-t'),
                'gudang_id' => $this->request->getGet('gudang_id') ?? 'semua',
                'produk_id' => $this->request->getGet('produk_id') ?? 'semua'
            ];

            $report_data = $this->mutasiModel->getMutasiStok($filters);
            $totals = $this->mutasiModel->calculateTotals($report_data);
            $warehouse_columns = $this->mutasiModel->getWarehouseColumns($filters['gudang_id']);

            // Ambil nama filter
            $gudang_name = 'Semua Gudang';
            if ($filters['gudang_id'] !== 'semua') {
                $gudang_info = $this->gudangModel->find($filters['gudang_id']);
                if ($gudang_info) {
                    $gudang_name = $gudang_info['nama_gudang'];
                }
            }

            $produk_name = null;
            if ($filters['produk_id'] !== 'semua') {
                $produk_info = $this->produkModel->find($filters['produk_id']);
                if ($produk_info) {
                    $produk_name = $produk_info['nama_produk'];
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'report_data' => $report_data,
                'totals' => $totals,
                'warehouse_columns' => $warehouse_columns,
                'filters_info' => [
                    'tipe_laporan' => $filters['tipe_laporan'],
                    'tgl_laporan_formatted' => date('d F Y', strtotime($filters['tanggal'])),
                    'tgl_mulai_formatted' => date('d F Y', strtotime($filters['tanggal_mulai'])),
                    'tgl_akhir_formatted' => date('d F Y', strtotime($filters['tanggal_akhir'])),
                    'gudang_name' => $gudang_name,
                    'produk_name' => $produk_name,
                    'filter_gudang' => $filters['gudang_id'],
                    'report_data_count' => count($report_data)
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getMutasiData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    public function exportMutasiCSV()
    {
        try {
            
            $filters = [
                'tipe_laporan'  => $this->request->getPost('tipe_laporan') ?? 'harian',
                'tanggal'       => $this->request->getPost('tanggal') ?? date('Y-m-d'),
                'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?? date('Y-m-01'),
                'tanggal_akhir' => $this->request->getPost('tanggal_akhir') ?? date('Y-m-t'),
                'gudang_id'     => $this->request->getPost('gudang_id') ?? 'semua',
                'produk_id'     => $this->request->getPost('produk_id') ?? 'semua',
            ];


            $report_data = $this->laporanMutasiModel->getMutasiStok($filters);
            $totals = $this->laporanMutasiModel->calculateTotals($report_data);
            $warehouse_columns = $this->laporanMutasiModel->getWarehouseColumns($filters['gudang_id']);

            $csv_data = $this->laporanMutasiModel->exportToCSV($report_data, $totals, $warehouse_columns);

            $filename = 'laporan_mutasi_stok_' . date('Ymd_His') . '.csv';
            
            $this->response->setHeader('Content-Type', 'text/csv');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            

            $output = fopen('php://output', 'w');
            foreach ($csv_data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);

            exit();

        } catch (\Exception $e) {
            log_message('error', '[LaporanController::exportMutasiCSV] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat file CSV: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Laporan Stok Saat Ini
     */
    public function stokSaatIni()
    {
        $data = [
            'page_title' => 'Laporan Stok Produk Saat Ini',
            'gudang_list' => $this->gudangModel->getGudangList(),
        ];

        return view('laporan/stok_saat_ini', $data);
    }

    /**
     * AJAX: Filter stok saat ini
     */
    public function filterStok()
    {
        try {
            $filters = [
                'tanggal_laporan' => $this->request->getPost('tanggal_laporan') ?? date('Y-m-d'),
                'id_gudang' => $this->request->getPost('id_gudang') ?? 'semua',
                'search' => $this->request->getPost('search') ?? ''
            ];

            $report_data = $this->stokModel->getStokProduk($filters);
            $stats = $this->stokModel->calculateStatistics($report_data);

            // Generate table body HTML
            ob_start();
            if (empty($report_data)) {
                echo '<tr><td colspan="5" style="text-align: center; padding: 3rem; color: #6b7280;"><i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i><br>Tidak ada data untuk ditampilkan.</td></tr>';
            } else {
                $no = 1;
                foreach ($report_data as $row) {
                    $total_stock = $row['final_dus'] + $row['final_satuan'];
                    $status_class = $total_stock > 10 ? 'stock-available' : ($total_stock > 0 ? 'stock-low' : 'stock-empty');
                    $status_text = $total_stock > 10 ? 'Tersedia' : ($total_stock > 0 ? 'Sedikit' : 'Kosong');
                    $status_icon = $total_stock > 10 ? 'check-circle' : ($total_stock > 0 ? 'exclamation-triangle' : 'times-circle');
                    ?>
                    <tr>
                        <td><div class="row-number"><?php echo $no++; ?></div></td>
                        <td class="text-left">
                            <div class="product-info">
                                <div class="product-name"><?php echo htmlspecialchars($row['nama_produk']); ?></div>
                                <div class="product-unit">
                                    <?php echo ($row['satuan_per_dus'] > 1) ? $row['satuan_per_dus'] . ' satuan/dus' : 'Satuan only'; ?>
                                </div>
                            </div>
                        </td>
                        <td><div class="stock-number"><?php echo number_format((int)($row['final_dus'] ?? 0)); ?></div></td>
                        <td><div class="stock-number"><?php echo number_format((int)($row['final_satuan'] ?? 0)); ?></div></td>
                        <td>
                            <span class="stock-status <?php echo $status_class; ?>">
                                <i class="fas fa-<?php echo $status_icon; ?>"></i> <?php echo $status_text; ?>
                            </span>
                        </td>
                    </tr>
                    <?php
                }
            }
            $table_body_html = ob_get_clean();

            return $this->response->setJSON([
                'success' => true,
                'table_body' => $table_body_html,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in filterStok: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export Stok Saat Ini ke CSV
     */
    public function exportStokCSV()
    {
        try {
            $filters = [
                'tanggal_laporan' => $this->request->getPost('tanggal_laporan') ?? date('Y-m-d'),
                'id_gudang' => $this->request->getPost('id_gudang') ?? 'semua',
                'search' => $this->request->getPost('search') ?? ''
            ];

            $report_data = $this->stokModel->getStokProduk($filters);

            $gudang_name = 'Total Semua Gudang';
            if ($filters['id_gudang'] !== 'semua') {
                $gudang_info = $this->gudangModel->find($filters['id_gudang']);
                if ($gudang_info) {
                    $gudang_name = $gudang_info['nama_gudang'] . ' (' . $gudang_info['tipe_gudang'] . ')';
                }
            }

            $filters['gudang_name'] = $gudang_name;
            $filters['tanggal_laporan'] = $this->request->getPost('tanggal_laporan') ?? date('Y-m-d');
            $csv_data = $this->stokModel->exportToCSV($report_data, $filters);

            $filename = 'stok_produk_' . date('Y-m-d_H-i-s') . '.csv';
            
            $this->response->setHeader('Content-Type', 'text/csv');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            foreach ($csv_data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
            
            return $this->response;

        } catch (\Exception $e) {
            log_message('error', 'Error in exportStokCSV: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal export CSV: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Halaman Laporan Overpack
     */
    public function laporanOverpack()
    {
        $data = [
            'page_title' => 'Laporan Stok Overpack',
            'produk_list' => $this->produkModel->getProdukList(),
            'tipe_laporan' => $this->request->getGet('tipe_laporan') ?? 'harian',
            'selected_date' => $this->request->getGet('tanggal') ?? date('Y-m-d'),
            'start_date' => $this->request->getGet('tanggal_mulai') ?? date('Y-m-01'),
            'end_date' => $this->request->getGet('tanggal_akhir') ?? date('Y-m-t'),
            'filter_produk' => $this->request->getGet('produk_id') ?? 'semua',
        ];

        return view('laporan/overpack', $data);
    }

    /**
     * AJAX: Mengambil data laporan overpack
     */
    public function getOverpackData()
    {
        try {
            $filters = [
                'tipe_laporan' => $this->request->getGet('tipe_laporan') ?? 'harian',
                'tanggal' => $this->request->getGet('tanggal') ?? date('Y-m-d'),
                'tanggal_mulai' => $this->request->getGet('tanggal_mulai') ?? date('Y-m-01'),
                'tanggal_akhir' => $this->request->getGet('tanggal_akhir') ?? date('Y-m-t'),
                'produk_id' => $this->request->getGet('produk_id') ?? 'semua'
            ];

            $result = $this->overpackModel->getLaporanOverpack($filters);

            return $this->response->setJSON([
                'success' => true,
                'report_data' => $result['report_data'],
                'grand_totals' => $result['grand_totals'],
                'filters_info' => $filters
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in getOverpackData: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export Overpack ke CSV
     */
    public function exportOverpackCSV()
    {
        try {
            $filters = [
                'tipe_laporan' => $this->request->getPost('tipe_laporan') ?? 'harian',
                'tanggal' => $this->request->getPost('tanggal') ?? date('Y-m-d'),
                'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?? date('Y-m-01'),
                'tanggal_akhir' => $this->request->getPost('tanggal_akhir') ?? date('Y-m-t'),
                'produk_id' => $this->request->getPost('produk_id') ?? 'semua'
            ];

            $result = $this->overpackModel->getLaporanOverpack($filters);
            $csv_data = $this->overpackModel->exportToCSV($result, $filters);

            $filename = 'laporan_overpack_' . date('Y-m-d_H-i-s') . '.csv';
            
            $this->response->setHeader('Content-Type', 'text/csv');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            foreach ($csv_data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
            
            return $this->response;

        } catch (\Exception $e) {
            log_message('error', 'Error in exportOverpackCSV: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal export CSV: ' . $e->getMessage()
            ]);
        }
    }
}
