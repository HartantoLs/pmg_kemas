<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Stok Opname</title>
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
            max-width: 1600px;
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
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
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
        .control-section {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            border: 2px solid #8b5cf6;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
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
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        .mode-indicator {
            padding: 15px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }
        .mode-create {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 2px solid #10b981;
        }
        .mode-edit {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 2px solid #f59e0b;
        }
        .mode-closed {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 2px solid #ef4444;
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
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 12px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .data-table td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
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
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .gudang-header {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            min-width: 80px;
            font-size: 11px;
        }
        .stock-input {
            width: 60px;
            padding: 4px 6px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            text-align: center;
            font-size: 11px;
        }
        .stock-input:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.1);
        }
        .stock-actual {
            color: #059669;
            font-weight: 600;
            font-size: 11px;
        }
        .stock-difference {
            font-weight: 600;
            font-size: 11px;
        }
        .stock-difference.positive {
            color: #059669;
        }
        .stock-difference.negative {
            color: #dc2626;
        }
        .stock-difference.zero {
            color: #6b7280;
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
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            padding: 16px 40px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.3);
            min-width: 250px;
        }
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .table-container {
            max-height: 600px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #8b5cf6;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
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
            .summary-stats {
                grid-template-columns: 1fr;
            }
            .data-table {
                font-size: 10px;
            }
            .stock-input {
                width: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-card">
            <div class="form-header">
                <h1>
                    <i class="fas fa-clipboard-check"></i>
                    Form Stok Opname
                </h1>
            </div>
            <div class="form-content">
                <div class="control-section">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="tanggal_opname_month">
                                <i class="fas fa-calendar"></i>
                                Periode Stok Opname
                            </label>
                            <input type="month" name="tanggal_opname_month" id="tanggal_opname_month" 
                                   value="<?php echo $selected_month_year; ?>" 
                                   onchange="window.location.href='<?= base_url('stok-opname') ?>?tanggal_opname_month=' + this.value">
                        </div>
                    </div>
                    
                    <?php if ($mode === 'create'): ?>
                        <div class="mode-indicator mode-create">
                            <i class="fas fa-plus-circle"></i> Mode: Buat Stok Opname Baru - <?php echo date('F Y', strtotime($selected_tanggal)); ?>
                        </div>
                    <?php elseif ($mode === 'edit'): ?>
                        <div class="mode-indicator mode-edit">
                            <i class="fas fa-edit"></i> Mode: Edit Stok Opname - <?php echo date('F Y', strtotime($selected_tanggal)); ?>
                        </div>
                    <?php else: ?>
                        <div class="mode-indicator mode-closed">
                            <i class="fas fa-lock"></i> Mode: Periode Tertutup - <?php echo date('F Y', strtotime($selected_tanggal)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="summary-stats">
                        <div class="stat-card">
                            <div class="stat-value"><?php echo count($produk_list); ?></div>
                            <div class="stat-label">Total Produk</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo count($gudang_list); ?></div>
                            <div class="stat-label">Total Gudang</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="totalRecords"><?php echo $total_stok_records; ?></div>
                            <div class="stat-label">Total Records Stok</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="filledRecords">0</div>
                            <div class="stat-label">Records Terisi</div>
                        </div>
                    </div>
                </div>

                <div id="formMessage" class="alert"></div>
                
                <?php if ($mode !== 'closed'): ?>
                <form id="formStokOpname">
                    <input type="hidden" name="tanggal_opname" value="<?php echo $selected_tanggal; ?>">
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 200px;">Produk</th>
                                    <?php foreach ($gudang_list as $gudang): ?>
                                        <th colspan="3" class="gudang-header">
                                            <?php echo htmlspecialchars($gudang['nama_gudang']); ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <?php foreach ($gudang_list as $gudang): ?>
                                        <th style="font-size: 10px;">Dus</th>
                                        <th style="font-size: 10px;">Satuan</th>
                                        <th style="font-size: 10px;">Aktual</th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produk_list as $produk): ?>
                                    <tr>
                                        <td class="produk-name" title="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                                            <?php echo htmlspecialchars($produk['nama_produk']); ?>
                                        </td>
                                        <?php foreach ($gudang_list as $gudang): ?>
                                            <?php 
                                                $existingDus = $existing_data[$produk['id_produk']][$gudang['id_gudang']]['dus'] ?? '';
                                                $existingSatuan = $existing_data[$produk['id_produk']][$gudang['id_gudang']]['satuan'] ?? '';
                                                $aktualData = $stok_aktual_map[$produk['id_produk']][$gudang['id_gudang']] ?? null;
                                                $aktualDus = $aktualData['jumlah_dus'] ?? 0;
                                                $aktualSatuan = $aktualData['jumlah_satuan'] ?? 0;
                                            ?>
                                            <td>
                                                <input type="number" 
                                                       name="items[<?php echo $produk['id_produk']; ?>][<?php echo $gudang['id_gudang']; ?>][dus]" 
                                                       class="stock-input opname-input" 
                                                       value="<?php echo $existingDus; ?>" 
                                                       min="0" 
                                                       data-produk="<?php echo $produk['id_produk']; ?>"
                                                       data-gudang="<?php echo $gudang['id_gudang']; ?>"
                                                       data-type="dus">
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       name="items[<?php echo $produk['id_produk']; ?>][<?php echo $gudang['id_gudang']; ?>][satuan]" 
                                                       class="stock-input opname-input" 
                                                       value="<?php echo $existingSatuan; ?>" 
                                                       min="0"
                                                       data-produk="<?php echo $produk['id_produk']; ?>"
                                                       data-gudang="<?php echo $gudang['id_gudang']; ?>"
                                                       data-type="satuan">
                                            </td>
                                            <td class="stock-actual">
                                                <?php echo number_format($aktualDus); ?>D / <?php echo number_format($aktualSatuan); ?>S
                                                <?php if ($aktualData): ?>
                                                    <br><small style="color: #6b7280;"><?php echo date('d/m H:i', strtotime($aktualData['last_updated'])); ?></small>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="submit-section">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save"></i>
                            Simpan Stok Opname
                        </button>
                    </div>
                </form>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #6b7280;">
                        <i class="fas fa-info-circle" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
                        <h3>Periode Stok Opname Tertutup</h3>
                        <p>Stok opname untuk periode ini sudah tidak dapat diubah.<br>
                        Pilih periode bulan berjalan untuk melakukan stok opname.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            function updateFilledRecords() {
                let filledCount = 0;
                $('.opname-input').each(function() {
                    if ($(this).val() !== '') {
                        filledCount++;
                    }
                });
                $('#filledRecords').text(filledCount);
            }
            
            // Update filled records count on input change
            $('.opname-input').on('input change', function() {
                updateFilledRecords();
            });
            
            // Auto-fill with actual stock on double-click
            $('.stock-actual').on('dblclick', function() {
                const row = $(this).closest('tr');
                const actualText = $(this).text();
                const matches = actualText.match(/(\d+)D \/ (\d+)S/);
                
                if (matches) {
                    const dus = matches[1];
                    const satuan = matches[2];
                    
                    row.find('input[data-type="dus"]').val(dus);
                    row.find('input[data-type="satuan"]').val(satuan);
                    
                    updateFilledRecords();
                }
            });
            
            // Bulk fill functions
            $(document).on('keydown', function(e) {
                // Ctrl+A to fill all with actual stock
                if (e.ctrlKey && e.key === 'a' && e.target.tagName !== 'INPUT') {
                    e.preventDefault();
                    
                    if (confirm('Isi semua field dengan stok aktual?')) {
                        $('.stock-actual').each(function() {
                            const actualText = $(this).text();
                            const matches = actualText.match(/(\d+)D \/ (\d+)S/);
                            
                            if (matches) {
                                const row = $(this).closest('tr');
                                const dus = matches[1];
                                const satuan = matches[2];
                                
                                row.find('input[data-type="dus"]').val(dus);
                                row.find('input[data-type="satuan"]').val(satuan);
                            }
                        });
                        
                        updateFilledRecords();
                    }
                }
                
                // Ctrl+R to clear all
                if (e.ctrlKey && e.key === 'r') {
                    e.preventDefault();
                    
                    if (confirm('Kosongkan semua field?')) {
                        $('.opname-input').val('');
                        updateFilledRecords();
                    }
                }
            });
            
            // Form submission
            $('#formStokOpname').on('submit', function(e) {
                e.preventDefault();
                
                const filledInputs = $('.opname-input').filter(function() {
                    return $(this).val() !== '';
                }).length;
                
                if (filledInputs === 0) {
                    alert('Harap isi minimal satu field stok opname.');
                    return;
                }
                
                if (!confirm(`Simpan stok opname dengan ${filledInputs} records terisi?`)) {
                    return;
                }
                
                const btn = $(this).find('button[type="submit"]');
                btn.html('<span class="spinner"></span> Menyimpan...').prop('disabled', true);
                
                $.ajax({
                    url: '<?= base_url('stok-opname/save') ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        const msgBox = $('#formMessage');
                        msgBox.text(response.message)
                              .removeClass('alert-error alert-success')
                              .addClass(response.success ? 'alert-success' : 'alert-error')
                              .show();
                        
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
                        btn.html('<i class="fas fa-save"></i> Simpan Stok Opname').prop('disabled', false);
                    }
                });
            });
            
            // Initialize
            updateFilledRecords();
            
            // Add tooltip for shortcuts
            $('body').append(`
                <div style="position: fixed; bottom: 20px; right: 20px; background: rgba(0,0,0,0.8); color: white; padding: 10px; border-radius: 6px; font-size: 12px; z-index: 1000;">
                    <strong>Shortcuts:</strong><br>
                    Double-click stok aktual: Isi otomatis<br>
                    Ctrl+A: Isi semua dengan stok aktual<br>
                    Ctrl+R: Kosongkan semua
                </div>
            `);
        });
    </script>
</body>
</html>
