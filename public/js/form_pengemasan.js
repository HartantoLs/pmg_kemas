(function() {
    let itemCounter = 0;
    const selfUrl = "/pengemasan";

    function tambahItem() {
        const itemIndex = itemCounter++;
        const newItem = $(`
            <div class="item-card" data-index="${itemIndex}">
                <div class="item-grid">
                    <div class="form-group">
                        <label><i class="fas fa-warehouse"></i> Gudang</label>
                        <select name="items[${itemIndex}][gudang]" class="gudang-select" required><option value="">Memuat...</option></select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-cog"></i> Mesin</label>
                        <select name="items[${itemIndex}][mesin]" class="mesin-select" required><option value="">-- Pilih Gudang --</option></select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-industry"></i> Jenis Produksi</label>
                        <select name="items[${itemIndex}][jenis_produksi]" class="jenis-produksi-select" required><option value="">Memuat...</option></select>
                    </div>
                    <div class="form-group">
                        <label class="jumlah-label"><i class="fas fa-sort-numeric-up"></i> Jumlah</label>
                        <input type="number" name="items[${itemIndex}][jumlah]" value="0" min="0" class="jumlah-input" required>
                    </div>
                    <div class="bahan-info">
                        <h5><i class="fas fa-list"></i> Kebutuhan Bahan:</h5>
                        <div class="bahan-content">Pilih jenis produksi dan masukkan jumlah</div>
                    </div>
                    <div class="form-group" style="align-self: center;">
                        <button type="button" class="btn btn-danger btn-hapus"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `);
        $('#items-container').append(newItem);
        loadGudangOptions(newItem.find('.gudang-select'));
        loadJenisProduksiOptions(newItem.find('.jenis-produksi-select'));
    }

    function loadGudangOptions(selectElement) {
        $.get(`${selfUrl}/getgudang`).done(data => selectElement.html(data))
            .fail(() => selectElement.html('<option value="">Gagal memuat</option>'));
    }

    function loadJenisProduksiOptions(selectElement) {
        $.get(`${selfUrl}/getjenisproduksi`).done(data => selectElement.html(data))
            .fail(() => selectElement.html('<option value="">Gagal memuat</option>'));
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
            let bahanHtml = data.bahan_baku.map(item => {
                let total = (parseFloat(item.jumlah) * jumlah);
                return `<div class="bahan-item"><strong>${item.nama_barang}:</strong> <span>${total.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 2})}</span></div>`;
            }).join('');
            bahanContent.html(bahanHtml);
        } else {
            bahanContent.html('Pilih jenis produksi dan masukkan jumlah');
        }
    }

    function showAlert(type, message) {
        const formMessages = $('#form-messages');
        formMessages.removeClass('alert-error alert-success').addClass(`alert-${type}`);
        formMessages.text(message).show();
        $('html, body').animate({ scrollTop: 0 }, 500);
    }

    // --- Event Handlers ---
    $(document).on('click', '.btn-hapus', function() {
        if ($('.item-card').length > 1) {
            $(this).closest('.item-card').remove();
        }
    });

    $(document).on('change', '.gudang-select', function() {
        const mesinSelect = $(this).closest('.item-card').find('.mesin-select');
        const namaGudang = $(this).find('option:selected').data('nama-gudang');
        mesinSelect.html('<option>Memuat...</option>');
        if (namaGudang) {
            $.get(`${selfUrl}/getmesin?nama_gudang=${encodeURIComponent(namaGudang)}`)
                .done(data => mesinSelect.html(data))
                .fail(() => mesinSelect.html('<option>Gagal</option>'));
        } else {
            mesinSelect.html('<option>-- Pilih Gudang --</option>');
        }
    });

    $(document).on('change', '.jenis-produksi-select', function() {
        const itemCard = $(this).closest('.item-card');
        const nomJenisProduksi = $(this).val();
        if (nomJenisProduksi > 0) {
            $.getJSON(`${selfUrl}/getinfoproduksi?nom_jenis_produksi=${nomJenisProduksi}`)
                .done(data => {
                    itemCard.find('.jenis-produksi-select').data('resep', data);
                    displayBahanBaku(itemCard);
                });
        } else {
            itemCard.find('.jumlah-label').html('<i class="fas fa-sort-numeric-up"></i> Jumlah');
            itemCard.find('.bahan-content').html('Pilih jenis produksi dan masukkan jumlah');
        }
    });

    $(document).on('keyup change', '.jumlah-input', function() {
        displayBahanBaku($(this).closest('.item-card'));
    });

    // Inisialisasi dan form submission
    $(document).ready(function() {
        tambahItem();
        $('#btn-tambah-item').click(tambahItem);

        $('#pengemasan-form').on('submit', function(event) {
            event.preventDefault();
            const submitBtn = $('.submit-btn');
            submitBtn.prop('disabled', true).html('<span class="spinner"></span> Menyimpan...');
            
            $.ajax({
                type: 'POST',
                url: `${selfUrl}/simpan`,
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    showAlert('success', response.message);
                    $('#items-container').empty();
                    tambahItem();
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                    showAlert('error', errorMsg);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Semua Data');
                }
            });
        });
    });
})();