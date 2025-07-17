<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pengemasan</title>
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
        .form-header h2 {
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
        .header-section {
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            border: 2px solid #fdba74;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
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
        .section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f97316;
        }
        .section-title h3 {
            color: #374151;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
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
        .item-card {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 2px solid #fbbf24;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            position: relative;
        }
        .item-grid {
            display: grid;
            grid-template-columns: 1.5fr 1.5fr 2fr 1fr 2fr auto;
            gap: 20px;
            align-items: start;
        }
        .bahan-info {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            font-size: 13px;
            color: #374151;
            min-height: 80px;
            margin-top: 28px;
        }
        .bahan-info h5 {
            color: #f97316;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 12px;
        }
        .bahan-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            padding: 2px 0;
        }
        .bahan-item strong {
            color: #374151;
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
        @media (max-width: 1200px) {
            .item-grid {
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }
            .bahan-info {
                margin-top: 0;
                grid-column: 1 / -1;
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
            .section-title {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
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
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-card">
            <div class="form-header">
                <h2>
                    <i class="fas fa-box"></i>
                    Input Hasil Pengemasan
                </h2>
            </div>
            <div class="form-content">
                <div id="form-messages" class="alert"></div>
                <form id="pengemasan-form">
                    <div class="header-section">
                        <div class="grid-container">
                            <div class="form-group">
                                <label for="tTanggal">
                                    <i class="fas fa-calendar"></i>
                                    Tanggal
                                </label>
                                <input type="date" name="tTanggal" id="tTanggal" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="tShift">
                                    <i class="fas fa-clock"></i>
                                    Shift
                                </label>
                                <select name="tShift" id="tShift" required>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="section-title">
                        <h3>
                            <i class="fas fa-cogs"></i>
                            Data Produksi
                        </h3>
                        <button type="button" id="btn-tambah-item" class="btn btn-success">
                            <i class="fas fa-plus"></i>
                            Tambah Produksi
                        </button>
                    </div>
                    <div id="items-container"></div>
                    <div class="submit-section">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save"></i>
                            Simpan Semua Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        (function() {
            let itemCounter = 0;
            function tambahItem() {
                const newItem = $(`
                    <div class="item-card" data-index="${itemCounter}">
                        <div class="item-grid">
                            <div class="form-group">
                                <label><i class="fas fa-warehouse"></i> Gudang</label>
                                <select name="items[${itemCounter}][gudang]" class="gudang-select" required>
                                    <option value="">Memuat...</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-cog"></i> Mesin</label>
                                <select name="items[${itemCounter}][mesin]" class="mesin-select" required>
                                    <option value="">-- Pilih Gudang Dulu --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-industry"></i> Jenis Produksi</label>
                                <select name="items[${itemCounter}][jenis_produksi]" class="jenis-produksi-select" required>
                                    <option value="">Memuat...</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="jumlah-label"><i class="fas fa-sort-numeric-up"></i> Jumlah</label>
                                <input type="number" name="items[${itemCounter}][jumlah]" value="0" min="0" class="jumlah-input" required>
                            </div>
                            <div class="bahan-info">
                                <h5><i class="fas fa-list"></i> Kebutuhan Bahan:</h5>
                                <div class="bahan-content">Pilih jenis produksi dan masukkan jumlah</div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-danger btn-hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);
                newItem.find('.btn-hapus').on('click', function() {
                    if ($('.item-card').length > 1) {
                        $(this).closest('.item-card').remove();
                    }
                });
                $('#items-container').append(newItem);
                loadGudangOptions(newItem.find('.gudang-select'));
                loadJenisProduksiOptions(newItem.find('.jenis-produksi-select'));
                itemCounter++;
            }
            function loadGudangOptions(selectElement) {
                $.get("<?= base_url('api/gudang') ?>")
                    .done(function(data) {
                        selectElement.html(data);
                    })
                    .fail(function() {
                        selectElement.html('<option value="">Gagal memuat gudang</option>');
                    });
            }
            function loadJenisProduksiOptions(selectElement) {
                $.get("<?= base_url('api/jenis-produksi') ?>")
                    .done(function(data) {
                        selectElement.html(data);
                    })
                    .fail(function() {
                        selectElement.html('<option value="">Gagal memuat jenis produksi</option>');
                    });
            }
            function displayBahanBaku(itemCard) {
                const bahanContent = itemCard.find('.bahan-content');
                const jumlahLabel = itemCard.find('.jumlah-label');
                const jenisProduksiSelect = itemCard.find('.jenis-produksi-select');
                const jumlah = parseInt(itemCard.find('.jumlah-input').val()) || 0;
                const data = jenisProduksiSelect.data('resep');
                if (!data) return;
                jumlahLabel.html(`<i class="fas fa-sort-numeric-up"></i> Jumlah (${data.unit_label})`);
                if (jumlah > 0 && data.bahan_baku && data.bahan_baku.length > 0) {
                    let bahanHtml = '';
                    data.bahan_baku.forEach(function(item) {
                        let total = (parseFloat(item.jumlah) * jumlah);
                        bahanHtml += `<div class="bahan-item"><strong>${item.nama_barang}:</strong> <span>${total.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2})}</span></div>`;
                    });
                    bahanContent.html(bahanHtml);
                } else {
                    bahanContent.html('Pilih jenis produksi dan masukkan jumlah');
                }
            }
            // Event handlers
            $(document).on('change', '.gudang-select', function() {
                const itemCard = $(this).closest('.item-card');
                const mesinSelect = itemCard.find('.mesin-select');
                const namaGudang = $(this).find('option:selected').data('nama-gudang');
                mesinSelect.html('<option>Memuat...</option>');
                if (namaGudang) {
                    $.get("<?= base_url('api/mesin') ?>?nama_gudang=" + encodeURIComponent(namaGudang))
                        .done(data => mesinSelect.html(data))
                        .fail(() => mesinSelect.html('<option>Gagal memuat mesin</option>'));
                } else {
                    mesinSelect.html('<option>-- Pilih Gudang Dulu --</option>');
                }
            });
            $(document).on('change', '.jenis-produksi-select', function() {
                const itemCard = $(this).closest('.item-card');
                const nomJenisProduksi = $(this).val();
                if (nomJenisProduksi > 0) {
                    $.getJSON("<?= base_url('api/info-produksi') ?>?nom_jenis_produksi=" + nomJenisProduksi)
                    .done(function(data) {
                        itemCard.find('.jenis-produksi-select').data('resep', data);
                        displayBahanBaku(itemCard);
                    })
                    .fail(function() {
                        console.log('Gagal memuat info produksi');
                    });
                } else {
                    itemCard.find('.jumlah-label').html('<i class="fas fa-sort-numeric-up"></i> Jumlah');
                    itemCard.find('.bahan-content').html('Pilih jenis produksi dan masukkan jumlah');
                }
            });
            $(document).on('keyup change', '.jumlah-input', function() {
                const itemCard = $(this).closest('.item-card');
                displayBahanBaku(itemCard);
            });
            $(document).ready(function() {
                tambahItem();
                $('#btn-tambah-item').click(tambahItem);
                $('#pengemasan-form').on('submit', function(event) {
                    event.preventDefault();
                    const submitBtn = $('.submit-btn');
                    const formMessages = $('#form-messages');
                    submitBtn.prop('disabled', true).html('<span class="spinner"></span> Menyimpan...');
                    const formData = $(this).serialize();
                    $.ajax({
                        type: 'POST',
                        url: '<?= base_url('pengemasan/save') ?>',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            formMessages.removeClass('alert-error').addClass('alert-success');
                            formMessages.text(response.message);
                            formMessages.show();
                            // Reset form
                            $('#items-container').empty();
                            tambahItem();
                            // Scroll to top
                            $('html, body').animate({scrollTop: 0}, 500);
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = 'Terjadi kesalahan.';
                            if(xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            formMessages.removeClass('alert-success').addClass('alert-error');
                            formMessages.text(errorMessage);
                            formMessages.show();
                            // Scroll to top
                            $('html, body').animate({scrollTop: 0}, 500);
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Semua Data');
                        }
                    });
                });
            });
        })();
    </script>
</body>
</html>
