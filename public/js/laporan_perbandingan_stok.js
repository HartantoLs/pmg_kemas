$(document).ready(() => {
    // === PENGATURAN & VARIABEL ===
    const container = $(".container");
    // [IMPROVEMENT] Mengambil base URL secara dinamis dari atribut HTML
    const baseUrl = container.data("base-url");
    let debounceTimeout;

    // === FUNGSI HELPERS ===

    /**
     * Mengambil parameter filter dari form. Mencegah pengulangan kode.
     * @returns {string} Serialized form data.
     */
    function getFilterParams() {
        return $("#filterForm").serialize();
    }

    /**
     * Menampilkan atau menyembunyikan overlay loading.
     * @param {boolean} show - True untuk menampilkan, false untuk menyembunyikan.
     */
    function toggleLoading(show) {
        $("#loading-overlay").toggle(show);
    }

    /**
     * Fungsi format selisih untuk kolom tabel.
     */
    function formatSelisihJS(selisih) {
        const num = parseInt(selisih, 10);
        const formattedNum = num.toLocaleString("id-ID");
        if (num > 0) return `<span class='selisih-plus'><i class='fas fa-arrow-up'></i>+${formattedNum}</span>`;
        if (num < 0) return `<span class='selisih-minus'><i class='fas fa-arrow-down'></i>${formattedNum}</span>`;
        return `<span class='selisih-zero'><i class='fas fa-minus'></i>0</span>`;
    }

    /**
     * Fungsi untuk menampilkan notifikasi toast.
     */
    function showNotification(message, type = "success") {
        const toastContainer = $("#notification-toast");
        const toastId = "toast-" + Date.now();
        const icon = type === "success" ? "fa-check-circle" : "fa-exclamation-circle";
        const toastElement = $(`
            <div id="${toastId}" class="toast-message ${type}">
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            </div>
        `);

        toastContainer.append(toastElement);
        setTimeout(() => toastElement.addClass("show"), 100);
        setTimeout(() => {
            toastElement.removeClass("show");
            setTimeout(() => toastElement.remove(), 500);
        }, 4000);
    }

    // === FUNGSI UTAMA ===

    /**
     * Mengupdate seluruh UI berdasarkan data dari AJAX.
     * @param {object} data - Objek response dari server.
     */
    function updateUI(data) {
        if (!data.success) {
            showNotification(data.message || "Gagal mengambil data", "error");
            return;
        }

        const { stats, filters, report_data } = data;

        // Update Stats Cards
        $("#stat-total-records").text(stats.total_records.toLocaleString("id-ID"));
        $("#stat-records-with-difference").text(stats.records_with_difference.toLocaleString("id-ID"));
        $("#stat-total-selisih-dus").text(stats.total_selisih_dus.toLocaleString("id-ID"));
        $("#stat-total-selisih-satuan").text(stats.total_selisih_satuan.toLocaleString("id-ID"));

        // Update Info Alert & Headers
        $("#info-gudang").text(filters.gudang_name);
        $("#info-produk").text(filters.produk_name);
        $("#info-tanggal").text(filters.tanggal);
        $("#header-date").html(`<i class="fas fa-calendar-day"></i> Tanggal: ${filters.tanggal}`);
        $("#table-record-count").text(`${stats.total_records} Records`);

        // Update Table Body
        const tableBody = $("#report-table-body");
        tableBody.empty();

        if (report_data.length === 0) {
            tableBody.html(`
                <tr>
                    <td colspan="9" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Tidak Ada Data Ditemukan</h3>
                        <p>Silakan coba ubah filter atau tanggal pengecekan.</p>
                    </td>
                </tr>
            `);
        } else {
            const rowsHtml = report_data.map((row, index) => {
                const selisihClass = row.selisih_dus != 0 || row.selisih_satuan != 0 ? "row-selisih" : "";
                return `
                    <tr class="${selisihClass}">
                        <td><div class="row-number">${index + 1}</div></td>
                        <td class="text-left"><div class="product-name-cell">${row.nama_produk}</div></td>
                        <td class="text-left"><span class="warehouse-badge">${row.nama_gudang}</span></td>
                        <td class="col-sistem"><div class="stock-number">${parseInt(row.sistem_dus, 10).toLocaleString("id-ID")}</div></td>
                        <td class="col-sistem"><div class="stock-number">${parseInt(row.sistem_satuan, 10).toLocaleString("id-ID")}</div></td>
                        <td class="col-fisik"><div class="stock-number">${parseInt(row.fisik_dus, 10).toLocaleString("id-ID")}</div></td>
                        <td class="col-fisik"><div class="stock-number">${parseInt(row.fisik_satuan, 10).toLocaleString("id-ID")}</div></td>
                        <td class="col-selisih">${formatSelisihJS(row.selisih_dus)}</td>
                        <td class="col-selisih">${formatSelisihJS(row.selisih_satuan)}</td>
                    </tr>
                `;
            }).join('');
            tableBody.html(rowsHtml);
        }
    }

    /**
     * Fungsi utama untuk mengambil data via AJAX.
     */
    function fetchReportData() {
        $.ajax({
            url: `${baseUrl}/getcomparisondata`,
            type: "GET",
            data: getFilterParams(), // [IMPROVEMENT] Menggunakan fungsi helper
            dataType: "json",
            // beforeSend: () => toggleLoading(true), // [IMPROVEMENT] Tampilkan loading
            success: (data) => updateUI(data),
            error: (jqXHR, textStatus, errorThrown) => {
                console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
                showNotification("Gagal memuat data. Silakan coba lagi.", "error");
                $("#report-table-body").html(`
                    <tr>
                        <td colspan="9" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle"></i> Gagal memuat data. Periksa koneksi atau hubungi administrator.
                        </td>
                    </tr>
                `);
            },
            complete: () => toggleLoading(false) // [IMPROVEMENT] Sembunyikan loading setelah selesai (baik sukses maupun error)
        });
    }

    // === EVENT HANDLERS ===

    // Event handlers untuk filter dengan debounce
    $(".filter-input, .filter-select").on("change", () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(fetchReportData, 300);
    });

    // Fungsi export CSV
    window.exportToCSV = () => {
        const printUrl = `${baseUrl}/exportcsv?${getFilterParams()}`;
        window.location.href = printUrl; 
        showNotification("Export CSV dimulai...", "success");
    };

    // Fungsi print laporan
    window.printLaporan = () => {
        const printUrl = `${baseUrl}/printlaporan?${getFilterParams()}`;
        window.open(printUrl, "_blank");
    };

    // === INISIALISASI ===
    fetchReportData(); 
});