<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Penjualan - Stok Keluar</title>
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
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
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
        .section-card {
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            border: 2px solid #fdba74;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .section-title {
            color: #374151;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f97316;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
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
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
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
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(249, 115, 22, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 8px 12px;
            font-size: 12px;
        }
        .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .items-section {
            background: #ffffff;
            border: 2px solid #f97316;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .item-row {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 2px solid #fbbf24;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .item-grid {
            display: grid;
            grid-template-columns: 2fr 2fr 1fr 1fr auto;
            gap: 20px;
            align-items: end;
        }
        .stock-info {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 8px;
            font-size: 12px;
            font-weight: 600;
            min-height: 32px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .stock-info.loading {
            color: #f59e0b;
        }
        .stock-info.available {
            color: #059669;
            background: #ecfdf5;
            border-color: #10b981;
        }
        .stock-info.warning {
            color: #dc2626;
            background: #fef2f2;
            border-color: #ef4444;
        }
        .stock-info.error {
            color: #dc2626;
            background: #fef2f2;
            border-color: #ef4444;
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
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 16px 40px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.3);
            min-width: 250px;
        }
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(249, 115, 22, 0.4);
        }
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .customer-history {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            margin-top: 8px;
            font-size: 12px;
            max-height: 120px;
            overflow-y: auto;
            display: none;
        }
        .history-item {
            padding: 4px 8px;
            margin: 2px 0;
            background: white;
            border-radius: 4px;
            border-left: 3px solid #f97316;
        }
        .summary-card {
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
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 6px;
        }
        .summary-item .label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
        }
        .summary-item .value {
            font-size: 18px;
            color: #0f172a;
            font-weight: 700;
        }
        @media (max-width: 1200px) {
            .item-grid {
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }
        }
        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }
            .form-content {
                padding: 20px;
            }
            .item-grid {
                grid-template-columns: 1fr;
            }
            .grid-container {
                grid-template-columns: 1fr;
            }
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
        .validation-error {
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }
        .form-group.error input,
        .form-group.error select {
            border-color: #dc2626;
            background-color: #fef2f2;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-card">
            <div class="form-header">
                <h1>
                    <i class="fas fa-shopping-cart"></i>
                    Form Penjualan (Stok Keluar)
                </h1>
            </div>
            <div class="form-content">
                <div id="formMessage" class="alert"></div>
                <form id="formPenjualan">
                    <!-- Header Information -->
                    <div class="section-card">
                        <h3 class="section-title">
                            <i class="fas fa-file-invoice"></i>
                            Informasi Penjualan
                        </h3>
                        <div class="grid-container">
                            <div class="form-group">
                                <label for="no_surat_jalan">
                                    <i class="fas fa-receipt"></i>
                                    No. Surat Jalan / Referensi
                                </label>
                                <input type="text" name="no_surat_jalan" id="no_surat_jalan" class="form-control" required>
                                <div class="validation-error">Nomor surat jalan wajib diisi</div>
                            </div>
                            <div class="form-group">
                                <label for="pelat_mobil">
                                    <i class="fas fa-truck"></i>
                                    Nomor Pelat Mobil
                                </label>
                                <input type="text" name="pelat_mobil" id="pelat_mobil" class="form-control" placeholder="B 1234 ABC">
                            </div>
                            <div class="form-group">
                                <label for="customer">
                                    <i class="fas fa-user-tie"></i>
                                    Customer
                                </label>
                                <input type="text" name="customer" id="customer" class="form-control" placeholder="Nama customer">
                                <div class="customer-history" id="customerHistory"></div>
                            </div>
                            <div class="form-group">
                                <label for="tanggal">
                                    <i class="fas fa-calendar"></i>
                                    Tanggal
                                </label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                <div class="validation-error">Tanggal wajib diisi</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Summary Card -->
                    <div class="summary-card" id="summaryCard" style="display: none;">
                        <h3 class="section-title">
                            <i class="fas fa-chart-bar"></i>
                            Ringkasan Penjualan
                        </h3>
                        <div class="summary-grid">
                            <div class="summary-item">
                                <div class="label">Total Item</div>
                                <div class="value" id="totalItems">0</div>
                            </div>
                            <div class="summary-item">
                                <div class="label">Total Dus</div>
                                <div class="value" id="totalDus">0</div>
                            </div>
                            <div class="summary-item">
                                <div class="label">Total Satuan</div>
                                <div class="value" id="totalSatuan">0</div>
                            </div>
                            <div class="summary-item">
                                <div class="label">Status Stok</div>
                                <div class="value" id="stockStatus">✓ Aman</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Items Section -->
                    <div class="items-section">
                        <div class="section-title">
                            <i class="fas fa-boxes"></i>
                            Item Produk
                            <div style="margin-left: auto;">
                                <button type="button" id="btnTambahItem" class="btn btn-success">
                                    <i class="fas fa-plus"></i>
                                    Tambah Item
                                </button>
                            </div>
                        </div>
                        <div id="items-container"></div>
                    </div>
                    
                    <div class="submit-section">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save"></i>
                            Simpan Penjualan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            let itemIndex = 0;
            let stockWarnings = [];
            
            function createNewRow(index) {
                const produkOptions = `<?php foreach ($produk_list as $produk) { echo "<option value='{$produk['id_produk']}'>".htmlspecialchars($produk['nama_produk'])."</option>"; } ?>`;
                const gudangOptions = `<?php foreach ($gudang_list as $gudang) { echo "<option value='{$gudang['id_gudang']}'>".htmlspecialchars($gudang['nama_gudang'])."</option>"; } ?>`;
                
                return `
                    <div class="item-row" data-index="${index}">
                        <div class="item-grid">
                            <div class="form-group">
                                <label><i class="fas fa-box"></i> Produk</label>
                                <select name="items[${index}][produk]" class="form-control produk-select" required>
                                    <option value="">-- Pilih Produk --</option>
                                    ${produkOptions}
                                </select>
                                <div class="validation-error">Pilih produk terlebih dahulu</div>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-warehouse"></i> Gudang Pengambilan</label>
                                <select name="items[${index}][gudang]" class="form-control gudang-select" required>
                                    <option value="">-- Pilih Gudang --</option>
                                    ${gudangOptions}
                                </select>
                                <div class="stock-info"></div>
                                <div class="validation-error">Pilih gudang terlebih dahulu</div>
                            </div>
                            <div class="form-group dus-input-group" style="display: none;">
                                <label><i class="fas fa-cubes"></i> Jumlah Dus</label>
                                <input type="number" name="items[${index}][jumlah_dus]" class="form-control quantity-input" value="0" min="0">
                            </div>
                            <div class="form-group satuan-input-group" style="display: none;">
                                <label><i class="fas fa-cube"></i> Jumlah Satuan</label>
                                <input type="number" name="items[${index}][jumlah_satuan]" class="form-control quantity-input" value="0" min="0">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-danger btn-delete">
                                    <i class="fas fa-trash"></i>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            function addRow() {
                $('#items-container').append(createNewRow(itemIndex));
                itemIndex++;
                updateSummary();
            }
            
            function updateSummary() {
                const items = $('#items-container .item-row');
                let totalItems = items.length;
                let totalDus = 0;
                let totalSatuan = 0;
                let hasStockWarning = false;
                
                items.each(function() {
                    const dus = parseInt($(this).find('input[name*="jumlah_dus"]').val()) || 0;
                    const satuan = parseInt($(this).find('input[name*="jumlah_satuan"]').val()) || 0;
                    totalDus += dus;
                    totalSatuan += satuan;
                    
                    if ($(this).find('.stock-info').hasClass('warning')) {
                        hasStockWarning = true;
                    }
                });
                
                $('#totalItems').text(totalItems);
                $('#totalDus').text(totalDus.toLocaleString('id-ID'));
                $('#totalSatuan').text(totalSatuan.toLocaleString('id-ID'));
                $('#stockStatus').text(hasStockWarning ? '⚠ Periksa Stok' : '✓ Aman')
                    .css('color', hasStockWarning ? '#dc2626' : '#059669');
                
                if (totalItems > 0) {
                    $('#summaryCard').show();
                } else {
                    $('#summaryCard').hide();
                }
            }
            
            function validateStockAvailability(row) {
                const stockInfo = row.find('.stock-info');
                const stockText = stockInfo.text();
                const dus = parseInt(row.find('input[name*="jumlah_dus"]').val()) || 0;
                const satuan = parseInt(row.find('input[name*="jumlah_satuan"]').val()) || 0;
                
                if (stockText.includes('Stok:')) {
                    const matches = stockText.match(/Stok: (\d+) Dus, (\d+) Satuan/);
                    if (matches) {
                        const availableDus = parseInt(matches[1]);
                        const availableSatuan = parseInt(matches[2]);
                        
                        if (dus > availableDus || satuan > availableSatuan) {
                            stockInfo.removeClass('available').addClass('warning');
                            stockInfo.html(`<i class="fas fa-exclamation-triangle"></i> Stok tidak cukup! Tersedia: ${availableDus} Dus, ${availableSatuan} Satuan`);
                            return false;
                        } else {
                            stockInfo.removeClass('warning').addClass('available');
                            stockInfo.html(`<i class="fas fa-check-circle"></i> Stok: ${availableDus} Dus, ${availableSatuan} Satuan`);
                            return true;
                        }
                    }
                }
                return true;
            }
            
            // Event Handlers
            $('#btnTambahItem').on('click', addRow);
            
            $('#items-container').on('click', '.btn-delete', function() {
                $(this).closest('.item-row').remove();
                updateSummary();
            });
            
            $('#items-container').on('change', '.produk-select', function() {
                const currentRow = $(this).closest('.item-row');
                const id_produk = $(this).val();
                const dusGroup = currentRow.find('.dus-input-group');
                const satuanGroup = currentRow.find('.satuan-input-group');
                
                dusGroup.hide().find('input').val(0);
                satuanGroup.hide().find('input').val(0);
                
                currentRow.find('.gudang-select').trigger('change');
                
                if (id_produk) {
                    $.getJSON('<?= base_url('api/produk-info') ?>', { id_produk: id_produk })
                        .done(function(info) {
                            if (info.satuan_per_dus > 1) {
                                dusGroup.show();
                            } else {
                                satuanGroup.show();
                            }
                        });
                }
                updateSummary();
            });
            
            $('#items-container').on('change', '.gudang-select', function() {
                const currentRow = $(this).closest('.item-row');
                const id_produk = currentRow.find('.produk-select').val();
                const id_gudang = $(this).val();
                const stockInfo = currentRow.find('.stock-info');
                
                if (id_gudang && id_produk) {
                    stockInfo.removeClass('available warning error').addClass('loading');
                    stockInfo.html('<i class="fas fa-spinner fa-spin"></i> Mengecek stok...');
                    
                    $.getJSON('<?= base_url('api/current-stock') ?>', { id_gudang: id_gudang, id_produk: id_produk })
                        .done(function(stok) {
                            stockInfo.removeClass('loading').addClass('available');
                            stockInfo.html(`<i class="fas fa-check-circle"></i> Stok: ${stok.jumlah_dus} Dus, ${stok.jumlah_satuan} Satuan`);
                            validateStockAvailability(currentRow);
                            updateSummary();
                        })
                        .fail(function() {
                            stockInfo.removeClass('loading').addClass('error');
                            stockInfo.html('<i class="fas fa-times-circle"></i> Gagal cek stok');
                        });
                } else {
                    stockInfo.removeClass('available warning error loading').text('');
                }
            });
            
            $('#items-container').on('input change', '.quantity-input', function() {
                const currentRow = $(this).closest('.item-row');
                validateStockAvailability(currentRow);
                updateSummary();
            });
            
            // Customer history feature
            let customerTimeout;
            $('#customer').on('input', function() {
                const customer = $(this).val();
                const historyDiv = $('#customerHistory');
                
                clearTimeout(customerTimeout);
                
                if (customer.length >= 3) {
                    customerTimeout = setTimeout(function() {
                        $.getJSON('<?= base_url('api/customer-history') ?>', { customer: customer })
                            .done(function(history) {
                                if (history.length > 0) {
                                    let historyHtml = '<strong>Riwayat Transaksi:</strong><br>';
                                    history.forEach(function(item) {
                                        historyHtml += `<div class="history-item">${item.no_surat_jalan} - ${item.tanggal} - ${item.pelat_mobil || 'N/A'}</div>`;
                                    });
                                    historyDiv.html(historyHtml).show();
                                } else {
                                    historyDiv.hide();
                                }
                            });
                    }, 500);
                } else {
                    historyDiv.hide();
                }
            });
            
            // Form validation and submission
            $('#formPenjualan').on('submit', function(e) {
                e.preventDefault();
                
                $('.form-group').removeClass('error');
                $('.validation-error').hide();
                
                let isValid = true;
                
                if (!$('#no_surat_jalan').val()) {
                    $('#no_surat_jalan').closest('.form-group').addClass('error');
                    $('#no_surat_jalan').siblings('.validation-error').show();
                    isValid = false;
                }
                
                if (!$('#tanggal').val()) {
                    $('#tanggal').closest('.form-group').addClass('error');
                    $('#tanggal').siblings('.validation-error').show();
                    isValid = false;
                }
                
                if ($('#items-container .item-row').length === 0) {
                    alert('Harap tambahkan minimal satu item penjualan.');
                    return;
                }
                
                let hasValidItem = false;
                $('#items-container .item-row').each(function() {
                    const produk = $(this).find('.produk-select').val();
                    const gudang = $(this).find('.gudang-select').val();
                    const dus = parseInt($(this).find('input[name*="jumlah_dus"]').val()) || 0;
                    const satuan = parseInt($(this).find('input[name*="jumlah_satuan"]').val()) || 0;
                    
                    if (!produk) {
                        $(this).find('.produk-select').closest('.form-group').addClass('error');
                        $(this).find('.produk-select').siblings('.validation-error').show();
                        isValid = false;
                    }
                    
                    if (!gudang) {
                        $(this).find('.gudang-select').closest('.form-group').addClass('error');
                        $(this).find('.gudang-select').siblings('.validation-error').show();
                        isValid = false;
                    }
                    
                    if (produk && gudang && (dus > 0 || satuan > 0)) {
                        hasValidItem = true;
                    }
                });
                
                if (!hasValidItem) {
                    alert('Harap lengkapi minimal satu item dengan jumlah yang valid.');
                    isValid = false;
                }
                
                if (!isValid) {
                    $('html, body').animate({scrollTop: $('.form-group.error').first().offset().top - 100}, 500);
                    return;
                }
                
                const btn = $(this).find('button[type="submit"]');
                btn.html('<span class="spinner"></span> Menyimpan...').prop('disabled', true);
                
                $.ajax({
                    url: '<?= base_url('penjualan/save') ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        const msgBox = $('#formMessage');
                        msgBox.text(response.message).removeClass('alert-error alert-success').addClass(response.success ? 'alert-success' : 'alert-error').show();
                        
                        if (response.success) {
                            $('#formPenjualan')[0].reset();
                            $('#items-container').empty();
                            $('#customerHistory').hide();
                            updateSummary();
                            addRow();
                        }
                        
                        $('html, body').animate({scrollTop: 0}, 500);
                        setTimeout(() => msgBox.slideUp(), 5000);
                    },
                    error: function() {
                        $('#formMessage').text('Terjadi kesalahan koneksi.').removeClass('alert-success').addClass('alert-error').show();
                        $('html, body').animate({scrollTop: 0}, 500);
                        setTimeout(() => $('#formMessage').slideUp(), 5000);
                    },
                    complete: function(){
                        btn.html('<i class="fas fa-save"></i> Simpan Penjualan').prop('disabled', false);
                    }
                });
            });
            
            // Initialize
            addRow();
        });
    </script>
</body>
</html>
