<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sistem Produksi</title>
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
            background-color: #f8fafc;
            color: #333;
            line-height: 1.6;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(249, 115, 22, 0.3);
        }
        
        .dashboard-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .dashboard-header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .menu-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }
        
        .menu-section h3 {
            color: #374151;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f97316;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .menu-item {
            display: block;
            padding: 12px 16px;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            border: 1px solid #fdba74;
            border-radius: 8px;
            color: #374151;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-weight: 500;
        }
        
        .menu-item:hover {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 16px;
        }
        
        .content-area {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
            min-height: 400px;
            display: none;
        }
        
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
            font-size: 16px;
            color: #6b7280;
        }
        
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f4f6;
            border-radius: 50%;
            border-top-color: #f97316;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 10px;
            }
            
            .menu-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header {
                padding: 20px;
            }
            
            .dashboard-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1><i class="fas fa-industry"></i> Dashboard Sistem Produksi</h1>
            <p>Kelola semua aspek produksi dan inventori dengan mudah</p>
        </div>
        
        <div class="menu-grid">
            <div class="menu-section">
                <h3><i class="fas fa-cogs"></i> Operasional Produksi</h3>
                <a href="#" class="menu-item" id="menuPengemasan">
                    <i class="fas fa-box"></i> Input Pengemasan
                </a>
                <a href="#" class="menu-item" id="menuPengadaanProduk">
                    <i class="fas fa-truck-loading"></i> Pengadaan Produk
                </a>
                <a href="#" class="menu-item" id="menuPenjualanProduk">
                    <i class="fas fa-shopping-cart"></i> Penjualan Produk
                </a>
                <a href="#" class="menu-item" id="menuOperpackKerusakanProduk">
                    <i class="fas fa-exclamation-triangle"></i> Operpack Kerusakan
                </a>
                <a href="#" class="menu-item" id="menuOperstockProduk">
                    <i class="fas fa-warehouse"></i> Operstock Produk
                </a>
            </div>
            
            <div class="menu-section">
                <h3><i class="fas fa-tools"></i> Manajemen Stok</h3>
                <a href="#" class="menu-item" id="menuSeleksiProduk">
                    <i class="fas fa-filter"></i> Seleksi Produk
                </a>
                <a href="#" class="menu-item" id="menuKemasUlangProduk">
                    <i class="fas fa-redo"></i> Kemas Ulang
                </a>
                <a href="#" class="menu-item" id="menuStokOpname">
                    <i class="fas fa-clipboard-check"></i> Stok Opname
                </a>
                <a href="#" class="menu-item" id="menuLihatStok">
                    <i class="fas fa-eye"></i> Lihat Stok
                </a>
                <a href="#" class="menu-item" id="menuFisikHarian">
                    <i class="fas fa-calendar-day"></i> Fisik Harian
                </a>
            </div>
            
            <div class="menu-section">
                <h3><i class="fas fa-chart-bar"></i> Laporan</h3>
                <a href="#" class="menu-item" id="menuLaporanProduk">
                    <i class="fas fa-file-alt"></i> Laporan Produk Harian
                </a>
                <a href="#" class="menu-item" id="menuLaporanKartuStok">
                    <i class="fas fa-id-card"></i> Laporan Kartu Stok
                </a>
                <a href="#" class="menu-item" id="menuLaporanMutasiProduk">
                    <i class="fas fa-exchange-alt"></i> Laporan Mutasi Produk
                </a>
                <a href="#" class="menu-item" id="menuLaporanOverpack">
                    <i class="fas fa-boxes"></i> Laporan Overpack
                </a>
                <a href="#" class="menu-item" id="menuLaporanPerbandingan">
                    <i class="fas fa-balance-scale"></i> Laporan Perbandingan
                </a>
            </div>
            
            <div class="menu-section">
                <h3><i class="fas fa-history"></i> Riwayat Transaksi</h3>
                <a href="#" class="menu-item" id="menuRiwayatPengemasan">
                    <i class="fas fa-box-open"></i> Riwayat Pengemasan
                </a>
                <a href="#" class="menu-item" id="menuRiwayatPenjualan">
                    <i class="fas fa-shopping-bag"></i> Riwayat Penjualan
                </a>
                <a href="#" class="menu-item" id="menuRiwayatOperStok">
                    <i class="fas fa-warehouse"></i> Riwayat Oper Stok
                </a>
                <a href="#" class="menu-item" id="menuRiwayatOperPack">
                    <i class="fas fa-dolly"></i> Riwayat Oper Pack
                </a>
                <a href="#" class="menu-item" id="menuRiwayatSeleksi">
                    <i class="fas fa-search"></i> Riwayat Seleksi
                </a>
                <a href="#" class="menu-item" id="menuRiwayatKemasUlang">
                    <i class="fas fa-recycle"></i> Riwayat Kemas Ulang
                </a>
            </div>
        </div>
        
        <div id="boxProduk" class="content-area">
            <div class="loading">
                <span class="spinner"></span>
                Memuat konten...
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Get base URL function
            function getBaseUrl() {
                return window.location.origin + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1);
            }

            // Menu click handlers - Updated to match your new routes
            $(document).on('click', '#menuPengemasan', function () {
                loadContent(getBaseUrl() + 'pengemasan');
            });

            $(document).on('click', '#menuPengadaanProduk', function () {
                loadContent(getBaseUrl() + 'pengadaan');
            });

            $(document).on('click', '#menuPenjualanProduk', function () {
                loadContent(getBaseUrl() + 'penjualan');
            });

            $(document).on('click', '#menuLaporanProduk', function () {
                loadContent(getBaseUrl() + 'laporan');
            });

            $(document).on('click', '#menuOperpackKerusakanProduk', function () {
                loadContent(getBaseUrl() + 'operpack-kerusakan');
            });

            $(document).on('click', '#menuOperstockProduk', function () {
                loadContent(getBaseUrl() + 'operstock');
            });

            $(document).on('click', '#menuSeleksiProduk', function () {
                loadContent(getBaseUrl() + 'seleksi');
            });

            $(document).on('click', '#menuKemasUlangProduk', function () {
                loadContent(getBaseUrl() + 'kemas-ulang');
            });

            $(document).on('click', '#menuStokOpname', function () {
                loadContent(getBaseUrl() + 'stok-opname');
            });

            $(document).on('click', '#menuLaporanKartuStok', function () {
                loadContent(getBaseUrl() + 'laporan/kartu-stok');
            });

            $(document).on('click', '#menuLaporanMutasiProduk', function () {
                loadContent(getBaseUrl() + 'laporan/mutasi');
            });

            $(document).on('click', '#menuLihatStok', function () {
                loadContent(getBaseUrl() + 'laporan/lihat-stok');
            });

            $(document).on('click', '#menuLaporanOverpack', function () {
                loadContent(getBaseUrl() + 'laporan/overpack');
            });

            $(document).on('click', '#menuFisikHarian', function () {
                loadContent(getBaseUrl() + 'fisik-harian');
            });

            $(document).on('click', '#menuLaporanPerbandingan', function () {
                loadContent(getBaseUrl() + 'laporan/perbandingan');
            });

            // History menu handlers
            $(document).on('click', '#menuRiwayatPengemasan', function () {
                loadContent(getBaseUrl() + 'riwayat-pengemasan');
            });

            $(document).on('click', '#menuRiwayatPenjualan', function () {
                loadContent(getBaseUrl() + 'riwayat-penjualan');
            });

            $(document).on('click', '#menuRiwayatOperStok', function () {
                loadContent(getBaseUrl() + 'riwayat-operstok');
            });

            $(document).on('click', '#menuRiwayatOperPack', function () {
                loadContent(getBaseUrl() + 'riwayat-operpack');
            });

            $(document).on('click', '#menuRiwayatSeleksi', function () {
                loadContent(getBaseUrl() + 'riwayat-seleksi');
            });

            $(document).on('click', '#menuRiwayatKemasUlang', function () {
                loadContent(getBaseUrl() + 'riwayat-kemas-ulang');
            });

            function loadContent(url) {
                $('#boxProduk').show().html('<div class="loading"><span class="spinner"></span>Memuat konten...</div>');
                
                $.get(url)
                    .done(function(data) {
                        $('#boxProduk').html(data);
                    })
                    .fail(function() {
                        $('#boxProduk').html('<div style="padding: 40px; text-align: center; color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Gagal memuat konten. Silakan coba lagi.</div>');
                    });
            }
        });
    </script>
</body>
</html>