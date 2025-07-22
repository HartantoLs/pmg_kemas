$(document).ready(() => {
  const baseUrl = "/laporan"

  function formatStokJS(dus, satuan) {
    dus = Number.parseInt(dus) || 0
    satuan = Number.parseInt(satuan) || 0
    if (dus === 0 && satuan === 0) return "-"
    const hasil = []
    if (dus !== 0) hasil.push(`${dus.toLocaleString("id-ID")} Dus`)
    if (satuan !== 0) hasil.push(`${satuan.toLocaleString("id-ID")} Pcs`)
    return hasil.join(", ")
  }

  function updateUI(data) {
    if (!data.success) {
      alert("Error: " + data.message)
      return
    }

    let subtitle = ""
    const filters = data.filters_info

    if (filters.tipe_laporan === "harian") {
      subtitle += `Tanggal: ${filters.tgl_laporan_formatted}`
    } else {
      subtitle += `Periode: ${filters.tgl_mulai_formatted} s/d ${filters.tgl_akhir_formatted}`
    }

    subtitle += ` | Gudang: ${filters.gudang_name}`
    if (filters.produk_name) {
      subtitle += ` | Produk: ${filters.produk_name}`
    }

    $("#header-subtitle").html(subtitle)

    const totals = data.totals
    $("#summary-saldo-awal").text(formatStokJS(totals.saldo_awal_dus, totals.saldo_awal_satuan))
    $("#summary-penerimaan").text(formatStokJS(totals.penerimaan_dus, totals.penerimaan_satuan))
    $("#summary-pengeluaran").text(formatStokJS(totals.pengeluaran_dus, totals.pengeluaran_satuan))
    $("#summary-saldo-akhir").text(formatStokJS(totals.saldo_akhir_dus, totals.saldo_akhir_satuan))

    const tableHead = $("#report-table-head")
    tableHead.empty()

    const warehouse_columns = data.warehouse_columns
    let headerHtml = `
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA PRODUK</th>
                <th rowspan="2">ISI</th>
                <th rowspan="2">SALDO AWAL</th>
                <th colspan="${warehouse_columns.length + 3}" class="group-header-penerimaan">PENERIMAAN</th>
                <th colspan="${warehouse_columns.length + 3}" class="group-header-pengeluaran">PENGELUARAN</th>
                <th rowspan="2">SALDO AKHIR</th>
            </tr>
            <tr>
                <th class="subheader-penerimaan">Produksi</th>
        `

    warehouse_columns.forEach((wh) => {
      headerHtml += `<th class="subheader-penerimaan">OP ${wh}</th>`
    })

    headerHtml += `
                <th class="subheader-penerimaan">Overpack</th>
                <th class="subheader-penerimaan">Total</th>
                <th class="subheader-pengeluaran">Jual</th>
        `

    warehouse_columns.forEach((wh) => {
      headerHtml += `<th class="subheader-pengeluaran">OP ${wh}</th>`
    })

    headerHtml += `
                <th class="subheader-pengeluaran">Overpack</th>
                <th class="subheader-pengeluaran">Total</th>
            </tr>
        `

    tableHead.html(headerHtml)

    const tableBody = $("#report-table-body")
    tableBody.empty()
    let bodyHtml = ""

    if (Object.keys(data.report_data).length === 0) {
      bodyHtml = `
                <tr>
                    <td colspan="${7 + warehouse_columns.length * 2}" style="text-align:center;padding:40px;color:#666;">
                        <i class="fas fa-inbox" style="font-size:3rem;margin-bottom:15px;color:#ddd;"></i><br>
                        Tidak ada data untuk ditampilkan.
                    </td>
                </tr>
            `
    } else {
      let no = 1
      for (const id_produk in data.report_data) {
        if (data.report_data.hasOwnProperty(id_produk)) {
          const row = data.report_data[id_produk]
          const colspan = 7 + warehouse_columns.length * 2

          if (row.error_message) {
            bodyHtml += `
                            <tr>
                                <td>${no++}</td>
                                <td class="text-left" style="font-weight:600;">${row.nama_produk}</td>
                                <td colspan="${colspan - 2}" style="text-align:center; color: #721c24; background-color: #f8d7da;">
                                    <i class="fas fa-exclamation-triangle"></i> ${row.error_message}
                                </td>
                            </tr>
                        `
          } else {
            const penerimaan_dus =
              Number.parseInt(row.produksi_dus) +
              Number.parseInt(row.op_masuk_p1_dus) +
              Number.parseInt(row.op_masuk_p2_dus) +
              Number.parseInt(row.op_masuk_p3_dus) +
              Number.parseInt(row.overpack_masuk_dus)
            const penerimaan_satuan =
              Number.parseInt(row.produksi_satuan) +
              Number.parseInt(row.op_masuk_p1_satuan) +
              Number.parseInt(row.op_masuk_p2_satuan) +
              Number.parseInt(row.op_masuk_p3_satuan) +
              Number.parseInt(row.overpack_masuk_satuan)
            const pengeluaran_dus =
              Number.parseInt(row.jual_dus) +
              Number.parseInt(row.op_keluar_p1_dus) +
              Number.parseInt(row.op_keluar_p2_dus) +
              Number.parseInt(row.op_keluar_p3_dus) +
              Number.parseInt(row.overpack_keluar_dus)
            const pengeluaran_satuan =
              Number.parseInt(row.jual_satuan) +
              Number.parseInt(row.op_keluar_p1_satuan) +
              Number.parseInt(row.op_keluar_p2_satuan) +
              Number.parseInt(row.op_keluar_p3_satuan) +
              Number.parseInt(row.overpack_keluar_satuan)
            const saldo_akhir_dus = Number.parseInt(row.saldo_awal_dus) + penerimaan_dus - pengeluaran_dus
            const saldo_akhir_satuan = Number.parseInt(row.saldo_awal_satuan) + penerimaan_satuan - pengeluaran_satuan

            bodyHtml += `
                            <tr>
                                <td>${no++}</td>
                                <td class="text-left" style="font-weight:600;">${row.nama_produk}</td>
                                <td>${row.isi}</td>
                                <td>${formatStokJS(row.saldo_awal_dus, row.saldo_awal_satuan)}</td>
                                <td>${formatStokJS(row.produksi_dus, row.produksi_satuan)}</td>
                        `

            warehouse_columns.forEach((wh) => {
              const key = wh.toLowerCase()
              bodyHtml += `<td>${formatStokJS(row["op_masuk_" + key + "_dus"], row["op_masuk_" + key + "_satuan"])}</td>`
            })

            bodyHtml += `
                                <td>${formatStokJS(row.overpack_masuk_dus, row.overpack_masuk_satuan)}</td>
                                <td style="font-weight:700;background:#e8f5e8;">${formatStokJS(penerimaan_dus, penerimaan_satuan)}</td>
                                <td>${formatStokJS(row.jual_dus, row.jual_satuan)}</td>
                        `

            warehouse_columns.forEach((wh) => {
              const key = wh.toLowerCase()
              bodyHtml += `<td>${formatStokJS(row["op_keluar_" + key + "_dus"], row["op_keluar_" + key + "_satuan"])}</td>`
            })

            bodyHtml += `
                                <td>${formatStokJS(row.overpack_keluar_dus, row.overpack_keluar_satuan)}</td>
                                <td style="font-weight:700;background:#fde8e8;">${formatStokJS(pengeluaran_dus, pengeluaran_satuan)}</td>
                                <td style="font-weight:700;">${formatStokJS(saldo_akhir_dus, saldo_akhir_satuan)}</td>
                            </tr>
                        `
          }
        }
      }

      // Total row
      bodyHtml += `
                <tr class="total-row">
                    <td colspan="3"><strong>TOTAL KESELURUHAN</strong></td>
                    <td><strong>${formatStokJS(totals.saldo_awal_dus, totals.saldo_awal_satuan)}</strong></td>
                    <td><strong>${formatStokJS(totals.produksi_dus, totals.produksi_satuan)}</strong></td>
            `

      warehouse_columns.forEach(() => {
        bodyHtml += `<td><strong>-</strong></td>`
      })

      bodyHtml += `
                    <td><strong>-</strong></td>
                    <td><strong>${formatStokJS(totals.penerimaan_dus, totals.penerimaan_satuan)}</strong></td>
                    <td><strong>-</strong></td>
            `

      warehouse_columns.forEach(() => {
        bodyHtml += `<td><strong>-</strong></td>`
      })

      bodyHtml += `
                    <td><strong>-</strong></td>
                    <td><strong>${formatStokJS(totals.pengeluaran_dus, totals.pengeluaran_satuan)}</strong></td>
                    <td><strong>${formatStokJS(totals.saldo_akhir_dus, totals.saldo_akhir_satuan)}</strong></td>
                </tr>
            `
    }

    tableBody.html(bodyHtml)

    const infoBox = $("#info-box-container")
    if (filters.report_data_count > 0) {
      let infoText = `<p>Menampilkan ${filters.report_data_count} produk dengan total agregasi dari ${filters.gudang_name}.</p>`
      if (filters.filter_gudang !== "semua") {
        infoText += `<p>Kolom OP menunjukkan mutasi antar gudang secara detail untuk memberikan visibilitas yang lebih baik terhadap pergerakan stok.</p>`
      }
      $("#info-box-content").html(`<h4>📊 Ringkasan Laporan</h4>${infoText}`)
      infoBox.show()
    } else {
      infoBox.hide()
    }
  }

  function fetchReportData() {
    const formData = $("#filterForm").serialize()

    $.ajax({
      url: `${baseUrl}/getmutasidata`,
      type: "GET",
      data: formData,
      dataType: "json",
      success: (data) => {
        updateUI(data)
      },
      error: (jqXHR, textStatus, errorThrown) => {
        alert("Gagal memuat data. Cek Konsol (F12) untuk detail error.")
        console.error(jqXHR.responseText)
      },
    })
  }

  function toggleDateFilters() {
    const type = $("#tipe_laporan").val()
    if (type === "harian") {
      $("#filter-harian").show()
      $("#filter-rekap-mulai, #filter-rekap-akhir").hide()
    } else {
      $("#filter-harian").hide()
      $("#filter-rekap-mulai, #filter-rekap-akhir").show()
    }
  }

  // Event handlers
  toggleDateFilters()
  $("#tipe_laporan").on("change", toggleDateFilters)

  $(".data-filter").on("change", () => {
    fetchReportData()
  })

  $("#refresh-button").on("click", (e) => {
    e.preventDefault()
    fetchReportData()
  })

  // Export function
  function exportExcel() {
    alert("Fitur export Excel akan segera tersedia!")
  }

  window.exportExcel = exportExcel

  // Initial load
  fetchReportData()
})
