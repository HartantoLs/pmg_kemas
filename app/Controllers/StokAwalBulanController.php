<?php

namespace App\Controllers;

use App\Models\StokAwalBulanModel;
use App\Models\StokModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;
use CodeIgniter\Controller;

class StokAwalBulanController extends BaseController
{
    protected $stokAwalBulanModel;
    protected $stokModel;
    protected $gudangModel;
    protected $produkModel;

    public function __construct()
    {
        $this->stokAwalBulanModel = new StokAwalBulanModel();
        $this->stokModel = new StokModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
    }

    public function form()
    {
        try {
            // Initialize default data
            $data = [
                'title' => 'Form Stock Opname',
                'current_month_year' => date('Y-m'),
                'selected_month_year' => $this->request->getGet('tanggal_opname_month') ?? date('Y-m'),
                'gudang_list' => [],
                'produk_list' => [],
                'existing_data' => [],
                'mode' => 'create',
                'error_message' => null
            ];

            // Validasi format tanggal
            if (!preg_match('/^\d{4}-\d{2}$/', $data['selected_month_year'])) {
                throw new \Exception("Format tanggal tidak valid: " . $data['selected_month_year']);
            }

            $selected_tanggal = $data['selected_month_year'] . '-01';
            $selected_year = date('Y', strtotime($selected_tanggal));
            $selected_month = date('m', strtotime($selected_tanggal));

            // Log untuk debugging
            log_message('info', "Loading stock opname data for: {$selected_year}-{$selected_month}");

            // Load gudang list dengan error handling
            try {
                $data['gudang_list'] = $this->gudangModel->getGudangList();
                if (empty($data['gudang_list'])) {
                    log_message('warning', "No gudang data found");
                }
            } catch (\Exception $e) {
                log_message('error', "Error loading gudang list: " . $e->getMessage());
                throw new \Exception("Gagal memuat data gudang: " . $e->getMessage());
            }

            // Load produk list dengan error handling
            try {
                $data['produk_list'] = $this->produkModel->getProdukList();
                if (empty($data['produk_list'])) {
                    log_message('warning', "No produk data found");
                }
            } catch (\Exception $e) {
                log_message('error', "Error loading produk list: " . $e->getMessage());
                throw new \Exception("Gagal memuat data produk: " . $e->getMessage());
            }

            // Load existing data dengan error handling
            try {
                $existing = $this->stokAwalBulanModel->getDataByMonth($selected_year, $selected_month);
                
                if (!empty($existing)) {
                    $data['mode'] = 'edit';
                    log_message('info', "Found " . count($existing) . " existing records for {$selected_year}-{$selected_month}");
                    
                    foreach ($existing as $row) {
                        // Validasi data row
                        if (empty($row['produk_id']) || empty($row['gudang_id'])) {
                            log_message('warning', "Invalid row data: " . json_encode($row));
                            continue;
                        }
                        
                        $data['existing_data'][$row['produk_id']][$row['gudang_id']] = [
                            'dus' => $row['jumlah_dus_opname'] ?? 0,
                            'satuan' => $row['jumlah_satuan_opname'] ?? 0,
                            'catatan' => $row['catatan'] ?? ''
                        ];
                    }
                } else {
                    log_message('info', "No existing data found for {$selected_year}-{$selected_month}");
                }
            } catch (\Exception $e) {
                log_message('error', "Error loading existing data: " . $e->getMessage());
                throw new \Exception("Gagal memuat data existing: " . $e->getMessage());
            }

            // Log summary
            log_message('info', "Data loaded successfully - Gudang: " . count($data['gudang_list']) . 
                       ", Produk: " . count($data['produk_list']) . 
                       ", Mode: " . $data['mode']);

            return view('stok_awal_bulan/form', $data);

        } catch (\Exception $e) {
            // Log error lengkap
            log_message('error', "StokAwalBulanController::input() Error: " . $e->getMessage());
            log_message('error', "Stack trace: " . $e->getTraceAsString());

            // Return view dengan error message
            $data = [
                'title' => 'Form Stock Opname - Error',
                'current_month_year' => date('Y-m'),
                'selected_month_year' => $this->request->getGet('tanggal_opname_month') ?? date('Y-m'),
                'gudang_list' => [],
                'produk_list' => [],
                'existing_data' => [],
                'mode' => 'create',
                'error_message' => $e->getMessage()
            ];

            return view('stok_awal_bulan/form', $data);
        }
    }

