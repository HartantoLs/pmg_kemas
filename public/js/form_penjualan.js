$(document).ready(function() {
    let itemIndex = 0;
    const selfUrl = `${baseUrl}/penjualan`;
    
    function createNewRow(index) {
        return `
            <div class="item-row anim-fade-in" data-index="${index}">
                <div class="item-grid">
                    <div class="form-group">
                        <label><i class="fas fa-box"></i> Produk</label>
                        <select name="items[${index}][produk]" class="form-control produk-select" required>
                            <option value="">-- Pilih Produk --</option>
                            ${produkOptions}
                        </select>
                        <div class="validation-error">Wajib dipilih</div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-warehouse"></i> Gudang Pengambilan</label>
                        <select name="items[${index}][gudang]" class="form-control gudang-select" required>
                            <option value="">-- Pilih Gudang --</option>
                            ${gudangOptions}
                        </select>
                        <div class="stock-info">Pilih tanggal, produk & gudang</div>
                        <div class="validation-error">Wajib dipilih</div>
                    </div>
                    <div class="form-group dus-input-group" style="display: none;">
                        <label><i class="fas fa-cubes"></i> Jumlah Dus</label>
                        <input type="number" name="items[${index}][jumlah_dus]" class="form-control quantity-input" value="0" min="0">
                    </div>
                    <div class="form-group satuan-input-group">
                        <label><i class="fas fa-cube"></i> Jumlah Satuan</label>
                        <input type="number" name="items[${index}][jumlah_satuan]" class="form-control quantity-input" value="0" min="0">
                    </div>
                    <div class="form-group" style="align-self: end;">
                        <button type="button" class="btn btn-danger btn-delete"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>`;
    }

    function addRow() {
        $('#items-container').append(createNewRow(itemIndex));
        itemIndex++;
        updateSummary();
    }

    function updateStockInfo(row) {
        const id_produk = row.find('.produk-select').val();
        const id_gudang = row.find('.gudang-select').val();
        const tanggal = $('#tanggal').val();
        const stockInfo = row.find('.stock-info');

        if (!id_produk || !id_gudang || !tanggal) {
            stockInfo.text('Pilih tanggal, produk & gudang').removeClass('available warning error loading');
            return;
        }

        stockInfo.html('<i class="fas fa-spinner fa-spin"></i> Mengecek...').removeClass('available warning error').addClass('loading');

        $.getJSON(`${selfUrl}/getstokpadatanggal`, { id_produk, id_gudang, tanggal })
            .done(function(stok) {
                const stokText = `Stok: ${stok.dus} Dus, ${stok.satuan} Satuan`;
                stockInfo.html(`<i class="fas fa-check-circle"></i> ${stokText}`).removeClass('loading').addClass('available').data('stok', stok);
                validateStockAvailability(row);
            })
            .fail(function() {
                stockInfo.html('<i class="fas fa-times-circle"></i> Gagal cek stok').removeClass('loading').addClass('error');
            });
    }
    
    function validateStockAvailability(row) {
        const stockInfo = row.find('.stock-info');
        const availableStock = stockInfo.data('stok');
        if (!availableStock) return true;

        const dus = parseInt(row.find('input[name*="jumlah_dus"]').val()) || 0;
        const satuan = parseInt(row.find('input[name*="jumlah_satuan"]').val()) || 0;

        if (dus > availableStock.dus || satuan > availableStock.satuan) {
            stockInfo.removeClass('available').addClass('warning');
            return false;
        } else {
            stockInfo.removeClass('warning').addClass('available');
            return true;
        }
    }

    function updateSummary() {
        let totalItems = 0, totalDus = 0, totalSatuan = 0, hasStockWarning = false;
        $('#items-container .item-row').each(function() {
            totalItems++;
            totalDus += parseInt($(this).find('input[name*="jumlah_dus"]').val()) || 0;
            totalSatuan += parseInt($(this).find('input[name*="jumlah_satuan"]').val()) || 0;
            if ($(this).find('.stock-info').hasClass('warning')) {
                hasStockWarning = true;
            }
        });
        $('#totalItems').text(totalItems);
        $('#totalDus').text(totalDus.toLocaleString('id-ID'));
        $('#totalSatuan').text(totalSatuan.toLocaleString('id-ID'));
        $('#stockStatus').text(hasStockWarning ? '⚠ Periksa Stok' : '✓ Aman').css('color', hasStockWarning ? '#dc2626' : '#059669');
        $('#summaryCard').toggle(totalItems > 0);
    }
    
    function showAlert(type, message) {
        const msgBox = $('#formMessage');
        msgBox.text(message).removeClass('alert-error alert-success').addClass(`alert-${type}`).show();
        $('html, body').animate({ scrollTop: 0 }, 300);
        setTimeout(() => msgBox.slideUp(), 5000);
    }

    // --- Event Handlers ---
    $('#btnTambahItem').on('click', addRow);
    $('#items-container').on('click', '.btn-delete', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            updateSummary();
        } else {
            alert('Minimal harus ada satu item penjualan.');
        }
    });

    $('#tanggal').on('change', () => $('.item-row').each((i, el) => updateStockInfo($(el))));
    $('#items-container').on('change', '.produk-select, .gudang-select', function() {
        const row = $(this).closest('.item-row');
        const id_produk = row.find('.produk-select').val();
        
        row.find('.dus-input-group, .satuan-input-group').hide();
        if(id_produk) {
            $.getJSON(`${selfUrl}/getprodukinfo`, { id_produk }).done(info => {
                if (info && info.satuan_per_dus > 1) {
                    row.find('.dus-input-group').show();
                }
                row.find('.satuan-input-group').show();
            });
        }
        updateStockInfo(row);
    });

    $('#items-container').on('input change', '.quantity-input', function() {
        validateStockAvailability($(this).closest('.item-row'));
        updateSummary();
    });

    $('#formPenjualan').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        btn.html('<span class="spinner"></span> Menyimpan...').prop('disabled', true);
        
        $.ajax({
            url: `${selfUrl}/simpan`,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                showAlert(response.success ? 'success' : 'error', response.message);
                if (response.success) {
                    $('#items-container').empty();
                    addRow();
                }
            },
            error: function(xhr) {
                showAlert('error', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan koneksi.');
            },
            complete: function() {
                btn.html('<i class="fas fa-save"></i> Simpan Penjualan').prop('disabled', false);
            }
        });
    });

    // Inisialisasi
    addRow();
});