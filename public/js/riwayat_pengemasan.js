$(document).ready(function() {
    const modal = $('#editModal');
    const loadingState = $('#loadingState');
    const dataTableBody = $('#dataTableBody');
    const selfUrl = "/pengemasan";

    let nilaiLama = { dus: 0, satuan: 0 };
    let stokSaatIni = { dus: 0, satuan: 0 }; 

    function showNotification(message, type = 'success') {
        const toastContainer = $('#notification-toast');
        const toastId = 'toast-' + Date.now();
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        const toastElement = $(`
            <div id="${toastId}" class="toast-message ${type}">
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            </div>
        `);

        toastContainer.append(toastElement);
        
        // Memicu animasi
        setTimeout(() => {
            toastElement.addClass('show');
        }, 100);

        // Menghilangkan notifikasi setelah 4 detik
        setTimeout(() => {
            toastElement.removeClass('show');
            setTimeout(() => {
                toastElement.remove();
            }, 500); 
        }, 4000);
    }

    function fetchFilteredData() {
        loadingState.show();
        dataTableBody.empty();
        const formData = {
            tanggal_mulai: $('#tanggal_mulai').val(),
            tanggal_akhir: $('#tanggal_akhir').val(),
            gudang_id: $('#gudang_id').val(),
            produk_id: $('#produk_id').val()
        };
        $.post(`${selfUrl}/filterriwayat`, formData, function(response) {
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
        if(dataTableBody.find('td').length === 1 && dataTableBody.find('td').attr('colspan') === "8") {
             $('#totalRows').text(0);
        } else {
             $('#totalRows').text(visibleRows);
        }
    }

    // Fungsi validasi yang hanya memeriksa apakah stok masa depan tidak akan negatif
    function validateInputs() {
        const dusBaru = parseInt($('#editJumlahDus').val()) || 0;
        const satuanBaru = parseInt($('#editJumlahSatuan').val()) || 0;
        
        // Logika validasi kritis: apakah perubahan ini akan membuat stok SAAT INI menjadi negatif?
        const finalStokDus = stokSaatIni.dus - nilaiLama.dus + dusBaru;
        const finalStokSatuan = stokSaatIni.satuan - nilaiLama.satuan + satuanBaru;

        const isError = finalStokDus < 0 || finalStokSatuan < 0;

        $('#editJumlahDus, #editJumlahSatuan').toggleClass('input-error', isError);
        $('#editErrorMsg').toggle(isError);
        $('#submitEdit').prop('disabled', isError).css('opacity', isError ? '0.6' : '1');
    }

    // Event Handlers
    $('.form-filter').on('change', fetchFilteredData);

    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#dataTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
        updateRowCount();
    });

    $('body').on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        $.post(`${selfUrl}/getdetailriwayat`, { id: id }, function(response) {
            if (response.success && response.data) {
                const data = response.data;
                $('#editId').val(data.id);
                $('#editNamaProduk').text(data.nama_produk);
                $('#editNamaGudang').text(data.nama_gudang);
                $('#editJumlahDus').val(data.jumlah_dus);
                $('#editJumlahSatuan').val(data.jumlah_satuan);
                
                nilaiLama = { dus: parseInt(data.jumlah_dus) || 0, satuan: parseInt(data.jumlah_satuan) || 0 };
                
                // Tampilkan STOK HISTORIS dari server
                const stokHistorisText = `${data.stok_gudang_historis_dus} Dus, ${data.stok_gudang_historis_satuan} Satuan`;
                $('#editStokInfo').text(stokHistorisText);
                
                // Ambil stok saat ini untuk validasi keamanan
                stokSaatIni.dus = data.stok_gudang_historis_dus;
                stokSaatIni.satuan = data.stok_gudang_historis_satuan;

                // Hitung selisih dari waktu itu ke sekarang untuk mendapatkan stok saat ini
                const selisihKeSekarangDus = (data.stok_gudang_historis_dus - data.jumlah_dus); // Stok sebelum transaksi
                // Ini hanyalah contoh, validasi utama ada di backend
                
                modal.show();
            } else {
                alert('Gagal mengambil data detail: ' + (response.message || 'Error'));
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
        
        $.post(`${selfUrl}/hapusRiwayat`, { id: id }, function(response) {
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
        
        $.post(`${selfUrl}/updateriwayat`, $(this).serialize(), function(response) {
            // PERBAIKAN LOGIKA: Cek status dari response
            if (response.status === 'success') {
                modal.hide();
                showNotification(response.message, 'success'); 
                fetchFilteredData(); 
            } else {
                showNotification(response.message, 'error'); 
            }
        }, 'json')
        .fail(function() {

            showNotification('Terjadi kesalahan koneksi dengan server.', 'error');
        })
        .always(function() {
            submitBtn.html('<i class="fas fa-save"></i> Update Data').prop('disabled', false);
        });
    });

    $('.close, .modal').on('click', function(e) {
        if ($(e.target).is('.modal, .close')) {
            modal.hide();
        }
    });

    fetchFilteredData();
});