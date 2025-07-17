<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Operpack Kerusakan</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
            border: 2px solid #ef4444;
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
            border-bottom: 2px solid #ef4444;
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
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        .items-section {
            background: #ffffff;
            border: 2px solid #ef4444;
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
            grid-template-columns: 2fr 1fr 1fr auto;
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 16px 40px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
            min-width: 250px;
        }
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .history-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 12px;
            max-height: 150px;
            overflow-y: auto;
            display: none;
        }
        .history-item {
            padding: 8px;
            margin: 4px 0;
            background: white;
            border-radius: 4px;
            border-left: 3px solid #ef4444;
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
                    <i class="fas fa-exclamation-triangle"></i>
                    Form Operpack Kerusakan
                </h1>
            </div>
            <div class="form-content">
                <div id="formMessage" class="alert"></div>
                <form id="formOperpackKerusakan">
                    <!-- Header Information -->
                    <div class="section-card">
                        <h3 class="section-title">
                            <i class="fas fa-file-invoice"></i>
                            Informasi Pengembalian Kerusakan
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="no_surat_jalan">
                                    <i class="fas fa-receipt"></i>
                                    No. Surat Jalan
                                </label>
                                <input type="text" name="no_surat_jalan" id="no_surat_jalan" required>
                            </div>
                            <div class="form-group">
                                <label for="tanggal">
                                    <i class="fas fa-calendar"></i>
                                    Tanggal
                                </label>
                                <input type="date" name="tanggal" id="tanggal" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="kategori_asal">
                                    <i class="fas fa-tags"></i>
                                    Kategori Asal
                                </label>
                                <select name="kategori_asal" id="kategori_asal" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Internal">Internal (Gudang)</option>
                                    <option value="Eksternal">Eksternal (Customer/Supplier)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="asal" id="labelAsal">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Asal Pengembalian
                                </label>
                                <select name="asal" id="asal" required style="display: none;">
                                    <option value="">-- Pilih Gudang --</option>
                                </select>
                                <input type="text" name="asal_eksternal" id="asal_eksternal" 
                                       placeholder="Nama customer/supplier" style="display: none;">
                            </div>
                        </div>
                        
                        <div class="history-section" id="damageHistory">
                            <h5 style="color: #ef4444; font-weight: 600; margin-bottom: 10px;">
                                <i class="fas fa-history"></i> Riwayat Kerusakan Terakhir
                            </h5>
                            <div id="historyContent"></div>
                        </div>
                    </div>
                    
                    <!-- Items Section -->
                    <div class="items-section">
                        <div class="section-title">
                            <i class="fas fa-boxes"></i>
                            Item Produk Rusak
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
                            Simpan Data Kerusakan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            let itemIndex = 0;
            
            function createNewRow(index) {
                const produkOptions = `<?php foreach ($produk_list as $produk) { echo "<option value='{$produk['id_produk']}' data-satuan-per-dus='{$produk['satuan_per_dus']}'>".htmlspecialchars($produk['nama_produk'])."</option>"; } ?>`;
                
                return `
                    <div class="item-row" data-index="${index}">
                        <div class="item-grid">
                            <div class="form-group">
                                <label><i class="fas fa-box"></i> Produk</label>
                                <select name="items[${index}][produk]" class="form-control produk-select" required>
                                    <option value="">-- Pilih Produk --</option>
                                    ${produkOptions}
                                </select>
                                <div class="stock-info"></div>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-cubes"></i> Jumlah Dus</label>
                                <input type="number" name="items[${index}][jumlah_dus]" class="form-control quantity-input" value="0" min="0">
                            </div>
                            <div class="form-group">
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
            }
            
            function loadGudangInternal() {
                $.getJSON('<?= base_url('operpack-kerusakan/get-gudang-internal') ?>')
                .done(function(gudangList) {
                    let options = '<option value="">-- Pilih Gudang --</option>';
                    gudangList.forEach(function(gudang) {
                        options += `<option value="${gudang.id_gudang}">${gudang.nama_gudang}</option>`;
                    });
                    $('#asal').html(options);
                });
            }
            
            function loadDamageHistory() {
                const kategoriAsal = $('#kategori_asal').val();
                let asal = '';
                
                if (kategoriAsal === 'Internal') {
                    asal = $('#asal').val();
                } else if (kategoriAsal === 'Eksternal') {
                    asal = $('#asal_eksternal').val();
                }
                
                if (kategoriAsal && asal) {
                    $.getJSON('<?= base_url('operpack-kerusakan/get-damage-history') ?>', {
                        kategori_asal: kategoriAsal,
                        asal: asal
                    })
                    .done(function(history) {
                        if (history.length > 0) {
                            let historyHtml = '';
                            history.forEach(function(item) {
                                historyHtml += `
                                    <div class="history-item">
                                        <strong>${item.no_surat_jalan}</strong> - ${item.waktu_diterima}<br>
                                        <small>${item.kategori_asal}: ${item.asal} (${item.total_items} items, ${item.total_pcs} pcs)</small>
                                    </div>
                                `;
                            });
                            $('#historyContent').html(historyHtml);
                            $('#damageHistory').show();
                        } else {
                            $('#damageHistory').hide();
                        }
                    });
                } else {
                    $('#damageHistory').hide();
                }
            }
            
            function updateStockInfo(row) {
                const kategoriAsal = $('#kategori_asal').val();
                const asal = $('#asal').val();
                const produk = row.find('.produk-select').val();
                const stockInfo = row.find('.stock-info');
                
                if (kategoriAsal === 'Internal' && asal && produk) {
                    stockInfo.removeClass('available warning').addClass('loading');
                    stockInfo.html('<i class="fas fa-spinner fa-spin"></i> Mengecek stok...');
                    
                    $.getJSON('<?= base_url('operpack-kerusakan/get-stok-produk') ?>', {
                        id_gudang: asal,
                        id_produk: produk
                    })
                    .done(function(data) {
                        if (data.exists) {
                            stockInfo.removeClass('loading').addClass('available');
                            stockInfo.html(`<i class="fas fa-check-circle"></i> Stok: ${data.jumlah_dus} Dus, ${data.jumlah_satuan} Satuan`);
                        } else {
                            stockInfo.removeClass('loading').addClass('warning');
                            stockInfo.html('<i class="fas fa-exclamation-triangle"></i> Produk tidak tersedia di gudang ini');
                        }
                    })
                    .fail(function() {
                        stockInfo.removeClass('loading').addClass('warning');
                        stockInfo.html('<i class="fas fa-times-circle"></i> Gagal cek stok');
                    });
                } else if (kategoriAsal === 'Eksternal') {
                    stockInfo.removeClass('loading available warning');
                    stockInfo.html('<i class="fas fa-info-circle"></i> Pengembalian dari eksternal');
                } else {
                    stockInfo.removeClass('loading available warning').text('');
                }
            }
            
            // Event Handlers
            $('#btnTambahItem').on('click', addRow);
            
            $('#items-container').on('click', '.btn-delete', function() {
                $(this).closest('.item-row').remove();
            });
            
            $('#kategori_asal').on('change', function() {
                const kategori = $(this).val();
                const labelAsal = $('#labelAsal');
                const selectAsal = $('#asal');
                const inputAsal = $('#asal_eksternal');
                
                if (kategori === 'Internal') {
                    labelAsal.html('<i class="fas fa-warehouse"></i> Gudang Internal');
                    selectAsal.show().prop('required', true);
                    inputAsal.hide().prop('required', false).val('');
                    loadGudangInternal();
                } else if (kategori === 'Eksternal') {
                    labelAsal.html('<i class="fas fa-user-tie"></i> Customer/Supplier');
                    selectAsal.hide().prop('required', false).val('');
                    inputAsal.show().prop('required', true);
                } else {
                    selectAsal.hide().prop('required', false).val('');
                    inputAsal.hide().prop('required', false).val('');
                }
                
                $('#damageHistory').hide();
                $('#items-container .item-row').each(function() {
                    updateStockInfo($(this));
                });
            });
            
            $('#asal, #asal_eksternal').on('change keyup', function() {
                loadDamageHistory();
                $('#items-container .item-row').each(function() {
                    updateStockInfo($(this));
                });
            });
            
            $('#items-container').on('change', '.produk-select', function() {
                const currentRow = $(this).closest('.item-row');
                updateStockInfo(currentRow);
            });
            
            // Form submission
            $('#formOperpackKerusakan').on('submit', function(e) {
                e.preventDefault();
                
                // Prepare form data
                const formData = $(this).serializeArray();
                const kategoriAsal = $('#kategori_asal').val();
                
                // Add correct asal value based on category
                if (kategoriAsal === 'Eksternal') {
                    formData.push({name: 'asal', value: $('#asal_eksternal').val()});
                }
                
                const btn = $(this).find('button[type="submit"]');
                btn.html('<span class="spinner"></span> Menyimpan...').prop('disabled', true);
                
                $.ajax({
                    url: '<?= base_url('operpack-kerusakan/save') ?>',
                    type: 'POST',
                    data: $.param(formData),
                    dataType: 'json',
                    success: function(response) {
                        const msgBox = $('#formMessage');
                        msgBox.text(response.message)
                              .removeClass('alert-error alert-success')
                              .addClass(response.success ? 'alert-success' : 'alert-error')
                              .show();
                        
                        if (response.success) {
                            $('#formOperpackKerusakan')[0].reset();
                            $('#items-container').empty();
                            $('#damageHistory').hide();
                            $('#asal').hide().prop('required', false);
                            $('#asal_eksternal').hide().prop('required', false);
                            addRow();
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
                        btn.html('<i class="fas fa-save"></i> Simpan Data Kerusakan').prop('disabled', false);
                    }
                });
            });
            
            // Initialize
            addRow();
        });
    </script>
</body>
</html>
