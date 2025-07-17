<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Kemas Ulang</title>
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
            max-width: 1000px;
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
        .info-section {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #10b981;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
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
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        .stock-info {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
        }
        .stock-info h4 {
            color: #10b981;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .stock-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .stock-item:last-child {
            border-bottom: none;
        }
        .alert {
            padding: 16px 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            display: none;
        }
        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 2px solid #10b981;
        }
        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 2px solid #ef4444;
        }
        .submit-section {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #f3f4f6;
        }
        .submit-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 16px 40px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
            min-width: 250px;
        }
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
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
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-card">
            <div class="form-header">
                <h1>
                    <i class="fas fa-redo"></i>
                    Form Kemas Ulang Produk
                </h1>
            </div>
            <div class="form-content">
                <div class="info-section">
                    <h3 style="color: #059669; margin-bottom: 15px;">
                        <i class="fas fa-info-circle"></i> Informasi Kemas Ulang
                    </h3>
                    <p style="color: #374151; line-height: 1.6;">
                        Proses kemas ulang digunakan untuk mengemas kembali produk yang telah melalui proses seleksi dan dinyatakan aman. 
                        Produk yang dikemas ulang akan ditambahkan kembali ke stok gudang Overpack.
                    </p>
                </div>

                <div id="formMessage" class="alert"></div>
                <form id="formKemasUlang">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="tanggal">
                                <i class="fas fa-calendar"></i>
                                Tanggal Kemas Ulang
                            </label>
                            <input type="date" name="tanggal" id="tanggal" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="id_produk">
                                <i class="fas fa-cube"></i>
                                Produk
                            </label>
                            <select name="id_produk" id="id_produk" required>
                                <option value="">-- Pilih Produk --</option>
                                <?php foreach ($produk_list as $produk): ?>
                                    <option value="<?php echo $produk['id_produk']; ?>">
                                        <?php echo htmlspecialchars($produk['nama_produk']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="jumlah_kemas_unit" id="labelJumlahKemas">
                                <i class="fas fa-sort-numeric-up"></i>
                                Jumlah Kemas Ulang
                            </label>
                            <input type="number" name="jumlah_kemas_unit" id="jumlah_kemas_unit" 
                                   value="0" min="0" required>
                        </div>
                    </div>
                    
                    <div class="stock-info" id="stockInfo" style="display: none;">
                        <h4><i class="fas fa-warehouse"></i> Informasi Stok</h4>
                        <div id="stockDetails"></div>
                    </div>
                    
                    <div class="submit-section">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save"></i>
                            Simpan Kemas Ulang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            // Load stock info when product changes
            $('#id_produk').on('change', function() {
                const idProduk = $(this).val();
                const stockInfo = $('#stockInfo');
                const stockDetails = $('#stockDetails');
                const labelJumlahKemas = $('#labelJumlahKemas');
                
                if (idProduk) {
                    $.getJSON('<?= base_url('kemas-ulang/get-stok-repack') ?>', { 
                        id_produk: idProduk 
                    })
                    .done(function(data) {
                        if (data.max_unit > 0) {
                            let stockHtml = `
                                <div class="stock-item">
                                    <span><strong>Nama Produk:</strong></span>
                                    <span>${data.nama_produk}</span>
                                </div>
                                <div class="stock-item">
                                    <span><strong>Hasil Seleksi Aman:</strong></span>
                                    <span>${data.hasil_seleksi_aman.toLocaleString('id-ID')} pcs</span>
                                </div>
                                <div class="stock-item">
                                    <span><strong>Sudah Dikemas Ulang:</strong></span>
                                    <span>${data.hasil_kemas_ulang.toLocaleString('id-ID')} pcs</span>
                                </div>
                                <div class="stock-item">
                                    <span><strong>Siap Dikemas Ulang:</strong></span>
                                    <span style="color: #10b981; font-weight: 600;">${data.stok_aman_siap_repack_pcs.toLocaleString('id-ID')} pcs</span>
                                </div>
                                <div class="stock-item">
                                    <span><strong>Maksimal ${data.unit_label}:</strong></span>
                                    <span style="color: #059669; font-weight: 600;">${data.max_unit.toLocaleString('id-ID')} ${data.unit_label}</span>
                                </div>
                            `;
                            
                            if (data.sisa_pcs > 0) {
                                stockHtml += `
                                    <div class="stock-item">
                                        <span><strong>Sisa (tidak cukup untuk 1 ${data.unit_label}):</strong></span>
                                        <span style="color: #f59e0b;">${data.sisa_pcs} pcs</span>
                                    </div>
                                `;
                            }
                            
                            stockDetails.html(stockHtml);
                            stockInfo.show();
                            
                            // Update label
                            labelJumlahKemas.html(`
                                <i class="fas fa-sort-numeric-up"></i>
                                Jumlah Kemas Ulang (${data.unit_label})
                            `);
                            
                            // Set max value
                            $('#jumlah_kemas_unit').attr('max', data.max_unit);
                        } else {
                            stockDetails.html('<p style="color: #ef4444;">Tidak ada stok yang siap untuk dikemas ulang.</p>');
                            stockInfo.show();
                            $('#jumlah_kemas_unit').attr('max', 0);
                        }
                    })
                    .fail(function() {
                        stockDetails.html('<p style="color: #ef4444;">Gagal memuat informasi stok.</p>');
                        stockInfo.show();
                    });
                } else {
                    stockInfo.hide();
                    labelJumlahKemas.html('<i class="fas fa-sort-numeric-up"></i> Jumlah Kemas Ulang');
                    $('#jumlah_kemas_unit').removeAttr('max');
                }
            });
            
            // Form submission
            $('#formKemasUlang').on('submit', function(e) {
                e.preventDefault();
                
                const btn = $(this).find('button[type="submit"]');
                btn.html('<span class="spinner"></span> Menyimpan...').prop('disabled', true);
                
                $.ajax({
                    url: '<?= base_url('kemas-ulang/save') ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        const msgBox = $('#formMessage');
                        msgBox.text(response.message)
                              .removeClass('alert-error alert-success')
                              .addClass(response.success ? 'alert-success' : 'alert-error')
                              .show();
                        
                        if (response.success) {
                            $('#formKemasUlang')[0].reset();
                            $('#stockInfo').hide();
                            $('#labelJumlahKemas').html('<i class="fas fa-sort-numeric-up"></i> Jumlah Kemas Ulang');
                        }
                        
                        $('html, body').animate({scrollTop: 0}, 500);
                        setTimeout(() => msgBox.slideUp(), 8000);
                    },
                    error: function() {
                        $('#formMessage').text('Terjadi kesalahan koneksi.')
                                        .removeClass('alert-success')
                                        .addClass('alert-error')
                                        .show();
                        $('html, body').animate({scrollTop: 0}, 500);
                        setTimeout(() => $('#formMessage').slideUp(), 5000);
                    },
                    complete: function(){
                        btn.html('<i class="fas fa-save"></i> Simpan Kemas Ulang').prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html>
