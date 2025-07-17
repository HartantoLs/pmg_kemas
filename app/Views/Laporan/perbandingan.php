<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?= $title ?> - Sistem Packaging</title>
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
           font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
           background: linear-gradient(135deg, var(--light-orange) 0%, #ffffff 50%, var(--light-orange) 100%);
           color: var(--text-dark);
           line-height: 1.6;
       }

       .container {
           max-width: 1200px;
           margin: 0 auto;
           padding: 1.5rem;
       }

       .header {
           text-align: center;
           margin-bottom: 2rem;
           padding: 1.5rem;
           background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
           border-radius: 15px;
           color: white;
           box-shadow: var(--shadow-lg);
       }

       .header h1 {
           font-size: 2rem;
           margin-bottom: 0.75rem;
           font-weight: 700;
       }

       .header .subtitle {
           font-size: 1rem;
           opacity: 0.9;
       }

       .filter-card {
           background: white;
           border-radius: 15px;
           padding: 1.5rem;
           margin-bottom: 2rem;
           box-shadow: var(--shadow-md);
           border: 1px solid var(--border-light);
       }

       .filter-header {
           display: flex;
           align-items: center;
           margin-bottom: 1.5rem;
           color: var(--primary-orange);
           font-size: 1.2rem;
           font-weight: 700;
       }

       .filter-header i {
           margin-right: 0.75rem;
           font-size: 1.3rem;
       }

       .filter-grid {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
           gap: 1.25rem;
           align-items: end;
       }

       .filter-group {
           display: flex;
           flex-direction: column;
       }

       .filter-group label {
           font-weight: 600;
           margin-bottom: 0.5rem;
           color: #555;
           font-size: 0.9rem;
       }

       .filter-group input,
       .filter-group select {
           padding: 0.75rem;
           border: 2px solid var(--border-light);
           border-radius: 10px;
           font-size: 0.85rem;
           transition: all 0.3s ease;
           background: white;
           box-shadow: 0 2px 8px rgba(0,0,0,0.05);
       }

       .filter-group input:focus,
       .filter-group select:focus {
           outline: none;
           border-color: var(--primary-orange);
           box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
           transform: translateY(-1px);
       }

       .btn {
           padding: 0.75rem 1.25rem;
           border: none;
           border-radius: 10px;
           font-size: 0.85rem;
           font-weight: 600;
           cursor: pointer;
           transition: all 0.3s ease;
           text-decoration: none;
           display: inline-flex;
           align-items: center;
           gap: 0.5rem;
           box-shadow: var(--shadow-sm);
       }

       .btn-primary {
           background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
           color: white;
       }

       .btn-secondary {
           background: white;
           color: var(--primary-orange);
           border: 2px solid var(--primary-orange);
       }

       .btn:hover {
           transform: translateY(-2px);
           box-shadow: var(--shadow-md);
       }

       .summary-cards {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
           gap: 1rem;
           margin-bottom: 2rem;
       }

       .summary-card {
           background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
           color: white;
           border-radius: 12px;
           padding: 1.5rem;
           text-align: center;
           box-shadow: var(--shadow-md);
           transition: transform 0.3s ease;
       }

       .summary-card:hover {
           transform: translateY(-3px);
       }

       .summary-card .icon {
           font-size: 2rem;
           margin-bottom: 0.75rem;
           opacity: 0.9;
       }

       .summary-card .value {
           font-size: 1.5rem;
           font-weight: 700;
           margin-bottom: 0.25rem;
       }

       .summary-card .label {
           font-size: 0.9rem;
           opacity: 0.9;
       }

       .action-buttons {
           display: flex;
           justify-content: flex-end;
           gap: 1rem;
           margin-bottom: 1.5rem;
       }

       .report-table-container {
           background: white;
           border-radius: 15px;
           overflow: hidden;
           box-shadow: var(--shadow-md);
           margin-bottom: 2rem;
       }

       .table-wrapper {
           overflow-x: auto;
           max-height: 60vh;
       }

       .report-table {
           width: 100%;
           border-collapse: collapse;
           font-size: 0.85rem;
           min-width: 800px;
       }

       .report-table th,
       .report-table td {
           padding: 0.75rem 0.5rem;
           text-align: center;
           border: 1px solid var(--border-light);
       }

       .report-table thead th {
           background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
           color: white;
           font-weight: 700;
           position: sticky;
           top: 0;
           z-index: 10;
           text-transform: uppercase;
           letter-spacing: 0.5px;
           font-size: 0.8rem;
       }

       .report-table .text-left {
           text-align: left;
       }

       .report-table tbody tr {
           transition: all 0.3s ease;
       }

       .report-table tbody tr:nth-child(even) {
           background-color: #f8fafc;
       }

       .report-table tbody tr:hover {
           background: linear-gradient(135deg, var(--light-orange) 0%, #ffe8d6 100%);
           transform: scale(1.005);
           box-shadow: var(--shadow-sm);
       }

       .selisih-positive {
           color: var(--success-green);
           font-weight: 700;
           background: var(--light-green);
           padding: 0.25rem 0.5rem;
           border-radius: 6px;
       }

       .selisih-negative {
           color: var(--error-red);
           font-weight: 700;
           background: var(--light-red);
           padding: 0.25rem 0.5rem;
           border-radius: 6px;
       }

       .selisih-zero {
           color: var(--text-gray);
           font-weight: 600;
       }

       .info-alert {
           background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
           border: 2px solid #2196f3;
           border-radius: 12px;
           padding: 1rem;
           margin-bottom: 1.5rem;
           display: flex;
           align-items: center;
           gap: 0.75rem;
       }

       .info-alert .icon {
           font-size: 1.2rem;
           color: #1976d2;
       }

       .info-alert .content {
           color: #1976d2;
           font-weight: 600;
       }

       .legend-cards {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
           gap: 1rem;
           margin-top: 1.5rem;
       }

       .legend-card {
           padding: 1rem;
           border-radius: 10px;
           text-align: center;
           font-weight: 600;
           display: flex;
           align-items: center;
           justify-content: center;
           gap: 0.5rem;
           border: 2px solid;
       }

       .legend-positive {
           background: var(--light-green);
           color: var(--success-green);
           border-color: var(--success-green);
       }

       .legend-negative {
           background: var(--light-red);
           color: var(--error-red);
           border-color: var(--error-red);
       }

       .legend-zero {
           background: #e2e8f0;
           color: var(--text-gray);
           border-color: var(--text-gray);
       }

       .empty-state {
           text-align: center;
           padding: 3rem 1rem;
           color: #a0aec0;
       }

       .empty-state i {
           font-size: 3rem;
           margin-bottom: 1rem;
           color: #e2e8f0;
       }

       .empty-state h3 {
           font-size: 1.25rem;
           margin-bottom: 0.5rem;
           color: #4a5568;
       }

       .empty-state p {
           font-size: 0.9rem;
           line-height: 1.5;
       }

       @media (max-width: 768px) {
           .container {
               padding: 1rem;
           }

           .filter-grid {
               grid-template-columns: 1fr;
           }

           .summary-cards {
               grid-template-columns: repeat(2, 1fr);
           }

           .action-buttons {
               flex-direction: column;
           }

           .header h1 {
               font-size: 1.5rem;
           }

           .legend-cards {
               grid-template-columns: 1fr;
           }
       }
   </style>
</head>
<body>
   <div class="container">
       <!-- Header -->
       <div class="header">
           <h1><i class="fas fa-balance-scale"></i> Laporan Perbandingan Stok</h1>
           <div class="subtitle">
               Perbandingan Stok Sistem vs Fisik untuk Analisis Selisih
           </div>
       </div>

       <!-- Filter Card -->
       <div class="filter-card">
           <div class="filter-header">
               <i class="fas fa-filter"></i>
               Filter Laporan
           </div>
           <form action="<?= base_url('laporan/perbandingan') ?>" method="GET" id="filterForm">
               <div class="filter-grid">
                   <div class="filter-group">
                       <label for="tanggal">Tanggal Cek</label>
                       <input type="date" class="form-filter" id="tanggal" name="tanggal" value="<?= esc($selected_date) ?>" required>
                   </div>

                   <div class="filter-group">
                       <label for="id_gudang">Gudang</label>
                       <select id="id_gudang" name="id_gudang" class="form-filter">
                           <option value="semua" <?= ($filter_gudang == 'semua') ? 'selected' : '' ?>>-- Semua Gudang --</option>
                           <?php foreach ($gudang_list as $gudang): ?>
                               <option value="<?= $gudang['id_gudang'] ?>" <?= ($filter_gudang == $gudang['id_gudang']) ? 'selected' : '' ?>>
                                   <?= esc($gudang['nama_gudang']) ?>
                               </option>
                           <?php endforeach; ?>
                       </select>
                   </div>

                   <div class="filter-group">
                       <label for="produk_id">Produk</label>
                       <select id="produk_id" name="produk_id" class="form-filter">
                           <option value="semua" <?= ($filter_produk == 'semua') ? 'selected' : '' ?>>-- Semua Produk --</option>
                           <?php foreach ($produk_list as $produk): ?>
                               <option value="<?= $produk['id_produk'] ?>" <?= ($filter_produk == $produk['id_produk']) ? 'selected' : '' ?>>
                                   <?= esc($produk['nama_produk']) ?>
                               </option>
                           <?php endforeach; ?>
                       </select>
                   </div>

                   <div class="filter-group">
                       <button type="button" class="btn btn-primary" onclick="refreshData()">
                           <i class="fas fa-sync-alt"></i> Refresh
                       </button>
                   </div>
               </div>
           </form>
       </div>

       <?php if (!empty($report_data)): ?>
           <!-- Summary Cards -->
           <div class="summary-cards">
               <div class="summary-card">
                   <div class="icon"><i class="fas fa-boxes"></i></div>
                   <div class="value"><?= number_format($total_records) ?></div>
                   <div class="label">Total Produk</div>
               </div>
               <div class="summary-card">
                   <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                   <div class="value"><?= number_format($records_with_difference) ?></div>
                   <div class="label">Ada Selisih</div>
               </div>
               <div class="summary-card">
                   <div class="icon"><i class="fas fa-cube"></i></div>
                   <div class="value"><?= number_format($total_selisih_dus) ?></div>
                   <div class="label">Total Selisih Dus</div>
               </div>
               <div class="summary-card">
                   <div class="icon"><i class="fas fa-cubes"></i></div>
                   <div class="value"><?= number_format($total_selisih_satuan) ?></div>
                   <div class="label">Total Selisih Satuan</div>
               </div>
           </div>

           <!-- Info Alert -->
           <div class="info-alert">
               <div class="icon">
                   <i class="fas fa-info-circle"></i>
               </div>
               <div class="content">
                   <strong>Filter Aktif:</strong> <?= esc($selected_gudang_name) ?> | <?= esc($selected_produk_name) ?> | 
                   Tanggal: <?= date('d F Y', strtotime($selected_date)) ?>
               </div>
           </div>

           <!-- Action Buttons -->
           <div class="action-buttons">
               <button class="btn btn-secondary" onclick="exportExcel()">
                   <i class="fas fa-file-excel"></i> Export Excel
               </button>
               <button class="btn btn-primary" onclick="window.print()">
                   <i class="fas fa-print"></i> Cetak Laporan
               </button>
           </div>

           <!-- Report Table -->
           <div class="report-table-container">
               <div class="table-wrapper">
                   <table class="report-table">
                       <thead>
                           <tr>
                               <th rowspan="2">NO</th>
                               <th rowspan="2" class="text-left">NAMA PRODUK</th>
                               <th rowspan="2">GUDANG</th>
                               <th colspan="2">STOK SISTEM</th>
                               <th colspan="2">STOK FISIK</th>
                               <th colspan="2">SELISIH</th>
                           </tr>
                           <tr>
                               <th>Dus</th>
                               <th>Satuan</th>
                               <th>Dus</th>
                               <th>Satuan</th>
                               <th>Dus</th>
                               <th>Satuan</th>
                           </tr>
                       </thead>
                       <tbody>
                           <?php $no = 1; foreach ($report_data as $row): ?>
                               <tr>
                                   <td><?= $no++ ?></td>
                                   <td class="text-left" style="font-weight:600;"><?= esc($row['nama_produk']) ?></td>
                                   <td><span style="background: var(--light-orange); padding: 0.25rem 0.5rem; border-radius: 6px; font-weight: 600;"><?= esc($row['nama_gudang']) ?></span></td>
                                   <td><?= number_format($row['sistem_dus']) ?></td>
                                   <td><?= number_format($row['sistem_satuan']) ?></td>
                                   <td><?= number_format($row['fisik_dus']) ?></td>
                                   <td><?= number_format($row['fisik_satuan']) ?></td>
                                   <td>
                                       <?php if ($row['selisih_dus'] > 0): ?>
                                           <span class="selisih-positive">+<?= number_format($row['selisih_dus']) ?></span>
                                       <?php elseif ($row['selisih_dus'] < 0): ?>
                                           <span class="selisih-negative"><?= number_format($row['selisih_dus']) ?></span>
                                       <?php else: ?>
                                           <span class="selisih-zero">0</span>
                                       <?php endif; ?>
                                   </td>
                                   <td>
                                       <?php if ($row['selisih_satuan'] > 0): ?>
                                           <span class="selisih-positive">+<?= number_format($row['selisih_satuan']) ?></span>
                                       <?php elseif ($row['selisih_satuan'] < 0): ?>
                                           <span class="selisih-negative"><?= number_format($row['selisih_satuan']) ?></span>
                                       <?php else: ?>
                                           <span class="selisih-zero">0</span>
                                       <?php endif; ?>
                                   </td>
                               </tr>
                           <?php endforeach; ?>
                       </tbody>
                   </table>
               </div>
           </div>

           <!-- Legend -->
           <div class="legend-cards">
               <div class="legend-card legend-positive">
                   <i class="fas fa-plus-circle"></i>
                   <span><strong>Positif:</strong> Fisik lebih banyak dari sistem</span>
               </div>
               <div class="legend-card legend-negative">
                   <i class="fas fa-minus-circle"></i>
                   <span><strong>Negatif:</strong> Fisik kurang dari sistem</span>
               </div>
               <div class="legend-card legend-zero">
                   <i class="fas fa-equals"></i>
                   <span><strong>Nol:</strong> Fisik sama dengan sistem</span>
               </div>
           </div>
       <?php else: ?>
           <div class="report-table-container">
               <div class="empty-state">
                   <i class="fas fa-search"></i>
                   <h3>Tidak Ada Data</h3>
                   <p>Tidak ditemukan data perbandingan untuk tanggal dan filter yang dipilih.</p>
               </div>
           </div>
       <?php endif; ?>
   </div>

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script>
       $(document).ready(function() {
           $('.form-filter').on('change', function() {
               $('#filterForm').submit();
           });
       });

       function refreshData() {
           setTimeout(function() {
               $('#filterForm').submit();
           }, 500);
       }

       function exportExcel() {
           alert('Fitur export Excel akan segera tersedia!');
       }
   </script>
</body>
</html>
