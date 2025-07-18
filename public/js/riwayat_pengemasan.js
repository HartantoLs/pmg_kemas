$(document).ready(function() {
    const modal = $('#editModal');
    const dataTableBody = $('#dataTableBody');
    const loadingState = $('#loadingState');
    const baseUrl = "/pengemasan"; 

    function showNotification(message, type = 'success') {
        const toastContainer = $('#notification-toast');
        const toastId = 'toast-' + Date.now();
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const toastElement = $(`<div id="${toastId}" class="toast-message ${type}"><i class="fas ${icon}"></i><span>${message}</span></div>`);
        
        toastContainer.append(toastElement);
        setTimeout(() => toastElement.addClass('show'), 100);
        setTimeout(() => {
            toastElement.removeClass('show');
            setTimeout(() => toastElement.remove(), 500);
        }, 4000);
    }

    function fetchFilteredData() {
        loadingState.show();
        dataTableBody.html('');
        $.post(`${baseUrl}/filterriwayat`, {
            tanggal_mulai: $('#tanggal_mulai').val(),
            tanggal_akhir: $('#tanggal_akhir').val(),
            gudang_id: $('#gudang_id').val(),
            produk_id: $('#produk_id').val(),
        }).done(function(response) {
            dataTableBody.html(response);
            updateRowCount();
        }).fail(function() {
            dataTableBody.html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
        }).always(function() {
            loadingState.hide();
        });
    }

    function updateRowCount() {
        const visibleRows = dataTableBody.find('tr').length;
        $('#totalRows').text(visibleRows);
    }
    
    // --- FUNGSI VALIDASI DIHAPUS ---

    // --- Event Handlers ---
    $('.form-filter').on('change', fetchFilteredData);

    $('body').on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        $.post(`${baseUrl}/getdetailriwayat`, { id: id }, function(response) {
            if (response.success && response.data) {
                const data = response.data;
                $('#editId').val(data.id);
                $('#editNamaProduk').text(data.nama_produk);
                $('#editNamaGudang').text(data.nama_gudang);
                $('#editJumlahDus').val(data.jumlah_dus).removeClass('input-error');
                $('#editJumlahSatuan').val(data.jumlah_satuan).removeClass('input-error');
                
                const stokHistorisText = `${data.stok_gudang_historis_dus} Dus, ${data.stok_gudang_historis_satuan} Satuan`;
                $('#editStokInfo').text(stokHistorisText);
                
                // Pastikan tombol submit selalu aktif
                $('#submitEdit').prop('disabled', false);

            
                modal.show();

                // ========================================================
                const satuanPerDus = parseInt(data.satuan_per_dus) || 0;
                const dusInput = $('#editJumlahDus');
                const satuanInput = $('#editJumlahSatuan');

                // Reset status kunci dan styling
                dusInput.prop('readonly', false).removeClass('input-locked');
                satuanInput.prop('readonly', false).removeClass('input-locked');

                if (satuanPerDus > 1) {
                    // Kunci input SATUAN dan tambahkan kelas opacity
                    satuanInput.prop('readonly', true).addClass('input-locked');
                } else {
                    // Kunci input DUS dan tambahkan kelas opacity
                    dusInput.prop('readonly', true).addClass('input-locked');
                }
                // ========================================================
                
                modal.show();
            } else {
                showNotification(response.message || 'Gagal mengambil data detail.', 'error');
            }
        }, 'json').always(() => {
            btn.html('<i class="fas fa-edit"></i> Edit').prop('disabled', false);
        });
    });
    
    $('body').on('click', '.btn-delete', function() {
        if (!confirm('Anda yakin ingin menghapus riwayat ini? Stok akan dikembalikan ke gudang.')) return;
        
        const id = $(this).data('id');
        const btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        $.post(`${baseUrl}/hapusriwayat`, { id: id }, function(response) {
            if (response.success) { 
                showNotification(response.message, 'success');
                $('#row-' + id).fadeOut(500, function() {
                    $(this).remove();
                    updateRowCount();
                });
            } else {
                showNotification(response.message, 'error');
                btn.html('<i class="fas fa-trash"></i> Hapus').prop('disabled', false);
            }
        }, 'json')
        .fail(function() {
            showNotification('Terjadi kesalahan koneksi.', 'error');
            btn.html('<i class="fas fa-trash"></i> Hapus').prop('disabled', false);
        });
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#submitEdit');
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
        
        $.post(`${baseUrl}/updateriwayat`, $(this).serialize(), function(response) {
            if (response.success) {
                modal.hide();
                showNotification(response.message, 'success');
                fetchFilteredData();
            } else {
                showNotification(response.message, 'error');
            }
        }, 'json')
        .fail(() => showNotification('Terjadi kesalahan koneksi.', 'error'))
        .always(() => submitBtn.html('<i class="fas fa-save"></i> Update Data').prop('disabled', false));
    });

    $('.close').on('click', () => modal.hide());
    $(window).on('click', (event) => { if ($(event.target).is(modal)) modal.hide(); });

    fetchFilteredData();
});