    public function calculateBeginningStock()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $target_month_year = $this->request->getPost('tanggal_opname_month') ?? date('Y-m');
            
            // Validasi format
            if (!preg_match('/^\d{4}-\d{2}$/', $target_month_year)) {
                throw new \Exception("Invalid date format: " . $target_month_year);
            }

            $previous_month_date = new \DateTime($target_month_year . '-01');
            $previous_month_date->modify('-1 month');
            $prev_year = $previous_month_date->format('Y');
            $prev_month = $previous_month_date->format('m');

            $start_of_prev_month = $prev_year . '-' . $prev_month . '-01';
            $end_of_prev_month = $previous_month_date->format('Y-m-t');

            log_message('info', "Calculating beginning stock for {$target_month_year}, using previous month: {$prev_year}-{$prev_month}");

            // Ambil data opname bulan sebelumnya
            try {
                $opname_data = $this->stokAwalBulanModel->getDataByMonth($prev_year, $prev_month);
                $calculated_stock = [];
                $opname_found = !empty($opname_data);

                log_message('info', "Found " . count($opname_data) . " opname records for {$prev_year}-{$prev_month}");

                foreach ($opname_data as $row) {
                    if (empty($row['produk_id']) || empty($row['gudang_id'])) {
                        log_message('warning', "Invalid opname row: " . json_encode($row));
                        continue;
                    }
                    
                    $calculated_stock[$row['produk_id']][$row['gudang_id']] = [
                        'dus' => (int)($row['jumlah_dus_opname'] ?? 0),
                        'satuan' => (int)($row['jumlah_satuan_opname'] ?? 0)
                    ];
                }
            } catch (\Exception $e) {
                log_message('error', "Error getting opname data: " . $e->getMessage());
                throw new \Exception("Gagal mengambil data opname: " . $e->getMessage());
            }

            // Ambil mutasi bulan sebelumnya
            try {
                $mutasi_data = $this->stokAwalBulanModel->getMutasiByPeriod($start_of_prev_month, $end_of_prev_month);
                
                log_message('info', "Found " . count($mutasi_data) . " mutation records for period {$start_of_prev_month} to {$end_of_prev_month}");

                foreach ($mutasi_data as $row) {
                    if (empty($row['produk_id']) || empty($row['gudang_id'])) {
                        log_message('warning', "Invalid mutation row: " . json_encode($row));
                        continue;
                    }

                    $p_id = $row['produk_id'];
                    $g_id = $row['gudang_id'];
                    
                    if (!isset($calculated_stock[$p_id][$g_id])) {
                        $calculated_stock[$p_id][$g_id] = ['dus' => 0, 'satuan' => 0];
                    }
                    
                    $calculated_stock[$p_id][$g_id]['dus'] += (int)($row['total_mutasi_dus'] ?? 0);
                    $calculated_stock[$p_id][$g_id]['satuan'] += (int)($row['total_mutasi_satuan'] ?? 0);
                }
            } catch (\Exception $e) {
                log_message('error', "Error getting mutation data: " . $e->getMessage());
                throw new \Exception("Gagal mengambil data mutasi: " . $e->getMessage());
            }

            log_message('info', "Beginning stock calculation completed successfully");

