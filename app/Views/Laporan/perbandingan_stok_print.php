<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Laporan Perbandingan Stok</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .filter-info {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .stats-row {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stat-item {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            margin: 0 5px;
        }
        
        .stat-number {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
            font-size: 10px;
        }
        
        th {
            background: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .text-left {
            text-align: left !important;
        }
        
        .selisih-plus { color: #059669; font-weight: bold; }
        .selisih-minus { color: #dc2626; font-weight: bold; }
        .selisih-zero { color: #6b7280; }
        
        .row-selisih {
            background: #fef3c7 !important;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PERBANDINGAN STOK</h1>
        <p>Perbandingan Stok Fisik vs Pembukuan</p>
        <p>Tanggal: <?= esc($filters['selected_date_formatted']); ?></p>
    </div>

    <div class="filter-info">
        <strong>Filter Laporan:</strong><br>
        Gudang: <?= esc($filters['selected_gudang_name']); ?> | 
        Produk: <?= esc($filters['selected_produk_name']); ?> | 
        Tanggal: <?= esc($filters['selected_date_formatted']); ?>
    </div>

    <div class="stats-row">
        <div class="stat-item">
            <div class="stat-number"><?= number_format($stats['total_records']); ?></div>
            <div class="stat-label">Total Records</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?= number_format($stats['records_with_difference']); ?></div>
            <div class="stat-label">Ada Selisih</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?= number_format($stats['total_selisih_dus']); ?></div>
            <div class="stat-label">Total Selisih Dus</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?= number_format($stats['total_selisih_satuan']); ?></div>
            <div class="stat-label">Total Selisih Satuan</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2" class="text-left">PRODUK</th>
                <th rowspan="2" class="text-left">GUDANG</th>
                <th colspan="2">STOK SISTEM</th>
                <th colspan="2">STOK FISIK</th>
                <th colspan="2">SELISIH</th>
            </tr>
            <tr>
                <th>Dus</th><th>Pcs</th>
                <th>Dus</th><th>Pcs</th>
                <th>Dus</th><th>Pcs</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($report_data)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">
                        Tidak ada data untuk ditampilkan
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($report_data as $index => $row): ?>
                    <?php 
                    $selisihClass = ($row['selisih_dus'] != 0 || $row['selisih_satuan'] != 0) ? 'row-selisih' : '';
                    
                    function format_selisih_print($selisih) {
                        if ($selisih > 0) return "<span class='selisih-plus'>+" . number_format($selisih) . "</span>";
                        if ($selisih < 0) return "<span class='selisih-minus'>" . number_format($selisih) . "</span>";
                        return "<span class='selisih-zero'>0</span>";
                    }
                    ?>
                    <tr class="<?= $selisihClass; ?>">
                        <td><?= $index + 1; ?></td>
                        <td class="text-left"><?= esc($row['nama_produk']); ?></td>
                        <td class="text-left"><?= esc($row['nama_gudang']); ?></td>
                        <td><?= number_format($row['sistem_dus']); ?></td>
                        <td><?= number_format($row['sistem_satuan']); ?></td>
                        <td><?= number_format($row['fisik_dus']); ?></td>
                        <td><?= number_format($row['fisik_satuan']); ?></td>
                        <td><?= format_selisih_print($row['selisih_dus']); ?></td>
                        <td><?= format_selisih_print($row['selisih_satuan']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?= date('d F Y H:i:s'); ?></p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
