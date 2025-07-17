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
           --warning-yellow: #f59e0b;
           --light-yellow: #fef3c7;
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
           min-height: 100vh;
       }

       .container {
           max-width: 1400px;
           margin: 0 auto;
           padding: 20px;
       }

       .header {
           text-align: center;
           margin-bottom: 30px;
           padding: 30px;
           background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
           border-radius: 20px;
           color: white;
           box-shadow: var(--shadow-xl);
           position: relative;
           overflow: hidden;
       }

       .header::before {
           content: '';
           position: absolute;
           top: -50%;
           left: -50%;
           width: 200%;
           height: 200%;
           background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
           animation: float 6s ease-in-out infinite;
       }

       @keyframes float {
           0%, 100% { transform: translateY(0px) rotate(0deg); }
           50% { transform: translateY(-20px) rotate(180deg); }
       }

       .header h1 {
           font-size: 2.8rem;
           margin-bottom: 15px;
           font-weight: 700;
           position: relative;
           z-index: 1;
       }

       .header .subtitle {
           font-size: 1.2rem;
           opacity: 0.9;
           position: relative;
           z-index: 1;
       }

       .filter-card {
           background: white;
           border-radius: 20px;
           padding: 30px;
           margin-bottom: 30px;
           box-shadow: var(--shadow-lg);
           border: 1px solid var(--border-light);
       }

       .filter-header {
           display: flex;
           align-items: center;
           margin-bottom: 25px;
           color: var(--primary-orange);
           font-size: 1.4rem;
           font-weight: 700;
       }

       .filter-header i {
           margin-right: 12px;
           font-size: 1.6rem;
       }

       .filter-grid {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
           gap: 25px;
           align-items: end;
       }

       .filter-group {
           display: flex;
           flex-direction: column;
       }

       .filter-group label {
           font-weight: 600;
           margin-bottom: 10px;
           color: #555;
           font-size: 0.95rem;
       }

       .filter-group input,
       .filter-group select {
           padding: 15px 18px;
           border: 2px solid var(--border-light);
           border-radius: 12px;
           font-size: 14px;
           transition: all 0.3s ease;
           background: white;
           box-shadow: 0 2px 10px rgba(0,0,0,0.05);
       }

       .filter-group input:focus,
       .filter-group select:focus {
           outline: none;
           border-color: var(--primary-orange);
           box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
           transform: translateY(-2px);
       }

       .search-box {
           position: relative;
       }

       .search-box .fas {
           position: absolute;
           left: 18px;
           top: 50%;
           transform: translateY(-50%);
           color: var(--text-gray);
           font-size: 1.1rem;
       }

       .search-box input {
           padding-left: 50px;
       }

       .btn {
           padding: 15px 30px;
           border: none;
           border-radius: 12px;
           font-size: 14px;
           font-weight: 600;
           cursor: pointer;
           transition: all 0.3s ease;
           text-decoration: none;
           display: inline-flex;
           align-items: center;
           gap: 10px;
           box-shadow: var(--shadow-md);
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
           transform: translateY(-3px);
           box-shadow: var(--shadow-lg);
       }

       .summary-cards {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
           gap: 20px;
           margin-bottom: 30px;
       }

       .summary-card {
           background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
           color: white;
           border-radius: 15px;
           padding: 25px;
           text-align: center;
           box-shadow: var(--shadow-lg);
           transition: transform 0.3s ease;
       }

       .summary-card:hover {
           transform: translateY(-5px);
       }

       .summary-card .icon {
           font-size: 2.5rem;
           margin-bottom: 15px;
           opacity: 0.9;
       }

       .summary-card .value {
           font-size: 2rem;
           font-weight: 700;
           margin-bottom: 5px;
       }

       .summary-card .label {
           font-size: 1rem;
           opacity: 0.9;
       }

       .info-alert {
           background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
           border: 2px solid #2196f3;
           border-radius: 15px;
           padding: 20px;
           margin-bottom: 25px;
           display: flex;
           align-items: center;
           gap: 15px;
       }

       .info-alert .icon {
           font-size: 1.5rem;
           color: #1976d2;
       }

       .info-alert .content {
           color: #1976d2;
           font-weight: 600;
       }

       .report-table-container {
           background: white;
           border-radius: 20px;
           overflow: hidden;
           box-shadow: var(--shadow-lg);
           margin-bottom: 30px;
       }

       .table-wrapper {
           overflow-x: auto;
       }

       .report-table {
           width: 100%;
           border-collapse: collapse;
           font-size: 14px;
           min-width: 1000px;
       }

       .report-table th,
       .report-table td {
           padding: 15px 12px;
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
           font-size: 13px;
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
           transform: scale(1.01);
           box-shadow: var(--shadow-md);
       }

       .stock-high {
           background: linear-gradient(135deg, var(--light-green) 0%, #d1fae5 100%) !important;
       }

       .stock-medium {
           background: linear-gradient(135deg, var(--light-yellow) 0%, #fef3c7 100%) !important;
       }

       .stock-low {
           background: linear-gradient(135deg, var(--light-red) 0%, #fef2f2 100%) !important;
       }

       .stock-empty {
           background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
           color: var(--text-gray);
       }

       .status-badge {
           padding: 6px 12px;
           border-radius: 20px;
           font-size: 12px;
           font-weight: 700;
           text-transform: uppercase;
           letter-spacing: 0.5px;
       }

       .status-tinggi {
           background: var(--success-green);
           color: white;
       }

       .status-sedang {
           background: var(--warning-yellow);
           color: white;
       }

       .status-rendah {
           background: var(--error-red);
           color: white;
       }

       .status-kosong {
           background: var(--text-gray);
           color: white;
       }

       .legend-cards {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
           gap: 15px;
           margin-top: 25px;
       }

       .legend-card {
           padding: 15px;
           border-radius: 12px;
           text-align: center;
           font-weight: 600;
           display: flex;
           align-items: center;
           justify-content: center;
           gap: 10px;
           border: 2px solid;
       }

       .legend-tinggi {
           background: var(--light-green);
           color: var(--success-green);
           border-color: var(--success-green);
       }

       .legend-sedang {
           background: var(--light-yellow);
           color: var(--warning-yellow);
           border-color: var(--warning-yellow);
       }

       .legend-rendah {
           background: var(--light-red);
           color: var(--error-red);
           border-color: var(--error-red);
       }

       .legend-kosong {
           background: #f1f5f9;
           color: var(--text-gray);
           border-color: var(--text-gray);
       }

       .empty-state {
           text-align: center;
           padding: 60px 20px;
           color: #a0aec0;
       }

       .empty-state i {
           font-size: 4rem;
           margin-bottom: 20px;
           color: #e2e8f0;
       }

       .empty-state h3 {
           font-size: 1.5rem;
           margin-bottom: 10px;
           color: #4a5568;
       }

       .empty-state p {
           font-size: 1rem;
           line-height: 1.6;
       }

       @media (max-width: 768px) {
           .filter-grid {
               grid-template-columns: 1fr;
           }

           .summary-cards {
               grid-template-columns: repeat(2, 1fr);
           }

           .header h1 {
               font-size: 2rem;
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
           <h1><i class="fas fa-warehouse"></i> Lihat Stok Saat Ini</h1>
           <div class="subtitle">
               Monitoring Real-time Stok Produk di Semua Gudang
           </div>
       </div>

       <!-- Filter Card -->
       <div class="filter-card">
           <div class="filter-header">
               <i class="fas fa-filter"></i>
               Filter & Pencarian
           </div>
           <form action="<?= base_url('laporan/lihat-stok') ?>" method="GET" id="filterForm">
               <div class="filter-grid">
                   <div class="filter-group">
                       <label for="id_gudang">Gudang</label>
                       <select id="id_gudang" name="id_gudang" class="form-filter">
                           <option value="semua" <?= ($filter_gudang == 'semua') ? 'selected' : '' ?>>-- Semua Gudang --</option>
                           <?php foreach ($gudang_list as $gudang): ?>
                               <option value="<?= $gudang['id_gudang'] ?>" <?= ($filter_gudang == $gudang['id_gudang']) ? 'selected' : '' ?>>
                                   <?= esc($gudang['nama_gudang']) ?> (<?= esc($gudang['tipe_gudang']) ?>)
                               </option>
                           <?php endforeach; ?>
                       </select>
                   </div>

                   <div class="filter-group">
                       <label for="search">Cari Produk</label>
                       <div class="search-box">
                           <i class="fas fa-search"></i>
                           <input type="text" class="form-filter" id="search" name="search" placeholder="Masukkan nama produk..." value="<?= esc($search_produk) ?>">
                       </div>
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
                   <div class="value"><?= number_format($total_products) ?></div>
                   <div class="label">Total Produk</div>
               </div>
               <div class="summary-card">
                   <div class="icon"><i class="fas fa-check-circle"></i></div>
                   <div class="value"><?= number_format($products_with_stock) ?></div>
                   <div class="label">Ada Stok</div>
               </div>
               <div class="summary-card">
                   <div class="icon"><i class="fas fa-cube"></i></div>
                   <div class="value"><?= number_format($total_dus) ?></div>
                   <div class="label">Total Dus</div>
               </div>
               <div class="summary-card">
                   <div class="icon"><i class="fas fa-cubes"></i></div>
                   <div class="value"><?= number_format($total_satuan) ?></div>
                   <div class="label">Total Satuan</div>
               </div>
           </div>

           <!-- Info Alert -->
           <div class="info-alert">
               <div class="icon">
                   <i class="fas fa-info-circle"></i>
               </div>
               <div class="content">
                   <strong>Filter Aktif:</strong> <?= esc($selected_gudang_name) ?>
                   <?php if (!empty($search_produk)): ?>
                       | Pencarian: "<?= esc($search_produk) ?>"
                   <?php endif; ?>
               </div>
           </div>

           <!-- Report Table -->
           <div class="report-table-container">
               <div class="table-wrapper">
                   <table class="report-table">
                       <thead>
                           <tr>
                               <th>NO</th>
                               <th class="text-left">NAMA PRODUK</th>
                               <th>ISI PER DUS</th>
                               <th>STOK DUS</th>
                               <th>STOK SATUAN</th>
                               <th>TOTAL SATUAN</th>
                               <th>STATUS</th>
                           </tr>
                       </thead>
                       <tbody>
                           <?php
                           $no = 1;
                           foreach ($report_data as $row):
                               $total_satuan_keseluruhan = ($row['final_dus'] * $row['satuan_per_dus']) + $row['final_satuan'];

                               // Determine stock status
                               $stock_class = '';
                               $stock_status = '';
                               if ($total_satuan_keseluruhan == 0) {
                                   $stock_class = 'stock-empty';
                                   $stock_status = '<span class="status-badge status-kosong">Kosong</span>';
                               } elseif ($total_satuan_keseluruhan < 100) {
                                   $stock_class = 'stock-low';
                                   $stock_status = '<span class="status-badge status-rendah">Rendah</span>';
                               } elseif ($total_satuan_keseluruhan < 500) {
                                   $stock_class = 'stock-medium';
                                   $stock_status = '<span class="status-badge status-sedang">Sedang</span>';
                               } else {
                                   $stock_class = 'stock-high';
                                   $stock_status = '<span class="status-badge status-tinggi">Tinggi</span>';
                               }
                               ?>
                               <tr class="<?= $stock_class ?>">
                                   <td><?= $no++ ?></td>
                                   <td class="text-left"><?= esc($row['nama_produk']) ?></td>
                                   <td><?= number_format($row['satuan_per_dus']) ?></td>
                                   <td><?= number_format($row['final_dus']) ?></td>
                                   <td><?= number_format($row['final_satuan']) ?></td>
                                   <td><strong><?= number_format($total_satuan_keseluruhan) ?></strong></td>
                                   <td><?= $stock_status ?></td>
                               </tr>
                           <?php endforeach; ?>
                       </tbody>
                   </table>
               </div>
           </div>

           <!-- Legend Cards -->
           <div class="legend-cards">
               <div class="legend-card legend-tinggi">
                   <i class="fas fa-check-circle"></i>
                   Tinggi (≥ 500)
               </div>
               <div class="legend-card legend-sedang">
                   <i class="fas fa-exclamation-triangle"></i>
                   Sedang (100-499)
               </div>
               <div class="legend-card legend-rendah">
                   <i class="fas fa-exclamation-circle"></i>
                   Rendah (1-99)
               </div>
               <div class="legend-card legend-kosong">
                   <i class="fas fa-times-circle"></i>
                   Kosong (0)
               </div>
           </div>

       <?php else: ?>
           <!-- Empty State -->
           <div class="empty-state">
               <i class="fas fa-search fa-4x"></i>
               <h3>Data Tidak Ditemukan</h3>
               <p>
                   <?php if (!empty($search_produk)): ?>
                       Tidak ada produk dengan nama "<?= esc($search_produk) ?>" dalam stok.
                   <?php else: ?>
                       Tidak ada data stok yang sesuai dengan filter yang dipilih.
                   <?php endif; ?>
               </p>
           </div>
       <?php endif; ?>
   </div>

   <script>
       function refreshData() {
           document.getElementById('filterForm').submit();
       }
   </script>
</body>
</html>
