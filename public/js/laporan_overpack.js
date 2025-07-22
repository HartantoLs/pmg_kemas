$(document).ready(() => {
  const selfUrl = `${baseUrl}/laporan`;

  function formatStok(pcs, satuan_per_dus) {
    if (pcs == 0) return "-"

    // Jika satuan per dus adalah 1, hanya tampilkan Pcs
    if (satuan_per_dus <= 1) {
      return Number(pcs).toLocaleString("id-ID") + " Pcs"
    }

    const dus = Math.floor(pcs / satuan_per_dus)
    const sisa_pcs = pcs % satuan_per_dus

    const hasil = []
    if (dus > 0) hasil.push(Number(dus).toLocaleString("id-ID") + " Dus")
    if (sisa_pcs > 0) hasil.push(Number(sisa_pcs).toLocaleString("id-ID") + " Pcs")

    return hasil.length > 0 ? hasil.join(" ") : "-"
  }

  function updateUI(data) {
    if (!data.success) {
      alert("Error: " + data.message)
      return
    }

    const filters = data.filters_info
    const reportData = data.report_data
    const grandTotals = data.grand_totals

    // Update date info
    let dateText = ""
    if (filters.tipe_laporan === "harian") {
      dateText = `Menampilkan status stok hingga tanggal: <strong>${new Date(filters.tanggal).toLocaleDateString(
        "id-ID",
        {
          day: "2-digit",
          month: "long",
          year: "numeric",
        },
      )}</strong>`
    } else {
      dateText = `Menampilkan rekapitulasi periode: <strong>${new Date(filters.tanggal_mulai).toLocaleDateString(
        "id-ID",
        {
          day: "2-digit",
          month: "long",
          year: "numeric",
        },
      )}</strong> s/d <strong>${new Date(filters.tanggal_akhir).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
      })}</strong>`
    }
    $("#date-info-text").html(dateText)

    // Generate table
    let tableHtml = ""

    if (filters.tipe_laporan === "harian") {
      tableHtml = `
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th class="text-left">NAMA PRODUK</th>
                            <th>ISI/DUS</th>
                            <th>BELUM DISELEKSI</th>
                            <th>SIAP DIKEMAS</th>
                            <th>SUDAH DIKEMAS</th>
                            <th>TOTAL CURAH</th>
                            <th>TOTAL OVERPACK</th>
                        </tr>
                    </thead>
                    <tbody>
            `

      if (reportData.length === 0) {
        tableHtml += `
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <h3>Tidak Ada Data</h3>
                                    <p>Tidak ada data stok overpack sesuai filter yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                `
      } else {
        reportData.forEach((row, index) => {
          tableHtml += `
                            <tr>
                                <td>${index + 1}</td>
                                <td class="text-left" style="font-weight:600;">${row.nama_produk}</td>
                                <td style="font-weight:600; color: #ff6b35;">${Number(row.satuan_per_dus).toLocaleString("id-ID")}</td>
                                <td>
                                    <div class="stok-display">
                                        <div class="stok-dus">${formatStok(row.belum_seleksi, row.satuan_per_dus)}</div>
                                        <div class="stok-pcs">${Number(row.belum_seleksi).toLocaleString("id-ID")} pcs</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="stok-display">
                                        <div class="stok-dus">${formatStok(row.siap_kemas, row.satuan_per_dus)}</div>
                                        <div class="stok-pcs">${Number(row.siap_kemas).toLocaleString("id-ID")} pcs</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="stok-display">
                                        <div class="stok-dus">${formatStok(row.sudah_kemas, row.satuan_per_dus)}</div>
                                        <div class="stok-pcs">${Number(row.sudah_kemas).toLocaleString("id-ID")} pcs</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="stok-display">
                                        <div class="stok-dus">${formatStok(row.total_curah, row.satuan_per_dus)}</div>
                                        <div class="stok-pcs">${Number(row.total_curah).toLocaleString("id-ID")} pcs</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="stok-display">
                                        <div class="stok-dus" style="color:#ff6b35;">${formatStok(row.total_keseluruhan, row.satuan_per_dus)}</div>
                                        <div class="stok-pcs">${Number(row.total_keseluruhan).toLocaleString("id-ID")} pcs</div>
                                    </div>
                                </td>
                            </tr>
                        `
        })
      }

      tableHtml += `</tbody>`

      if (reportData.length > 0) {
        tableHtml += `
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-left"><strong>GRAND TOTAL (DALAM PCS)</strong></td>
                            <td><strong>${Number(grandTotals.belum_seleksi || 0).toLocaleString("id-ID")}</strong></td>
                            <td><strong>${Number(grandTotals.siap_kemas || 0).toLocaleString("id-ID")}</strong></td>
                            <td><strong>${Number(grandTotals.sudah_kemas || 0).toLocaleString("id-ID")}</strong></td>
                            <td><strong>${Number(grandTotals.total_curah || 0).toLocaleString("id-ID")}</strong></td>
                            <td><strong>${Number(grandTotals.total_keseluruhan || 0).toLocaleString("id-ID")}</strong></td>
                        </tr>
                    </tfoot>
                `
      }

      tableHtml += `</table>`
    } else {
      // Rekap
      tableHtml = `
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th class="text-left">NAMA PRODUK</th>
                            <th>ISI/DUS</th>
                            <th>TOTAL MASUK</th>
                            <th>SELEKSI AMAN</th>
                            <th>SELEKSI CURAH</th>
                            <th>KEMAS ULANG</th>
                        </tr>
                    </thead>
                    <tbody>
            `

      if (reportData.length === 0) {
        tableHtml += `
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <h3>Tidak Ada Pergerakan</h3>
                                    <p>Tidak ada pergerakan stok overpack pada periode ini.</p>
                                </div>
                            </td>
                        </tr>
                `
      } else {
        reportData.forEach((row, index) => {
          tableHtml += `
                            <tr>
                                <td>${index + 1}</td>
                                <td class="text-left" style="font-weight:600;">${row.nama_produk}</td>
                                <td style="font-weight:600; color: #ff6b35;">${Number(row.satuan_per_dus).toLocaleString("id-ID")}</td>
                                <td>
                                    <div class="stok-display">
                                        <div class="stok-dus">${formatStok(row.total_masuk, row.satuan_per_dus)}</div>
                                        <div class="stok-pcs">${Number(row.total_masuk).toLocaleString("id-ID")} pcs</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="stok-display">
                                        <div class="stok-dus">${formatStok(row.total_aman, row.satuan_per_dus)}</div>
                                        <div class="stok-pcs">${Number(row.total_aman).toLocaleString("id-ID")} pcs</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="stok-display">
                                        <div class="stok-dus">${formatStok(row.total_curah, row.satuan_per_dus)}</div>
                                        <div class="stok-pcs">${Number(row.total_curah).toLocaleString("id-ID")} pcs</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="stok-display">
                                        <div class="stok-dus">${formatStok(row.total_kemas, row.satuan_per_dus)}</div>
                                        <div class="stok-pcs">${Number(row.total_kemas).toLocaleString("id-ID")} pcs</div>
                                    </div>
                                </td>
                            </tr>
                        `
        })
      }

      tableHtml += `</tbody>`

      if (reportData.length > 0) {
        tableHtml += `
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-left"><strong>GRAND TOTAL (DALAM PCS)</strong></td>
                            <td><strong>${Number(grandTotals.total_masuk || 0).toLocaleString("id-ID")}</strong></td>
                            <td><strong>${Number(grandTotals.total_aman || 0).toLocaleString("id-ID")}</strong></td>
                            <td><strong>${Number(grandTotals.total_curah || 0).toLocaleString("id-ID")}</strong></td>
                            <td><strong>${Number(grandTotals.total_kemas || 0).toLocaleString("id-ID")}</strong></td>
                        </tr>
                    </tfoot>
                `
      }

      tableHtml += `</table>`
    }

    $("#table-content").html(tableHtml)
  }

  function fetchData() {
    const formData = $("#filterForm").serialize()

    $.ajax({
      url: `${selfUrl}/getoverpackdata`,
      type: "GET",
      data: formData,
      dataType: "json",
      success: (data) => {
        updateUI(data)
      },
      error: (jqXHR, textStatus, errorThrown) => {
        console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText)
        $("#table-content").html(
          '<div style="text-align: center; padding: 3rem; color: #e74c3c;"><i class="fas fa-exclamation-triangle fa-2x"></i><br><br>Gagal memuat data. Silakan coba lagi.</div>',
        )
      },
    })
  }

  function toggleDateFilters() {
    const type = $("#tipe_laporan").val()
    if (type === "harian") {
      $(".filter-harian").show()
      $(".filter-rekap").hide()
    } else {
      $(".filter-harian").hide()
      $(".filter-rekap").show()
    }
  }

  // Event handlers
  toggleDateFilters()
  $("#tipe_laporan").on("change", toggleDateFilters)

  // Submit form on any filter change
  $(".form-filter").on("change", () => {
    fetchData()
  })

  // Export function
  window.exportCSV = () => {
    const formData = $("#filterForm").serialize()

    // Create a form and submit it
    const form = $("<form>", {
      method: "POST",
      action: `${selfUrl}/exportoverpackcsv`,
    })

    // Add form data as hidden inputs
    const formArray = $("#filterForm").serializeArray()
    formArray.forEach((item) => {
      form.append(
        $("<input>", {
          type: "hidden",
          name: item.name,
          value: item.value,
        }),
      )
    })

    $("body").append(form)
    form.submit()
    form.remove()
  }

  // Initial load
  fetchData()
})
