<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Seleksi Produk</title>
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 2px solid #f59e0b;
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
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
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
            color: #f59e0b;
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
        .calculation-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .calculation-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .calc-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        .calc-item .label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .calc-item .value {
            font-size: 18px;
            color: #0f172a;
            font-weight: 700;
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 16px 40px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.3);
            min-width: 250px;
        }
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
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
            .calculation-grid {
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
                    <i class="fas fa-filter"></i>
                    Form Seleksi Produk
                </h1>
            </div>
            <div class="form-content">
                <div class="info-section">
                    <h3 style="color: #d97706; margin-bottom: 15px;">
                        <i class="fas fa-info-circle"></i> Informasi Seleksi
                    </h3>
                    <p style="color: #374151; line-height: 1.6;">
                        Proses seleksi digunakan untuk memisahkan produk yang aman dan produk curah dari stok overpack. 
                        Masukkan jumlah produk yang telah diseleksi berdasarkan kondisinya.
                    </p>
                </div>

                <div id="formMessage" class="alert"></div>
                <form id="formSeleksi">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="tanggal">
                                <i class="fas fa-calendar"></i>
                                Tanggal Seleksi
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
                            <label for="pcs_aman">
                                <i class="fas fa-check-circle"></i>
                                Jumlah Aman (Pcs)
                            </label>
                            <input type="number" name="pcs_aman" id="pcs_aman" 
                                   value="0" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="pcs_curah">
                                <i class="fas fa-exclamation-circle"></i>
                                Jumlah Curah (Pcs)
                            </label>
                            <input type="number" name="pcs_curah" id="pcs_curah" 
                                   value="0" min="0" required>
                        </div>
                    </div>
                    
                    <div class="stock-info" id="stockInfo" style="display: none;">
                        <h4><i class="fas fa-warehouse"></i> Informasi Stok</h4>
                        <div id="stockDetails"></div>
                    </div>
                    
                    <div class="calculation-section" id="calculationSection" style="display: none;">
                        <h4 style="color: #0ea5e9; margin-bottom: 15px; text-align: center;">
                            <i class="fas fa-calculator"></i> Perhitungan Seleksi
                        </h4>
                        <div class="calculation-grid">
                            <div class="calc-item">
                                <div class="label">Total Input</div>
                                <div class="value" id="totalInput">0</div>
                            </div>
                            <div class="calc-item">
                                <div class="label">Stok Tersedia</div>
                                <div class="value" id="stokTersedia">0</div>
                            </div>
                            <div class="calc-item">
                                <div class="label">Status</div>
                                <div class="value" id="statusValidasi" style="font-size: 14px;">-</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="submit-section">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save"></i>
                            Simpan Hasil Seleksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            let currentStokTersedia = 0;
            
            // Load stock info when product changes
            $('#id_produk').on('change', function() {
                const idProduk = $(this).val();
                const stockInfo = $('#stockInfo');
                const stockDetails = $('#stockDetails');
                
                if (idProduk) {
                    $.getJSON('<?= base_url('seleksi/get-stok-seleksi') ?>', { 
                        id_produk: idProduk 
                    })
                    .done(function(data) {
                        currentStokTersedia = data.belum_seleksi || 0;
                        
                        let stockHtml = `
                            <div class="stock-item">
                                <span><strong>Stok Belum Diseleksi:</strong></span>
                                <span style="color: #f59e0b; font-weight: 600;">${currentStokTersedia.toLocaleString('id-ID')} pcs</span>
                            </div>
                        `;
                        
                        if (currentStokTersedia > 0) {
                            stockHtml += `
                                <div class="stock-item">
                                    <span style="color: #059669;"><i class="fas fa-info-circle"></i> Siap untuk diseleksi</span>
                                    <span></span>
                                </div>
                            `;
                        } else {
                            stockHtml += `
                                <div class="stock-item">
                                    <span style="color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Tidak ada stok yang perlu diseleksi</span>
                                    <span></span>
                                </div>
                            `;
                        }
                        
                        stockDetails.html(stockHtml);
                        stockInfo.show();
                        updateCalculation();
                    })
                    .fail(function() {
                        stockDetails.html('<p style="color: #ef4444;">Gagal memuat informasi stok.</p>');
                        stockInfo.show();
                        currentStokTersedia = 0;
                    });
                } else {
                    stockInfo.hide();
                    $('#calculationSection').hide();
                    currentStokTersedia = 0;
                }
            });
            
            // Update calculation when quantities change
            $('#pcs_aman, #pcs_curah').on('input change', function() {
                updateCalculation();
            });
            
            function updateCalculation() {
                const pcsAman = parseInt($('#pcs_aman').val()) || 0;
                const pcsCurah = parseInt($('#pcs_curah').val()) || 0;
                const totalInput = pcsAman + pcsCurah;
                
                $('#totalInput').text(totalInput.toLocaleString('id-ID'));
                $('#stokTersedia').text(currentStokTersedia.toLocaleString('id-ID'));
                
                let status = '';
                let statusColor = '';
                
                if (totalInput === 0) {
                    status = 'Belum Input';
                    statusColor = '#6b7280';
                } else if (totalInput > currentStokTersedia) {
                    status = 'Melebihi Stok';
                    statusColor = '#ef4444';
                } else if (totalInput === currentStokTersedia) {
                    status = 'Sesuai Stok';
                    statusColor = '#10b981';
                } else {
                    status = 'Valid';
                    statusColor = '#059669';
                }
                
                $('#statusValidasi').text(status).css('color', statusColor);
                
                if (currentStokTersedia > 0) {
                    $('#calculationSection').show();
                }
            }
            
            // Form submission
            $('#formSeleksi').on('submit', function(e) {
                e.preventDefault();
                
                const pcsAman = parseInt($('#pcs_aman').val()) || 0;
                const pcsCurah = parseInt($('#pcs_curah').val()) || 0;
                const totalInput = pcsAman + pcsCurah;
                
                // Validation
                if (totalInput === 0) {
                    alert('Harap masukkan jumlah pcs aman atau curah.');
                    return;
                }
                
                if (totalInput > currentStokTersedia) {
                    alert(`Total input (${totalInput} pcs) melebihi stok yang tersedia (${currentStokTersedia} pcs).`);
                    return;
                }
                
                const btn = $(this).find('button[type="submit"]');
                btn.html('<span class="spinner"></span> Menyimpan...').prop('disabled', true);
                
                $.ajax({
                    url: '<?= base_url('seleksi/save') ?>',
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
                            $('#formSeleksi')[0].reset();
                            $('#stockInfo').hide();
                            $('#calculationSection').hide();
                            currentStokTersedia = 0;
                        }
                        
                        $('html, body').animate({scrollTop: 0}, 500);
                        setTimeout(() => msgBox.slideUp(), 5000);
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
                        btn.html('<i class="fas fa-save"></i> Simpan Hasil Seleksi').prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html>
