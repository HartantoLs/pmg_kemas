<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?= $title ?> - Sistem Packaging</title>
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <style>
       :root {
           --primary-orange: #ff6b35;
           --secondary-orange: #f7931e;
           --light-orange: #fff5f0;
           --success-green: #10b981;
           --light-green: #d1fae5;
           --error-red: #ef4444;
           --light-red: #fef2f2;
           --text-dark: #1f2937;
           --text-gray: #6b7280;
           --border-light: #e5e7eb;
           --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
           --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
           --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
           --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
       }

       * {
           margin: 0;
           padding: 0;
           box-sizing: border-box;
       }

       body {
           font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
           background: linear-gradient(135deg, var(--light-orange) 0%, #ffffff 50%, var(--light-orange) 100%);
           color: var(--text-dark);
           line-height: 1.6;
       }

       .container {
           max-width: 1200px;
           margin: 0 auto;
           padding: 1.5rem;
       }

       /* Header Styles */
       .page-header {
           text-align: center;
           margin-bottom: 2rem;
           padding: 2rem;
           background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
           border-radius: 15px;
           color: white;
           box-shadow: var(--shadow-lg);
       }

       .page-header h1 {
           font-size: 2rem;
           font-weight: 700;
           margin-bottom: 0.5rem;
       }

       .page-header p {
           font-size: 1rem;
           opacity: 0.9;
       }

       /* Card Styles */
       .card {
           background: white;
           border-radius: 12px;
           box-shadow: var(--shadow-md);
           border: 1px solid var(--border-light);
           margin-bottom: 1.5rem;
           overflow: hidden;
       }

       .card-header {
           background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
           color: white;
           padding: 1rem 1.5rem;
           display: flex;
           align-items: center;
           justify-content: space-between;
       }

       .card-header h2 {
           font-size: 1.1rem;
           font-weight: 600;
           display: flex;
           align-items: center;
           gap: 0.5rem;
       }

       .card-body {
           padding: 1.5rem;
       }

       /* Filter Styles */
       .filter-grid {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
           gap: 1rem;
           align-items: end;
       }

       .filter-group {
           display: flex;
           flex-direction: column;
       }

       .filter-label {
           display: block;
           margin-bottom: 0.5rem;
           font-weight: 600;
           color: var(--primary-orange);
           font-size: 0.85rem;
           text-transform: uppercase;
           letter-spacing: 0.05em;
       }

       .filter-input, .filter-select {
           width: 100%;
           padding: 0.75rem;
           border: 2px solid var(--border-light);
           border-radius: 8px;
           font-size: 0.9rem;
           transition: all 0.3s ease;
           background: white;
       }

       .filter-input:focus, .filter-select:focus {
           outline: none;
           border-color: var(--primary-orange);
           box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
       }

       /* Button Styles */
       .btn {
           padding: 0.75rem 1.25rem;
           border: none;
           border-radius: 8px;
           font-size: 0.9rem;
           font-weight: 600;
           cursor: pointer;
           transition: all 0.3s ease;
           display: inline-flex;
           align-items: center;
           justify-content: center;
           gap: 0.5rem;
           text-decoration: none;
       }

       .btn-export {
           background: linear-gradient(135deg, var(--success-green) 0%, #059669 100%);
           color: white;
           box-shadow: var(--shadow-sm);
       }

       .btn-export:hover {
           transform: translateY(-1px);
           box-shadow: var(--shadow-md);
       }

       /* Info Cards */
       .info-cards {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
           gap: 1rem;
           margin-bottom: 1.5rem;
       }

       .info-card {
           background: white;
           padding: 1.25rem;
           border-radius: 10px;
           border: 2px solid var(--border-light);
           text-align: center;
           transition: all 0.3s ease;
           position: relative;
           overflow: hidden;
       }

       .info-card::before {
           content: '';
           position: absolute;
           top: 0;
           left: 0;
           right: 0;
           height: 3px;
           background: linear-gradient(90deg, var(--primary-orange), var(--secondary-orange));
       }

       .info-card:hover {
           transform: translateY(-2px);
           box-shadow: var(--shadow-md);
           border-color: var(--primary-orange);
       }

       .info-card-icon {
           font-size: 1.5rem;
           color: var(--primary-orange);
           margin-bottom: 0.5rem;
       }

       .info-card-title {
           font-size: 0.8rem;
           color: var(--text-gray);
           font-weight: 600;
           text-transform: uppercase;
           letter-spacing: 0.05em;
           margin-bottom: 0.25rem;
       }

       .info-card-value {
           font-size: 1.1rem;
           font-weight: 700;
           color: var(--text-dark);
       }

       /* Table Styles */
       .table-container {
           background: white;
           border-radius: 12px;
           overflow: hidden;
           box-shadow: var(--shadow-md);
           border: 1px solid var(--border-light);
       }

       .table-wrapper {
           overflow-x: auto;
           max-height: 60vh;
       }

       .report-table {
           width: 100%;
           border-collapse: collapse;
           font-size: 0.85rem;
           background: white;
       }

       .report-table thead th {
           background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
           color: var(--text-dark);
           padding: 0.75rem 0.5rem;
           border: 1px solid var(--border-light);
           text-align: center;
           font-weight: 700;
           position: sticky;
           top: 0;
           z-index: 10;
           text-transform: uppercase;
           letter-spacing: 0.05em;
           font-size: 0.7rem;
       }

       .report-table tbody td {
           padding: 0.75rem 0.5rem;
           border: 1px solid var(--border-light);
           text-align: center;
           transition: all 0.2s ease;
       }

       .report-table .text-left {
           text-align: left;
       }

       .report-table tbody tr:hover td {
           background-color: rgba(255, 107, 53, 0.05);
       }

       .saldo-row {
           background: linear-gradient(135deg, var(--light-orange) 0%, #fef3f0 100%) !important;
           font-weight: 700;
           color: var(--primary-orange);
       }

       .saldo-row td {
           border-top: 2px solid var(--primary-orange) !important;
           border-bottom: 2px solid var(--primary-orange) !important;
       }

       .masuk {
           color: var(--success-green);
           font-weight: 600;
       }

       .keluar {
           color: var(--error-red);
           font-weight: 600;
       }

       /* Alert Styles */
       .alert {
           padding: 1rem;
           border-radius: 8px;
           margin-bottom: 1.5rem;
           font-weight: 600;
           border: 1px solid;
           display: flex;
           align-items: center;
           gap: 0.5rem;
       }

       .alert-warning {
           background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
           color: #92400e;
           border-color: #f59e0b;
       }

       /* Responsive Design */
       @media (max-width: 768px) {
           .container {
               padding: 1rem;
           }
           
           .page-header h1 {
               font-size: 1.5rem;
           }
           
           .filter-grid {
               grid-template-columns: 1fr;
           }
           
           .card-body {
               padding: 1rem;
           }
           
           .info-cards {
               grid-template-columns: repeat(2, 1fr);
           }
       }
   </style>
</head>
<body>
   <div class="container">
       <!-- Page Header -->
       <div class="page-header">
           <h1><i class="fas fa-chart-line"></i> Laporan Kartu Stok Produk</h1>
           <p>Sistem pelacakan mutasi stok produk yang komprehensif dan real-time</p>
       </div>

       <!-- Filter Card -->
       <div class="card">
           <div class="card-header">
               <h2><i class="fas fa-filter"></i> Filter Laporan</h2>
               <div style="display: flex; gap: 0.5rem;">
                   <button type="button" onclick="window.print()" class="btn btn-export">
                       <i class="fas fa-print"></i> Print
                   </button>
                   <button type="button" onclick="exportToCSV()" class="btn btn-export">
                       <i class="fas fa-download"></i> Export CSV
                   </button>
               </div>
           </div>
           <div class="card-body">
               <form action="<?= base_url('laporan/kartu-stok') ?>" method="GET" id="filterForm">
                   <div class="filter-grid">
                       <div class="filter-group">
                           <label for="produk_id" class="filter-label">
                               <i class="fas fa-box"></i> Produk <span style="color: var(--error-red);">*</span>
                           </label>
                           <select id="produk_id" name="produk_id" class="filter-select" required>
                               <option value="">-- WAJIB PILIH PRODUK --</option>
                               <?php foreach ($produk_list as $produk): ?>
                                   <option value="<?= $produk['id_produk'] ?>" <?= ($filter_produk == $produk['id_produk']) ? 'selected' : '' ?>>
                                       <?= esc($produk['nama_produk']) ?>
                                   </option>
                               <?php endforeach; ?>
                           </select>
                       </div>
                       <div class="filter-group">
                           <label for="gudang_id" class="filter-label">
                               <i class="fas fa-warehouse"></i> Gudang
                           </label>
                           <select id="gudang_id" name="gudang_id" class="filter-select">
                               <option value="semua">-- Semua Gudang --</option>
                               <?php foreach ($gudang_list as $gudang): ?>
                                   <option value="<?= $gudang['id_gudang'] ?>" <?= ($filter_gudang == $gudang['id_gudang']) ? 'selected' : '' ?>>
                                       <?= esc($gudang['nama_gudang']) ?>
                                   </option>
                               <?php endforeach; ?>
                           </select>
                       </div>
                       <div class="filter-group">
                           <label for="tanggal_mulai" class="filter-label">
                               <i class="fas fa-calendar-alt"></i> Dari Tanggal
                           </label>
                           <input type="date" class="filter-input" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($tgl_mulai) ?>">
                       </div>
                       <div class="filter-group">
                           <label for="tanggal_akhir" class="filter-label">
                               <i class="fas fa-calendar-check"></i> Sampai Tanggal
                           </label>
                           <input type="date" class="filter-input" id="tanggal_akhir" name="tanggal_akhir" value="<?= esc($tgl_akhir) ?>">
                       </div>
                   </div>
               </form>
           </div>
       </div>

       <?php if ($filter_produk): ?>
           <!-- Info Cards -->
           <div class="info-cards">
               <div class="info-card">
                   <div class="info-card-icon">
                       <i class="fas fa-box"></i>
                   </div>
                   <div class="info-card-title">Produk</div>
                   <div class="info-card-value"><?= esc($selected_produk_name) ?></div>
               </div>
               <div class="info-card">
                   <div class="info-card-icon">
                       <i class="fas fa-warehouse"></i>
                   </div>
                   <div class="info-card-title">Gudang</div>
                   <div class="info-card-value"><?= esc($selected_gudang_name) ?></div>
               </div>
               <div class="info-card">
                   <div class="info-card-icon">
                       <i class="fas fa-calendar-alt"></i>
                   </div>
                   <div class="info-card-title">Periode</div>
                   <div class="info-card-value"><?= date('d/m/Y', strtotime($tgl_mulai)) . ' - ' . date('d/m/Y', strtotime($tgl_akhir)) ?></div>
               </div>
               <div class="info-card">
                   <div class="info-card-icon">
                       <i class="fas fa-list"></i>
                   </div>
                   <div class="info-card-title">Total Transaksi</div>
                   <div class="info-card-value"><?= count($report_data) ?></div>
               </div>
           </div>
       <?php endif; ?>

       <?php if (empty($filter_produk)): ?>
           <div class="alert alert-warning">
               <i class="fas fa-exclamation-triangle"></i>
               <span>Silakan pilih produk untuk menampilkan laporan kartu stok.</span>
           </div>
       <?php endif; ?>

       <!-- Report Table -->
       <div class="table-container">
           <div class="card-header">
               <h2><i class="fas fa-table"></i> Kartu Stok - <?= $filter_produk ? esc($selected_produk_name) : 'Pilih Produk' ?></h2>
           </div>
           <div class="table-wrapper">
               <table class="report-table" id="reportTable">
                   <thead>
                       <tr>
                           <th rowspan="2">Tanggal</th>
                           <th rowspan="2">ID Gudang</th>
                           <th rowspan="2">Keterangan</th>
                           <th colspan="2">Masuk</th>
                           <th colspan="2">Keluar</th>
                           <th colspan="2">Saldo</th>
                       </tr>
                       <tr>
                           <th>Dus</th><th>Pcs</th>
                           <th>Dus</th><th>Pcs</th>
                           <th>Dus</th><th>Pcs</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php if (empty($filter_produk)): ?>
                           <tr>
                               <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-gray);">Silakan pilih produk untuk menampilkan laporan.</td>
                           </tr>
                       <?php else: ?>
                           <tr class="saldo-row">
                               <td colspan="7" class="text-left">
                                   <i class="fas fa-play-circle"></i> SALDO AWAL PER <?= strtoupper(date('d F Y', strtotime($tgl_mulai))) ?>
                               </td>
                               <td><?= number_format($saldo_awal_dus) ?></td>
                               <td><?= number_format($saldo_awal_satuan) ?></td>
                           </tr>
                           <?php
                               $running_dus = $saldo_awal_dus;
                               $running_satuan = $saldo_awal_satuan;
                               if (!empty($report_data)):
                                   foreach ($report_data as $row):
                                       $masuk_dus = ($row['perubahan_dus'] > 0) ? $row['perubahan_dus'] : 0;
                                       $masuk_satuan = ($row['perubahan_satuan'] > 0) ? $row['perubahan_satuan'] : 0;
                                       $keluar_dus = ($row['perubahan_dus'] < 0) ? abs($row['perubahan_dus']) : 0;
                                       $keluar_satuan = ($row['perubahan_satuan'] < 0) ? abs($row['perubahan_satuan']) : 0;
                                       $running_dus += $row['perubahan_dus'];
                                       $running_satuan += $row['perubahan_satuan'];
                           ?>
                               <tr>
                                   <td><?= date('d-m-Y', strtotime($row['tanggal_transaksi'])) ?></td>
                                   <td><span style="background: var(--light-orange); padding: 0.25rem 0.5rem; border-radius: 6px; font-weight: 600;"><?= esc($row['gudang_id']) ?></span></td>
                                   <td class="text-left"><?= esc($row['tipe_transaksi']) ?></td>
                                   <td class="masuk"><?= ($masuk_dus > 0) ? number_format($masuk_dus) : '-' ?></td>
                                   <td class="masuk"><?= ($masuk_satuan > 0) ? number_format($masuk_satuan) : '-' ?></td>
                                   <td class="keluar"><?= ($keluar_dus > 0) ? number_format($keluar_dus) : '-' ?></td>
                                   <td class="keluar"><?= ($keluar_satuan > 0) ? number_format($keluar_satuan) : '-' ?></td>
                                   <td><?= number_format($running_dus) ?></td>
                                   <td><?= number_format($running_satuan) ?></td>
                               </tr>
                           <?php 
                                   endforeach;
                               endif;
                           ?>
                           <tr class="saldo-row">
                               <td colspan="7" class="text-left">
                                   <i class="fas fa-stop-circle"></i> SALDO AKHIR PER <?= strtoupper(date('d F Y', strtotime($tgl_akhir))) ?>
                               </td>
                               <td><?= number_format($running_dus) ?></td>
                               <td><?= number_format($running_satuan) ?></td>
                           </tr>
                       <?php endif; ?>
                   </tbody>
               </table>
           </div>
       </div>
   </div>

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script>
       $(document).ready(function() {
           // Auto-submit form when filters change
           $('.filter-select, .filter-input').on('change', function() {
               if ($('#produk_id').val()) {
                   $('#filterForm').submit();
               }
           });

           // Export to CSV function
           window.exportToCSV = function() {
               if (!<?= $filter_produk ? 'true' : 'false' ?>) {
                   alert('Silakan pilih produk terlebih dahulu.');
                   return;
               }

               const table = document.getElementById('reportTable');
               const rows = table.querySelectorAll('tr');
               let csv = [];

               // Add header
               csv.push('Laporan Kartu Stok - <?= addslashes($selected_produk_name ?? '') ?>');
               csv.push('Periode: <?= date("d/m/Y", strtotime($tgl_mulai)) . " - " . date("d/m/Y", strtotime($tgl_akhir)) ?>');
               csv.push('Gudang: <?= addslashes($selected_gudang_name ?? '') ?>');
               csv.push('');

               // Add table data
               for (let i = 0; i < rows.length; i++) {
                   const row = rows[i];
                   const cols = row.querySelectorAll('td, th');
                   let rowData = [];

                   for (let j = 0; j < cols.length; j++) {
                       let cellData = cols[j].innerText.replace(/"/g, '""');
                       rowData.push('"' + cellData + '"');
                   }
                   csv.push(rowData.join(','));
               }

               // Download CSV
               const csvContent = csv.join('\n');
               const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
               const link = document.createElement('a');
               const url = URL.createObjectURL(blob);
               link.setAttribute('href', url);
               link.setAttribute('download', 'kartu_stok_<?= date("Y-m-d") ?>.csv');
               link.style.visibility = 'hidden';
               document.body.appendChild(link);
               link.click();
               document.body.removeChild(link);
           };
       });
   </script>
</body>
</html>
