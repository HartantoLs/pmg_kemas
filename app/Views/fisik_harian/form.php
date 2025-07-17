<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Fisik Harian</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            color: #333;
            line-height: 1.6;
        }
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .form-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid #f1f1f1;
        }
        .form-header {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }
        .form-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .form-content {
            padding: 30px;
        }
        .filter-section {
            background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%);
            border: 2px solid #06b6d4;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #ffffff;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        }
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .data-table th {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            padding: 15px 12px;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
        }
        .data-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .data-table tr:hover {
            background-color: #f3f4f6;
        }
        .produk-name {
            text-align: left !important;
            font-weight: 600;
            color: #374151;
        }
        .gudang-name {
            text-align: left !important;
            color: #059669;
            font-weight: 500;
        }
        .stock-value {
            font-weight: 600;
            color: #0891b2;
        }
        .stock-zero {
            color: #6b7280;
            font-style: italic;
        }
        .summary-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .summary-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .summary-value {
            font-size: 24px;
            font-weight: 700;
            color: #0891b2;
            margin-bottom: 5px;
        }
        .summary-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
        }
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        .no-data i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f4f6;
            border-radius: 50%;
            border-top-color: #06b6d4;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .table-container {
            max-height: 600px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .export-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }
            .form-content {
                padding: 20px;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .summary-grid {
                grid-template-columns: 1fr;
            }
            .data-table {
                font-size: 11px;
            }
            .data-table th,
            .data-table td {
                padding: 8px 6px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-card">
            <div class="form-header">
                <h1>
                    <i class="fas fa-calendar-day"></i>
                    Laporan Fisik Harian
                </h1>
            </div>
            <div class="form-content">
                <div class="filter-section">
                    <h3 style="color: #0891b2; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-filter"></i> Filter Laporan
                    </h3>
                    <form id="filterForm">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="tanggal_dari">
                                    <i class="fas fa-calendar-alt"></i>
                                    Tanggal Dari
                                </label>
                                <input type="date" name="tanggal_dari" id="tanggal_dari" 
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="tanggal_sampai">
                                    <i class="fas fa-calendar-alt"></i>
                                    Tanggal Sampai
                                </label>
                                <input type="date" name="tanggal_sampai" id="tanggal_sampai" 
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="gudang_filter">
                                    <i class="fas fa-warehouse"></i>
                                    Filter Gudang
                                </label>
                                <select name="gudang_filter" id="gudang_filter">
                                    <option value="">-- Semua Gudang --</option>
                                    <option value="Produksi">Gudang Produksi</option>
                                    <option value="Overpack">Gudang Overpack</option>
                                    <option value="Finished Goods">Finished Goods</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="produk_filter">
                                    <i class="fas fa-cube"></i>
                                    Filter Produk
                                </label>
                                <input type="text" name="produk_filter" id="produk_filter" 
                                       placeholder="Nama produk...">
                            </div>
                        </div>
                        <div style="text-align: center; margin-top: 20px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                Tampilkan Laporan
                            </button>
                            <button type="button" id="btnReset" class="btn" style="background: #6b7280; color: white; margin-left: 10px;">
                                <i class="fas fa-undo"></i>
                                Reset Filter
                            </button>
                        </div>
                    </form>
                </div>

                <div id="summarySection" class="summary-section" style="display: none;">
                    <h3 style="color: #0ea5e9; margin-bottom: 15px; text-align: center;">
                        <i class="fas fa-chart-bar"></i> Ringkasan Stok Fisik
                    </h3>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="summary-value" id="totalProduk">0</div>
                            <div class="summary-label">Total Produk</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value" id="totalGudang">0</div>
                            <div class="summary-label">Total Gudang</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value" id="totalDus">0</div>
                            <div class="summary-label">Total Dus</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value" id="totalSatuan">0</div>
                            <div class="summary-label">Total Satuan</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value" id="stokKosong">0</div>
                            <div class="summary-label">Stok Kosong</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value" id="lastUpdate">-</div>
                            <div class="summary-label">Update Terakhir</div>
                        </div>
                    </div>
                </div>

                <div id="dataSection">
                    <div class="no-data">
                        <i class="fas fa-search"></i>
                        <h3>Pilih Filter dan Klik "Tampilkan Laporan"</h3>
                        <p>Gunakan filter di atas untuk menampilkan data stok fisik harian</p>
                    </div>
                </div>

                <div id="exportSection" class="export-section" style="display: none;">
                    <button type="button" id="btnExportExcel" class="btn btn-success">
                        <i class="fas fa-file-excel"></i>
                        Export ke Excel
                    </button>
                    <button type="button" id="btnPrint" class="btn" style="background: #6366f1; color: white; margin-left: 10px;">
                        <i class="fas fa-print"></i>
                        Print Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            let currentData = [];
            
            // Sample data for demonstration
            const sampleData = [
                {
                    id_produk: 1,
                    nama_produk: 'Produk A',
                    nama_gudang: 'Gudang Produksi 1',
                    jumlah_dus: 150,
                    jumlah_satuan: 25,
                    last_updated: '2024-01-15 14:30:00'
                },
                {
                    id_produk: 1,
                    nama_produk: 'Produk A',
                    nama_gudang: 'Gudang Overpack',
                    jumlah_dus: 75,
                    jumlah_satuan: 12,
                    last_updated: '2024-01-15 15:45:00'
                },
                {
                    id_produk: 2,
                    nama_produk: 'Produk B',
                    nama_gudang: 'Gudang Produksi 2',
                    jumlah_dus: 200,
                    jumlah_satuan: 0,
                    last_updated: '2024-01-15 13:20:00'
                },
                {
                    id_produk: 3,
                    nama_produk: 'Produk C',
                    nama_gudang: 'Finished Goods',
                    jumlah_dus: 0,
                    jumlah_satuan: 0,
                    last_updated: '2024-01-15 12:00:00'
                },
                {
                    id_produk: 4,
                    nama_produk: 'Produk D',
                    nama_gudang: 'Gudang Produksi 1',
                    jumlah_dus: 300,
                    jumlah_satuan: 45,
                    last_updated: '2024-01-15 16:10:00'
                }
            ];
            
            function displayData(data) {
                currentData = data;
                
                if (data.length === 0) {
                    $('#dataSection').html(`
                        <div class="no-data">
                            <i class="fas fa-inbox"></i>
                            <h3>Tidak Ada Data</h3>
                            <p>Tidak ditemukan data stok untuk filter yang dipilih</p>
                        </div>
                    `);
                    $('#summarySection').hide();
                    $('#exportSection').hide();
                    return;
                }
                
                // Calculate summary
                const uniqueProduk = [...new Set(data.map(item => item.id_produk))].length;
                const uniqueGudang = [...new Set(data.map(item => item.nama_gudang))].length;
                const totalDus = data.reduce((sum, item) => sum + item.jumlah_dus, 0);
                const totalSatuan = data.reduce((sum, item) => sum + item.jumlah_satuan, 0);
                const stokKosong = data.filter(item => item.jumlah_dus === 0 && item.jumlah_satuan === 0).length;
                const lastUpdate = data.reduce((latest, item) => {
