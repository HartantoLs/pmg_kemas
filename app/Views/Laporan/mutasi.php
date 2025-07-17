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
           max-width: 1400px;
           margin: 0 auto;
           padding: 20px;
       }

       .header {
           text-align: center;
           margin-bottom: 30px;
           padding: 20px;
           background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
           border-radius: 15px;
           color: white;
           box-shadow: var(--shadow-xl);
       }

       .header h1 {
           font-size: 2.5rem;
           margin-bottom: 10px;
           font-weight: 700;
       }

       .header .subtitle {
           font-size: 1.1rem;
           opacity: 0.9;
       }

       .filter-card {
           background: white;
           border-radius: 15px;
           padding: 25px;
           margin-bottom: 30px;
           box-shadow: var(--shadow-lg);
           border: 1px solid var(--border-light);
       }

       .filter-header {
           display: flex;
           align-items: center;
           margin-bottom: 20px;
           color: var(--primary-orange);
           font-size: 1.3rem;
           font-weight: 600;
       }

       .filter-header i {
           margin-right: 10px;
           font-size: 1.5rem;
       }

       .filter-grid {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
           gap: 20px;
           align-items: end;
       }

       .filter-group {
           display: flex;
           flex-direction: column;
       }

       .filter-group label {
           font-weight: 600;
           margin-bottom: 8px;
           color: #555;
           font-size: 0.9rem;
       }

       .filter-group input,
       .filter-group select {
           padding: 12px 15px;
           border: 2px solid var(--border-light);
           border-radius: 10px;
           font-size: 14px;
           transition: all 0.3s ease;
           background: white;
       }

       .filter-group input:focus,
       .filter-group select:focus {
           outline: none;
           border-color: var(--primary-orange);
           box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
       }

       .summary-cards {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
           gap: 20px;
           margin-bottom: 30px;
       }

       .summary-card {
           background: white;
           border-radius: 15px;
           padding: 25px;
           box-shadow: var(--shadow-lg);
           border-left: 5px solid;
           transition: transform 0.3s ease;
       }

       .summary-card:hover {
           transform: translateY(-5px);
       }

       .summary-card.saldo-awal { border-left-color: #3498db; }
       .summary-card.penerimaan { border-left-color: var(--success-green); }
       .summary-card.pengeluaran { border-left-color: var(--error-red); }
       .summary-card.saldo-akhir { border-left-color: var(--primary-orange); }

       .summary-card .icon {
           font-size: 2rem;
           margin-bottom: 10px;
       }

       .summary-card.saldo-awal .icon { color: #3498db; }
       .summary-card.penerimaan .icon { color: var(--success-green); }
       .summary-card.pengeluaran .icon { color: var(--error-red); }
       .summary-card.saldo-akhir .icon { color: var(--primary-orange); }

       .summary-card .label {
           font-size: 0.9rem;
           color: #666;
           margin-bottom: 5px;
       }

       .summary-card .value {
           font-size: 1.3rem;
           font-weight: 700;
           color: #333;
       }

       .action-buttons {
           display: flex;
           justify-content: flex-end;
           gap: 15px;
           margin-bottom: 20px;
       }

       .btn {
           padding: 12px 25px;
           border: none;
           border-radius: 10px;
           font-size: 14px;
           font-weight: 600;
           cursor: pointer;
           transition: all 0.3s ease;
           text-decoration: none;
           display: inline-flex;
           align-items: center;
           gap: 8px;
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
           box-shadow: var(--shadow-lg);
       }

       .report-table-container {
           background: white;
           border-radius: 15px;
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
           font-size: 13px;
           min-width: 1200px;
       }

       .report-table th,
       .report-table td {
           padding: 12px 8px;
           text-align: center;
           border: 1px solid var(--border-light);
       }

       .report-table thead th {
           background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
           font-weight: 700;
           color: #495057;
           position: sticky;
           top: 0;
           z-index: 10;
       }

       .report-table .text-left {
           text-align: left;
       }

       .report-table .group-header-penerimaan {
           background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
           color: #155724;
       }

       .report-table .group-header-pengeluaran {
           background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
           color: #721c24;
       }

       .report-table .subheader-penerimaan {
           background: #e8f5e8;
           color: #2d5a2d;
           font-size: 11px;
       }

       .report-table .subheader-pengeluaran {
           background: #fde8e8;
           color: #5a2d2d;
           font-size: 11px;
       }

       .report-table tbody tr:nth-child(even) {
           background-color: #f8f9fa;
       }

       .report-table tbody tr:hover {
           background-color: #fff3e0;
           transform: scale(1.01);
           transition: all 0.2s ease;
       }

       .report-table .total-row {
           background: linear-gradient(135deg, #fff3e0 0%, #ffe0b3 100%);
           font-weight: 700;
           border-top: 3px solid var(--primary-orange);
           color: #d84315;
       }

       .info-box {
           background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
           border: 1px solid #2196f3;
           border-radius: 15px;
           padding: 20px;
           margin-bottom: 20px;
           display: flex;
           align-items: flex-start;
           gap: 15px;
       }

       .info-box .icon {
           font-size: 1.5rem;
           color: #1976d2;
           margin-top: 2px;
       }

       .info-box .content h4 {
           color: #1565c0;
           margin-bottom: 5px;
           font-size: 1.1rem;
       }

       .info-box .content p {
           color: #1976d2;
           font-size: 0.9rem;
           line-height: 1.5;
       }

       @media (max-width: 768px) {
           .filter-grid {
               grid-template-columns: 1fr;
           }

           .summary-cards {
               grid-template-columns: 1fr;
           }

           .action-buttons {
               flex-direction: column;
           }

           .header h1 {
               font-size: 1.8rem;
           }
       }
   </style>
</head>
<body>
   <div class="container">
       <!-- Header -->
       <div class="header">
           <h1><i class="fas fa-chart-line"></i> Laporan Mutasi Stok Per Produk</h1>
           <div class="subtitle">
               <?php 
               if ($tipe_laporan === 'harian') {
                   echo "Tanggal: " . date('d F Y', strtotime($tgl_laporan));
               } else {
                   echo "Periode: " . date('d F Y', strtotime($tgl_mulai)) . " s/d " . date('d F Y', strtotime($tgl_akhir));
               }

               if ($filter_gudang !== 'semua') {
                   $gudang_name = '';
                   foreach ($gudang_list as $gudang) {
                       if ($gudang['id_gudang'] == $filter_gudang) {
                           $gudang_name = $gudang['nama_gudang'];
                           break;
                       }
                   }
                   echo " | Gudang: " . $gudang_name;
               } else {
                   echo " | Semua Gudang";
               }

               if ($filter_produk !== 'semua') {
                   $produk_name = '';
                   foreach ($produk_list as $produk) {
                       if ($produk['id_produk'] == $filter_produk) {
                           $produk_name = $produk['nama_produk'];
                           break;
                       }
                   }
                   echo " | Produk: " . $produk_name;
               }
               ?>
           </div>
       </div>

       <!-- Filter Card -->
       <div class="filter-card">
           <div class="filter-header">
               <i class="fas fa-filter"></i>
               Filter Laporan
           </div>
           <form action="<?= base_url('laporan/mutasi') ?>" method="GET" id="filterForm">
               <div class="filter-grid">
                   <div class="filter-group">
                       <label for="tipe_laporan">Tipe Laporan</label>
                       <select id="tipe_laporan" name="tipe_laporan" class="form-filter">
                           <option value="harian" <?= ($tipe_laporan == 'harian') ? 'selected' : '' ?>>Harian</option>
                           <option value="rekap" <?= ($tipe_laporan == 'rekap') ? 'selected' : '' ?>>Rekapitulasi</option>
                       </select>
                   </div>

                   <div class="filter-group" id="filter-harian">
                       <label for="tanggal">Tanggal Laporan</label>
                       <input type="date" class="form-filter" id="tanggal" name="tanggal" value="<?= esc($tgl_laporan) ?>">
                   </div>

                   <div class="filter-group" id="filter-rekap-mulai" style="display:none;">
                       <label for="tanggal_mulai">Dari Tanggal</label>
                       <input type="date" class="form-filter" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($tgl_mulai) ?>">
                   </div>

                   <div class="filter-group" id="filter-rekap-akhir" style="display:none;">
                       <label for="tanggal_akhir">Sampai Tanggal</label>
                       <input type="date" class="form-filter" id="tanggal_akhir" name="tanggal_akhir" value="<?= esc($tgl_akhir) ?>">
                   </div>

                   <div class="filter-group">
                       <label for="gudang_id">Gudang</label>
                       <select id="gudang_id" name="gudang_id" class="form-filter">
                           <option value="semua">-- Semua Gudang --</option>
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

       <!-- Summary Cards -->
       <div class="summary-cards">
           <div class="summary-card saldo-awal">
               <div class="icon"><i class="fas fa-box"></i></div>
               <div class="label">Saldo Awal</div>
               <div class="value"><?= number_format($totals['saldo_awal_dus'] ?? 0) ?> Dus, <?= number_format($totals['saldo_awal_satuan'] ?? 0) ?> Pcs</div>
           </div>
           <div class="summary-card penerimaan">
               <div class="icon"><i class="fas fa-arrow-up"></i></div>
               <div class="label">Total Penerimaan</div>
               <div class="value"><?= number_format($totals['penerimaan_dus'] ?? 0) ?> Dus, <?= number_format($totals['penerimaan_satuan'] ?? 0) ?> Pcs</div>
           </div>
           <div class="summary-card pengeluaran">
               <div class="icon"><i class="fas fa-arrow-down"></i></div>
               <div class="label">Total Pengeluaran</div>
               <div class="value"><?= number_format($totals['pengeluaran_dus'] ?? 0) ?> Dus, <?= number_format($totals['pengeluaran_satuan'] ?? 0) ?> Pcs</div>
           </div>
           <div class="summary-card saldo-akhir">
               <div class="icon"><i class="fas fa-chart-bar"></i></div>
               <div class="label">Saldo Akhir</div>
               <div class="value"><?= number_format($totals['saldo_akhir_dus'] ?? 0) ?> Dus, <?= number_format($totals['saldo_akhir_satuan'] ?? 0) ?> Pcs</div>
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
                           <th rowspan="2">NAMA PRODUK</th>
                           <th rowspan="2">ISI</th>
                           <th rowspan="2">SALDO AWAL</th>
                           <th colspan="<?= count($warehouse_columns) + 3 ?>" class="group-header-penerimaan">PENERIMAAN</th>
                           <th colspan="<?= count($warehouse_columns) + 3 ?>" class="group-header-pengeluaran">PENGELUARAN</th>
                           <th rowspan="2">SALDO AKHIR</th>
                       </tr>
                       <tr>
                           <th class="subheader-penerimaan">Produksi</th>
                           <?php foreach ($warehouse_columns as $warehouse): ?>
                               <th class="subheader-penerimaan">OP <?= $warehouse ?></th>
                           <?php endforeach; ?>
                           <th class="subheader-penerimaan">Overpack</th>
                           <th class="subheader-penerimaan">Total</th>
                           <th class="subheader-pengeluaran">Jual</th>
                           <?php foreach ($warehouse_columns as $warehouse): ?>
                               <th class="subheader-pengeluaran">OP <?= $warehouse ?></th>
                           <?php endforeach; ?>
                           <th class="subheader-pengeluaran">Overpack</th>
                           <th class="subheader-pengeluaran">Total</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php if (empty($report_data)): ?>
                           <tr>
                               <td colspan="<?= 7 + (count($warehouse_columns) * 2) ?>" style="text-align:center;padding:40px;color:#666;">
                                   <i class="fas fa-inbox" style="font-size:3rem;margin-bottom:15px;color:#ddd;"></i><br>
                                   Tidak ada data untuk ditampilkan dengan filter yang dipilih.
                               </td>
                           </tr>
                       <?php else: ?>
                           <?php $no = 1; foreach ($report_data as $data): ?>
                               <?php
                                   $penerimaan_dus = $data['produksi_dus'] +
                                                     $data['op_masuk_p1_dus'] + $data['op_masuk_p2_dus'] + $data['op_masuk_p3_dus'] +
                                                     $data['overpack_masuk_dus'];
                                   $penerimaan_satuan = $data['produksi_satuan'] +
                                                        $data['op_masuk_p1_satuan'] + $data['op_masuk_p2_satuan'] + $data['op_masuk_p3_satuan'] +
                                                        $data['overpack_masuk_satuan'];
                                   $pengeluaran_dus = $data['jual_dus'] +
                                                      $data['op_keluar_p1_dus'] + $data['op_keluar_p2_dus'] + $data['op_keluar_p3_dus'] +
                                                      $data['overpack_keluar_dus'];
                                   $pengeluaran_satuan = $data['jual_satuan'] +
                                                         $data['op_keluar_p1_satuan'] + $data['op_keluar_p2_satuan'] + $data['op_keluar_p3_satuan'] +
                                                         $data['overpack_keluar_satuan'];
                                   $saldo_akhir_dus = $data['saldo_awal_dus'] + $penerimaan_dus - $pengeluaran_dus;
                                   $saldo_akhir_satuan = $data['saldo_awal_satuan'] + $penerimaan_satuan - $pengeluaran_satuan;
                               ?>
                               <tr>
                                   <td><?= $no++ ?></td>
                                   <td class="text-left" style="font-weight:600;"><?= esc($data['nama_produk']) ?></td>
                                   <td><?= esc($data['isi']) ?></td>
                                   <td><?= number_format($data['saldo_awal_dus']) ?> Dus, <?= number_format($data['saldo_awal_satuan']) ?> Pcs</td>
                                   <td><?= number_format($data['produksi_dus']) ?> Dus, <?= number_format($data['produksi_satuan']) ?> Pcs</td>
                                   <?php foreach ($warehouse_columns as $warehouse): ?>
                                       <?php
                                            $key = strtolower($warehouse);
                                           $dus = $data['op_masuk_' . $key . '_dus'];
                                           $satuan = $data['op_masuk_' . $key . '_satuan'];
                                       ?>
                                       <td><?= ($dus > 0 || $satuan > 0) ? number_format($dus) . ' Dus, ' . number_format($satuan) . ' Pcs' : '-' ?></td>
                                   <?php endforeach; ?>
                                   <td><?= number_format($data['overpack_masuk_dus']) ?> Dus, <?= number_format($data['overpack_masuk_satuan']) ?> Pcs</td>
                                   <td style="font-weight:700;background:#e8f5e8;"><?= number_format($penerimaan_dus) ?> Dus, <?= number_format($penerimaan_satuan) ?> Pcs</td>
                                   <td><?= number_format($data['jual_dus']) ?> Dus, <?= number_format($data['jual_satuan']) ?> Pcs</td>
                                   <?php foreach ($warehouse_columns as $warehouse): ?>
                                       <?php
                                            $key = strtolower($warehouse);
                                           $dus = $data['op_keluar_' . $key . '_dus'];
                                           $satuan = $data['op_keluar_' . $key . '_satuan'];
                                       ?>
                                       <td><?= ($dus > 0 || $satuan > 0) ? number_format($dus) . ' Dus, ' . number_format($satuan) . ' Pcs' : '-' ?></td>
                                   <?php endforeach; ?>
                                   <td><?= number_format($data['overpack_keluar_dus']) ?> Dus, <?= number_format($data['overpack_keluar_satuan']) ?> Pcs</td>
                                   <td style="font-weight:700;background:#fde8e8;"><?= number_format($pengeluaran_dus) ?> Dus, <?= number_format($pengeluaran_satuan) ?> Pcs</td>
                                   <td style="font-weight:700;"><?= number_format($saldo_akhir_dus) ?> Dus, <?= number_format($saldo_akhir_satuan) ?> Pcs</td>
                               </tr>
                           <?php endforeach; ?>

                           <!-- Total Row -->
                           <tr class="total-row">
                               <td colspan="3"><strong>TOTAL KESELURUHAN</strong></td>
                               <td><strong><?= number_format($totals['saldo_awal_dus']) ?> Dus, <?= number_format($totals['saldo_awal_satuan']) ?> Pcs</strong></td>
                               <td><strong><?= number_format($totals['produksi_dus']) ?> Dus, <?= number_format($totals['produksi_satuan']) ?> Pcs</strong></td>
                               <?php foreach ($warehouse_columns as $warehouse): ?>
                                   <td><strong>-</strong></td>
                               <?php endforeach; ?>
                               <td><strong>-</strong></td>
                               <td><strong><?= number_format($totals['penerimaan_dus']) ?> Dus, <?= number_format($totals['penerimaan_satuan']) ?> Pcs</strong></td>
                               <td><strong>-</strong></td>
                               <?php foreach ($warehouse_columns as $warehouse): ?>
                                   <td><strong>-</strong></td>
                               <?php endforeach; ?>
                               <td><strong>-</strong></td>
                               <td><strong><?= number_format($totals['pengeluaran_dus']) ?> Dus, <?= number_format($totals['pengeluaran_satuan']) ?> Pcs</strong></td>
                               <td><strong><?= number_format($totals['saldo_akhir_dus']) ?> Dus, <?= number_format($totals['saldo_akhir_satuan']) ?> Pcs</strong></td>
                           </tr>
                       <?php endif; ?>
                   </tbody>
               </table>
           </div>
       </div>

       <!-- Info Box -->
       <?php if (!empty($report_data)): ?>
       <div class="info-box">
           <div class="icon">
               <i class="fas fa-info-circle"></i>
           </div>
           <div class="content">
               <h4>📊 Ringkasan Laporan</h4>
               <p>
                   Menampilkan <?= count($report_data) ?> produk dengan total agregasi dari
                   <?= ($filter_gudang === 'semua') ? 'semua gudang' : '1 gudang yang dipilih' ?>.
                   <?php if ($filter_gudang !== 'semua'): ?>
                       Kolom OP menunjukkan mutasi antar gudang secara detail untuk memberikan visibilitas yang lebih baik terhadap pergerakan stok.
                   <?php endif; ?>
               </p>
           </div>
       </div>
       <?php endif; ?>
   </div>

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script>
       $(document).ready(function() {
           function toggleDateFilters() {
               const type = $('#tipe_laporan').val();
               if (type === 'harian') {
                   $('#filter-harian').show();
                   $('#filter-rekap-mulai, #filter-rekap-akhir').hide();
               } else {
                   $('#filter-harian').hide();
                   $('#filter-rekap-mulai, #filter-rekap-akhir').show();
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
