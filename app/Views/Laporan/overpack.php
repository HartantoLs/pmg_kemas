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

       .action-buttons {
           display: flex;
           justify-content: flex-end;
           gap: 1rem;
           margin-bottom: 1.5rem;
       }

       .date-info {
           text-align: center;
           margin-bottom: 1.5rem;
           padding: 1rem;
           background: linear-gradient(135deg, var(--light-orange) 0%, #ffe8d6 100%);
           border-radius: 12px;
           border-left: 4px solid var(--primary-orange);
           font-size: 1rem;
           font-weight: 600;
           color: #4a5568;
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

       .report-table tfoot {
           background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
           color: white;
           font-weight: 700;
       }

       .report-table tfoot td {
           border-color: #4a5568;
           font-size: 0.9rem;
           padding: 1rem 0.5rem;
       }

       .stok-display {
           display: flex;
           flex-direction: column;
           align-items: center;
           gap: 0.25rem;
       }

       .stok-dus {
           font-weight: 700;
           color: var(--text-dark);
           font-size: 0.85rem;
       }

       .stok-pcs {
           font-size: 0.75rem;
           color: var(--text-gray);
           font-style: italic;
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

           .action-buttons {
               flex-direction: column;
           }

           .header h1 {
               font-size: 1.5rem;
           }
       }
   </style>
</head>
<body>
   <div class="container">
       <!-- Header -->
       <div class="header">
           <h1><i class="fas fa-recycle"></i> Laporan Status Stok Overpack</h1>
           <div class="subtitle">
               Monitoring dan Tracking Stok Produk Overpack
           </div>
       </div>

       <!-- Filter Card -->
       <div class="filter-card">
           <div class="filter-header">
               <i class="fas fa-filter"></i>
               Filter Laporan
           </div>
           <form action="<?= base_url('laporan/overpack') ?>" method="GET" id="filterForm">
               <div class="filter-grid">
                   <div class="filter-group">
                       <label for="tipe_laporan">Tipe Laporan</label>
                       <select id="tipe_laporan" name="tipe_laporan" class="form-filter">
                           <option value="harian" <?= ($tipe_laporan == 'harian') ? 'selected' : '' ?>>Laporan Status Harian</option>
                           <option value="rekap" <?= ($tipe_laporan == 'rekap') ? 'selected' : '' ?>>Rekapitulasi Periode</option>
                       </select>
                   </div>

                   <div class="filter-group filter-harian">
                       <label for="tanggal">Pilih Tanggal</label>
                       <input type="date" class="form-filter" id="tanggal" name="tanggal" value="<?= esc($selected_date) ?>">
                   </div>

                   <div class="filter-group filter-rekap" style="display:none;">
                       <label for="tanggal_mulai">Dari Tanggal</label>
                       <input type="date" class="form-filter" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($start_date) ?>">
                   </div>

                   <div class="filter-group filter-rekap" style="display:none;">
                       <label for="tanggal_akhir">Sampai Tanggal</label>
                       <input type="date" class="form-filter" id="tanggal_akhir" name="tanggal_akhir" value="<?= esc($end_date) ?>">
                   </div>

                   <div class="filter-group">
                       <label for="produk_id">Produk</label>
                       <select id="produk_id" name="produk_id" class="form-filter">
                           <option value="semua">-- Semua Produk --</option>
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

       <!-- Date Info -->
       <div class="date-info">
           <i class="fas fa-calendar-alt"></i>
           <?php 
           if ($tipe_laporan === 'harian') {
               echo "Status stok hingga tanggal: <strong>" . date('d F Y', strtotime($selected_date)) . "</strong>";
           } else {
               echo "Rekapitulasi periode: <strong>" . date('d F Y', strtotime($start_date)) . "</strong> s/d <strong>" . date('d F Y', strtotime($end_date)) . "</strong>";
           }

           if ($filter_produk !== 'semua') {
               $produk_name = '';
               foreach ($produk_list as $produk) {
                   if ($produk['id_produk'] == $filter_produk) {
                       $produk_name = $produk['nama_produk'];
                       break;
                   }
               }
               echo " | Produk: <strong>" . esc($produk_name) . "</strong>";
           }
           ?>
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
               <?php if ($tipe_laporan === 'harian'): ?>
                   <table class="report-table">
                       <thead>
                           <tr>
                               <th>NO</th>
                               <th class="text-left">NAMA PRODUK</th>
                               <th>ISI/DUS</th>
                               <th>BELUM DISELEKSI</th>
                               <th>SIAP DIKEMAS</th>
                               <th>SUDAH DIKEMAS</th>
                               <th>TOTAL CURAH</th>
                               <th>TOTAL OVERPACK</th>
                           </tr>
                       </thead>
                       <tbody>
                           <?php if (empty($report_data)): ?>
                               <tr>
                                   <td colspan="8">
                                       <div class="empty-state">
                                           <i class="fas fa-inbox"></i>
                                           <h3>Tidak Ada Data</h3>
                                           <p>Tidak ada data stok overpack sesuai filter yang dipilih.</p>
                                       </div>
                                   </td>
                               </tr>
                           <?php else: ?>
                               <?php $no = 1; foreach ($report_data as $row): ?>
                                   <tr>
                                       <td><?= $no++ ?></td>
                                       <td class="text-left" style="font-weight:600;"><?= esc($row['nama_produk']) ?></td>
                                       <td style="font-weight:600;"><?= number_format($row['satuan_per_dus']) ?></td>
                                       <td>
                                           <?php if ($row['belum_seleksi'] > 0): ?>
                                               <div class="stok-display">
                                                   <div class="stok-dus"><?= number_format(floor($row['belum_seleksi'] / $row['satuan_per_dus'])) ?> Dus</div>
                                                   <div class="stok-pcs"><?= number_format($row['belum_seleksi']) ?> pcs</div>
                                               </div>
                                           <?php endif; ?>
                                       </td>
                                       <td>
                                           <?php if ($row['siap_kemas'] > 0): ?>
                                               <div class="stok-display">
                                                   <div class="stok-dus"><?= number_format(floor($row['siap_kemas'] / $row['satuan_per_dus'])) ?> Dus</div>
                                                   <div class="stok-pcs"><?= number_format($row['siap_kemas']) ?> pcs</div>
                                               </div>
                                           <?php endif; ?>
                                       </td>
                                       <td>
                                           <?php if ($row['sudah_kemas'] > 0): ?>
                                               <div class="stok-display">
                                                   <div class="stok-dus"><?= number_format(floor($row['sudah_kemas'] / $row['satuan_per_dus'])) ?> Dus</div>
                                                   <div class="stok-pcs"><?= number_format($row['sudah_kemas']) ?> pcs</div>
                                               </div>
                                           <?php endif; ?>
                                       </td>
                                       <td>
                                           <?php if ($row['total_curah'] > 0): ?>
                                               <div class="stok-display">
                                                   <div class="stok-dus"><?= number_format(floor($row['total_curah'] / $row['satuan_per_dus'])) ?> Dus</div>
                                                   <div class="stok-pcs"><?= number_format($row['total_curah']) ?> pcs</div>
                                               </div>
                                           <?php endif; ?>
                                       </td>
                                       <td>
                                           <?php if ($row['total_keseluruhan'] > 0): ?>
                                               <div class="stok-display">
                                                   <div class="stok-dus" style="color:var(--primary-orange);"><?= number_format(floor($row['total_keseluruhan'] / $row['satuan_per_dus'])) ?> Dus</div>
                                                   <div class="stok-pcs"><?= number_format($row['total_keseluruhan']) ?> pcs</div>
                                               </div>
                                           <?php endif; ?>
                                       </td>
                                   </tr>
                               <?php endforeach; ?>
                           <?php endif; ?>
                       </tbody>
                       <?php if (!empty($report_data) && !empty($grand_totals)): ?>
                           <tfoot>
                               <tr>
                                   <td colspan="3" class="text-left"><strong>GRAND TOTAL</strong></td>
                                   <td><strong><?= number_format($grand_totals['belum_seleksi'] ?? 0) ?> pcs</strong></td>
                                   <td><strong><?= number_format($grand_totals['siap_kemas'] ?? 0) ?> pcs</strong></td>
                                   <td><strong><?= number_format($grand_totals['sudah_kemas'] ?? 0) ?> pcs</strong></td>
                                   <td><strong><?= number_format($grand_totals['total_curah'] ?? 0) ?> pcs</strong></td>
                                   <td><strong><?= number_format($grand_totals['total_keseluruhan'] ?? 0) ?> pcs</strong></td>
                               </tr>
                           </tfoot>
                       <?php endif; ?>
                   </table>
               <?php else: // Rekap ?>
                   <table class="report-table">
                       <thead>
                           <tr>
                               <th>NO</th>
                               <th class="text-left">NAMA PRODUK</th>
                               <th>ISI/DUS</th>
                               <th>TOTAL MASUK</th>
                               <th>SELEKSI AMAN</th>
                               <th>SELEKSI CURAH</th>
                               <th>KEMAS ULANG</th>
                           </tr>
                       </thead>
                       <tbody>
                           <?php if (empty($report_data)): ?>
                               <tr>
                                   <td colspan="7">
                                       <div class="empty-state">
                                           <i class="fas fa-chart-line"></i>
                                           <h3>Tidak Ada Pergerakan</h3>
                                           <p>Tidak ada pergerakan stok overpack pada periode ini.</p>
                                       </div>
                                   </td>
                               </tr>
                           <?php else: ?>
                               <?php $no = 1; foreach ($report_data as $row): ?>
                                   <tr>
                                       <td><?= $no++ ?></td>
                                       <td class="text-left" style="font-weight:600;"><?= esc($row['nama_produk']) ?></td>
                                       <td style="font-weight:600;"><?= number_format($row['satuan_per_dus']) ?></td>
                                       <td>
                                           <?php if ($row['total_masuk'] > 0): ?>
                                               <div class="stok-display">
                                                   <div class="stok-dus"><?= number_format(floor($row['total_masuk'] / $row['satuan_per_dus'])) ?> Dus</div>
                                                   <div class="stok-pcs"><?= number_format($row['total_masuk']) ?> pcs</div>
                                               </div>
                                           <?php endif; ?>
                                       </td>
                                       <td>
                                           <?php if ($row['total_aman'] > 0): ?>
                                               <div class="stok-display">
                                                   <div class="stok-dus"><?= number_format(floor($row['total_aman'] / $row['satuan_per_dus'])) ?> Dus</div>
                                                   <div class="stok-pcs"><?= number_format($row['total_aman']) ?> pcs</div>
                                               </div>
                                           <?php endif; ?>
                                       </td>
                                       <td>
                                           <?php if ($row['total_curah'] > 0): ?>
                                               <div class="stok-display">
                                                   <div class="stok-dus"><?= number_format(floor($row['total_curah'] / $row['satuan_per_dus'])) ?> Dus</div>
                                                   <div class="stok-pcs"><?= number_format($row['total_curah']) ?> pcs</div>
                                               </div>
                                           <?php endif; ?>
                                       </td>
                                       <td>
                                           <?php if ($row['total_kemas'] > 0): ?>
                                               <div class="stok-display">
                                                   <div class="stok-dus"><?= number_format(floor($row['total_kemas'] / $row['satuan_per_dus'])) ?> Dus</div>
                                                   <div class="stok-pcs"><?= number_format($row['total_kemas']) ?> pcs</div>
                                               </div>
                                           <?php endif; ?>
                                       </td>
                                   </tr>
                               <?php endforeach; ?>
                           <?php endif; ?>
                       </tbody>
                       <?php if (!empty($report_data) && !empty($grand_totals)): ?>
                           <tfoot>
                               <tr>
                                   <td colspan="3" class="text-left"><strong>GRAND TOTAL</strong></td>
                                   <td><strong><?= number_format($grand_totals['total_masuk'] ?? 0) ?> pcs</strong></td>
                                   <td><strong><?= number_format($grand_totals['total_aman'] ?? 0) ?> pcs</strong></td>
                                   <td><strong><?= number_format($grand_totals['total_curah'] ?? 0) ?> pcs</strong></td>
                                   <td><strong><?= number_format($grand_totals['total_kemas'] ?? 0) ?> pcs</strong></td>
                               </tr>
                           </tfoot>
                       <?php endif; ?>
                   </table>
               <?php endif; ?>
           </div>
       </div>
   </div>

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script>
       $(document).ready(function() {
           function toggleDateFilters() {
               const tipeLaporan = document.getElementById('tipe_laporan').value;
               const filterHarian = document.querySelectorAll('.filter-harian');
               const filterRekap = document.querySelectorAll('.filter-rekap');
               
               if (tipeLaporan === 'rekap') {
                   filterHarian.forEach(field => field.style.display = 'none');
                   filterRekap.forEach(field => field.style.display = 'block');
               } else {
                   filterHarian.forEach(field => field.style.display = 'block');
                   filterRekap.forEach(field => field.style.display = 'none');
               }
           }

           toggleDateFilters();
           $('#tipe_laporan').on('change', toggleDateFilters);

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
