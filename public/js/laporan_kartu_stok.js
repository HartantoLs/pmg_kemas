// Declare the $ variable before using it
const $ = window.jQuery || window.$

$(document).ready(() => {
  const selfUrl = `${baseUrl}/laporan`;
  let currentData = null

  function showLoading() {
    $("#loading-overlay").show()
  }

  function hideLoading() {
    $("#loading-overlay").hide()
  }

  function updateUI(data) {
    if (!data.success) {
      alert("Error: " + data.message)
      return
    }

    const result = data.data
    currentData = result

    // Update info cards
    $("#info-produk").text(result.produk_info.nama_produk)
    $("#info-gudang").text(result.gudang_name)
    $("#info-periode").text(result.periode)
    $("#info-total-transaksi").text(result.total_transaksi)

    // Update table title
    $("#table-title").text(result.produk_info.nama_produk)

    // Show info cards
    $("#info-cards").show()
    $("#alert-pilih-produk").hide()

    // Generate table content
    let tableHtml = ""

    // Saldo awal
    tableHtml += `
            <tr class="saldo-row">
                <td colspan="7" class="text-left">
                    <i class="fas fa-play-circle"></i> SALDO AWAL PER ${new Date(result.periode.split(" - ")[0].split("/").reverse().join("-")).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }).toUpperCase()}
                </td>
                <td>${Number(result.saldo_awal.dus).toLocaleString("id-ID")}</td>
                <td>${Number(result.saldo_awal.satuan).toLocaleString("id-ID")}</td>
            </tr>
        `

    // Transaksi
    let running_dus = result.saldo_awal.dus
    let running_satuan = result.saldo_awal.satuan

    if (result.transaksi.length === 0) {
      tableHtml += `
                <tr>
                    <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-gray);">
                        Tidak ada transaksi pada rentang tanggal dan filter yang dipilih.
                    </td>
                </tr>
            `
    } else {
      result.transaksi.forEach((row) => {
        const masuk_dus = row.perubahan_dus > 0 ? row.perubahan_dus : 0
        const masuk_satuan = row.perubahan_satuan > 0 ? row.perubahan_satuan : 0
        const keluar_dus = row.perubahan_dus < 0 ? Math.abs(row.perubahan_dus) : 0
        const keluar_satuan = row.perubahan_satuan < 0 ? Math.abs(row.perubahan_satuan) : 0

        running_dus += Number.parseInt(row.perubahan_dus)
        running_satuan += Number.parseInt(row.perubahan_satuan)

        tableHtml += `
                    <tr>
                        <td>${new Date(row.tanggal_transaksi).toLocaleDateString("id-ID")}</td>
                        <td><span style="background: var(--light-orange); padding: 0.25rem 0.5rem; border-radius: 6px; font-weight: 600;">${row.gudang_id}</span></td>
                        <td class="text-left">${row.tipe_transaksi}</td>
                        <td class="masuk">${masuk_dus > 0 ? Number(masuk_dus).toLocaleString("id-ID") : "-"}</td>
                        <td class="masuk">${masuk_satuan > 0 ? Number(masuk_satuan).toLocaleString("id-ID") : "-"}</td>
                        <td class="keluar">${keluar_dus > 0 ? Number(keluar_dus).toLocaleString("id-ID") : "-"}</td>
                        <td class="keluar">${keluar_satuan > 0 ? Number(keluar_satuan).toLocaleString("id-ID") : "-"}</td>
                        <td>${Number(running_dus).toLocaleString("id-ID")}</td>
                        <td>${Number(running_satuan).toLocaleString("id-ID")}</td>
                    </tr>
                `
      })
    }

    // Saldo akhir
    const endDate = result.periode.split(" - ")[1]
    tableHtml += `
            <tr class="saldo-row">
                <td colspan="7" class="text-left">
                    <i class="fas fa-stop-circle"></i> SALDO AKHIR PER ${new Date(endDate.split("/").reverse().join("-")).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }).toUpperCase()}
                </td>
                <td>${Number(running_dus).toLocaleString("id-ID")}</td>
                <td>${Number(running_satuan).toLocaleString("id-ID")}</td>
            </tr>
        `

    $("#table-body").html(tableHtml)
  }

  function fetchData() {
    const produk_id = $("#produk_id").val()

    if (!produk_id) {
      $("#info-cards").hide()
      $("#alert-pilih-produk").show()
      $("#table-title").text("Pilih Produk")
      $("#table-body").html(
        '<tr><td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-gray);">Silakan pilih produk untuk menampilkan laporan.</td></tr>',
      )
      return
    }

    showLoading()

    const formData = $("#filterForm").serialize()

    $.ajax({
      url: `${selfUrl}/getkartustokdata`,
      type: "GET",
      data: formData,
      dataType: "json",
      success: (data) => {
        updateUI(data)
      },
      error: (jqXHR, textStatus, errorThrown) => {
        console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText)
        alert("Gagal memuat data. Silakan coba lagi.")
      },
      complete: () => {
        hideLoading()
      },
    })
  }

  // Event handlers
  $(".filter-select, .filter-input").on("change", () => {
    fetchData()
  })

  // Export to CSV function
  window.exportToCSV = () => {
    if (!currentData) {
      alert("Silakan pilih produk terlebih dahulu.")
      return
    }

    const formData = $("#filterForm").serialize()

    // Create a form and submit it
    const form = $("<form>", {
      method: "POST",
      action: `${selfUrl}/exportkartustokcsv`,
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