            return $this->response->setJSON([
                'success' => true,
                'opname_found' => $opname_found,
                'calculated_stock' => $calculated_stock
            ]);

        } catch (\Exception $e) {
            log_message('error', "calculateBeginningStock error: " . $e->getMessage());
            log_message('error', "Stack trace: " . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function saveOpname()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $db = \Config\Database::connect();
        
        try {
            $tanggal_opname = $this->request->getPost('tanggal_opname');
            $items = $this->request->getPost('items');
            $catatan_global = $this->request->getPost('catatan') ?? '';

            // Validasi input
            if (empty($tanggal_opname)) {
                throw new \Exception('Tanggal opname tidak boleh kosong');
            }

            if (empty($items) || !is_array($items)) {
                throw new \Exception('Data items tidak valid atau kosong');
            }

            // Validasi format tanggal
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_opname)) {
                throw new \Exception('Format tanggal opname tidak valid: ' . $tanggal_opname);
            }

            log_message('info', "Saving opname data for date: {$tanggal_opname}, items count: " . count($items));

            // Start transaction dengan error detail
            $db->transBegin();
            log_message('info', "Database transaction started");

            $saved_count = 0;
            $error_items = [];
            $transaction_errors = [];

            foreach ($items as $produk_id => $gudang_data) {
                if (!is_array($gudang_data)) {
                    $error_items[] = "Invalid data for produk_id: {$produk_id}";
                    continue;
                }

                foreach ($gudang_data as $gudang_id => $jumlah) {
                    try {
                        $dus = intval($jumlah['dus'] ?? 0);
                        $satuan = intval($jumlah['satuan'] ?? 0);
                        $catatan = $jumlah['catatan'] ?? $catatan_global;
                        
                        // Validasi ID dengan lebih detail
                        if (empty($produk_id) || !is_numeric($produk_id)) {
                            $error_items[] = "Invalid produk_id: '{$produk_id}' (type: " . gettype($produk_id) . ")";
                            continue;
                        }

                        if (empty($gudang_id) || !is_numeric($gudang_id)) {
                            $error_items[] = "Invalid gudang_id: '{$gudang_id}' for produk_id: {$produk_id} (type: " . gettype($gudang_id) . ")";
                            continue;
                        }

                        $data = [
                            'tanggal_opname' => $tanggal_opname,
                            'produk_id' => (int)$produk_id,
                            'gudang_id' => (int)$gudang_id,
                            'jumlah_dus_opname' => $dus,
                            'jumlah_satuan_opname' => $satuan,
                            'catatan' => $catatan
                        ];

                        log_message('debug', "Attempting to save item: " . json_encode($data));

                        // Cek apakah produk_id dan gudang_id valid (opsional - jika ada referential constraint)
                        /*
                        $produk_exists = $this->produkModel->find($produk_id);
                        if (!$produk_exists) {
                            $error_items[] = "Produk ID {$produk_id} not found in database";
                            continue;
                        }

                        $gudang_exists = $this->gudangModel->find($gudang_id);
                        if (!$gudang_exists) {
                            $error_items[] = "Gudang ID {$gudang_id} not found in database";
                            continue;
                        }
                        */

                        $save_result = $this->stokAwalBulanModel->saveOpname($data);
                        
                        if ($save_result === false) {
                            // Get detailed database error
                            $db_error = $db->error();
                            $error_message = "Failed to save P{$produk_id}-G{$gudang_id}";
                            
                            if (!empty($db_error['message'])) {
                                $error_message .= " - DB Error: " . $db_error['message'];
                            }
                            
                            if (!empty($db_error['code'])) {
                                $error_message .= " (Code: " . $db_error['code'] . ")";
                            }
                            
                            $error_items[] = $error_message;
                            $transaction_errors[] = $error_message;
                            
                            log_message('error', "Database error for P{$produk_id}-G{$gudang_id}: " . json_encode($db_error));
                        } else {
                            $saved_count++;
                            log_message('debug', "Successfully saved P{$produk_id}-G{$gudang_id}");
                        }

                    } catch (\Exception $e) {
                        $error_message = "Exception saving P{$produk_id}-G{$gudang_id}: " . $e->getMessage();
                        $error_items[] = $error_message;
                        $transaction_errors[] = $error_message;
                        log_message('error', "Exception saving item P{$produk_id}-G{$gudang_id}: " . $e->getMessage());
                        log_message('error', "Stack trace: " . $e->getTraceAsString());
                    }
                }
            }

            // Cek apakah ada error kritis yang harus menggagalkan transaksi
            if (!empty($transaction_errors) && count($transaction_errors) > (count($items) * 0.5)) {
                throw new \Exception('Too many save errors: ' . implode('; ', array_slice($transaction_errors, 0, 3)) . (count($transaction_errors) > 3 ? '...' : ''));
            }

            if ($saved_count === 0) {
                throw new \Exception('No items were saved successfully. Errors: ' . implode('; ', array_slice($error_items, 0, 3)));
            }

            // Rekalkulasi bulan-bulan berikutnya
            $recalculated_months = [];
            try {
                log_message('info', "Starting recalculation for following months");
                $recalculated_months = $this->stokAwalBulanModel->recalculateFollowingMonths($tanggal_opname);
                log_message('info', "Recalculation completed for months: " . implode(', ', $recalculated_months));
            } catch (\Exception $e) {
                $recalc_error = "Recalculation error: " . $e->getMessage();
                log_message('error', $recalc_error);
                $error_items[] = "Warning: " . $recalc_error;
            }

            // Commit transaction dengan detail
            if ($db->transStatus() === false) {
                $db_error = $db->error();
                log_message('error', "Transaction commit failed - DB Error: " . json_encode($db_error));
                throw new \Exception('Transaction commit failed: ' . ($db_error['message'] ?? 'Unknown database error'));
            }

            $db->transCommit();
            log_message('info', "Database transaction committed successfully");

            $message = "Data stock opname berhasil diperbarui! ({$saved_count} records saved)";
            if (!empty($error_items)) {
                $message .= "<br><span class='text-warning'>Warning: " . count($error_items) . " issues found - check logs for details</span>";
            }
            
            if (!empty($recalculated_months)) {
                $message .= "<br><b>Rekalkulasi otomatis berhasil!</b> Stok awal untuk bulan " . implode(', ', $recalculated_months) . " telah diperbarui.";
            }

            log_message('info', "SaveOpname completed successfully. Saved: {$saved_count}, Errors: " . count($error_items) . ", Recalculated months: " . count($recalculated_months));

            return $this->response->setJSON([
                'success' => true, 
                'message' => $message,
                'details' => [
                    'saved_count' => $saved_count,
                    'error_count' => count($error_items),
                    'recalculated_months' => count($recalculated_months)
                ]
            ]);

        } catch (\Exception $e) {
            // Rollback transaction dengan detail
            if ($db->transStatus() !== false) {
                $db->transRollback();
                log_message('info', "Database transaction rolled back due to error");
            }
            
            // Get additional database error info
            $db_error = $db->error();
            $error_details = [
                'exception_message' => $e->getMessage(),
                'db_error' => $db_error,
                'transaction_status' => $db->transStatus()
            ];
            
            log_message('error', "saveOpname error details: " . json_encode($error_details));
            log_message('error', "Exception stack trace: " . $e->getTraceAsString());
            
            // Build detailed error message
            $error_message = 'Gagal menyimpan: ' . $e->getMessage();
            
            if (!empty($db_error['message'])) {
                $error_message .= ' | DB Error: ' . $db_error['message'];
            }
            
            if (!empty($db_error['code'])) {
                $error_message .= ' (Code: ' . $db_error['code'] . ')';
            }
            
            return $this->response->setJSON([
                'success' => false, 
                'message' => $error_message,
                'debug_info' => $error_details // Hanya untuk debugging, hapus di production
            ]);
        }
    }
}