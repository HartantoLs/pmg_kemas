$(document).ready(() => {
  let itemIndex = 0
  const baseUrl = "/operstock"

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
                        <div class="stock-comparison">Pilih produk dan gudang</div>
                        <div class="validation-error">Pilih produk terlebih dahulu</div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-cubes"></i> Jumlah Dus</label>
                        <input type="number" name="items[${index}][jumlah_dus]" class="form-control quantity-input dus-input" value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-cube"></i> Jumlah Satuan</label>
                        <input type="number" name="items[${index}][jumlah_satuan]" class="form-control quantity-input satuan-input" value="0" min="0">
                    </div>
                    <div class="form-group" style="align-self: center;">
                        <button type="button" class="btn btn-danger btn-delete"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>`
  }

  function addRow() {
    $("#items-container").append(createNewRow(itemIndex++))
    updateSummary()
  }

  function handleSatuanPerDus(row, satuanPerDus) {
    const dusInput = row.find(".dus-input")
    const satuanInput = row.find(".satuan-input")

    if (satuanPerDus == 1) {
      // Jika satuan_per_dus = 1, disable dus input dan aktifkan satuan
      dusInput.addClass("input-locked").prop("disabled", true).val(0)
      satuanInput.removeClass("input-locked").prop("disabled", false)
    } else {
      // Jika satuan_per_dus > 1, aktifkan kedua input
      dusInput.removeClass("input-locked").prop("disabled", false)
      satuanInput.removeClass("input-locked").prop("disabled", false)
    }
  }

  function updateStockComparison(row) {
    const stockComp = row.find(".stock-comparison")
    const produkSelect = row.find(".produk-select")
    const id_produk = produkSelect.val()
    const satuanPerDus = produkSelect.find("option:selected").data("satuan-per-dus")
    const id_gudang_asal = $("#gudang_asal").val()
    const id_gudang_tujuan = $("#gudang_tujuan").val()
    const tanggal = $("#tanggal").val()
    const dus = Number.parseInt(row.find('input[name*="jumlah_dus"]').val()) || 0
    const satuan = Number.parseInt(row.find('input[name*="jumlah_satuan"]').val()) || 0

    // Handle satuan per dus logic
    if (id_produk && satuanPerDus) {
      handleSatuanPerDus(row, satuanPerDus)
    }

    if (id_produk && id_gudang_asal && id_gudang_tujuan && tanggal) {
      stockComp.html('<i class="fas fa-spinner fa-spin"></i> Mengecek...')

      $.getJSON(`${baseUrl}/getbothstocks`, {
        id_produk,
        id_gudang_asal,
        id_gudang_tujuan,
        tanggal,
      })
        .done((data) => {
          const isValid = dus <= data.asal.dus && satuan <= data.asal.satuan
          const transferColor = isValid ? "#0ea5e9" : "#dc2626"

          let html = `
                    <div class="stock-item stock-asal">
                        <span><i class="fas fa-warehouse"></i> Asal:</span>
                        <span>${data.asal.dus} Dus, ${data.asal.satuan} Satuan</span>
                    </div>
                    <div class="stock-item stock-tujuan">
                        <span><i class="fas fa-warehouse"></i> Tujuan:</span>
                        <span>${data.tujuan.dus} Dus, ${data.tujuan.satuan} Satuan</span>
                    </div>
                    <div class="stock-item stock-transfer" style="color: ${transferColor}">
                        <span><i class="fas fa-exchange-alt"></i> Transfer:</span>
                        <span>${dus} Dus, ${satuan} Satuan</span>
                    </div>
                `

          if (!isValid && (dus > 0 || satuan > 0)) {
            html += `<div style="color: #dc2626; font-size: 11px; margin-top: 4px;">
                        <i class="fas fa-exclamation-triangle"></i> Stok asal tidak cukup!
                    </div>`
          }

          stockComp.html(html)
          updateSummary()
        })
        .fail(() => {
          stockComp.html('<span style="color: #dc2626;"><i class="fas fa-times"></i> Gagal cek stok</span>')
        })
    } else {
      stockComp.html("Pilih produk & gudang")
    }
  }

  function updateSummary() {
    const items = $("#items-container .item-row")
    const totalItems = items.length
    let totalDus = 0
    let totalSatuan = 0
    let hasStockWarning = false

    items.each(function () {
      const dus = Number.parseInt($(this).find('input[name*="jumlah_dus"]').val()) || 0
      const satuan = Number.parseInt($(this).find('input[name*="jumlah_satuan"]').val()) || 0
      totalDus += dus
      totalSatuan += satuan

      // Check if there's stock warning
      const stockComp = $(this).find(".stock-comparison")
      if (stockComp.find(".stock-transfer").css("color") === "rgb(220, 38, 38)") {
        hasStockWarning = true
      }
    })

    $("#totalItems").text(totalItems)
    $("#totalDus").text(totalDus.toLocaleString("id-ID"))
    $("#totalSatuan").text(totalSatuan.toLocaleString("id-ID"))
    $("#stockStatus")
      .text(hasStockWarning ? "⚠ Periksa Stok" : "✓ Siap Transfer")
      .css("color", hasStockWarning ? "#dc2626" : "#059669")

    if (totalItems > 0) {
      $("#summaryCard").show()
    } else {
      $("#summaryCard").hide()
    }
  }

  function updateWarehouseFlow() {
    const asalId = $("#gudang_asal").val()
    const tujuanId = $("#gudang_tujuan").val()

    if (asalId && tujuanId) {
      $("#namaGudangAsal").text($("#gudang_asal option:selected").text())
      $("#namaGudangTujuan").text($("#gudang_tujuan option:selected").text())
      $("#warehouseFlow").show()

      // Load transfer history
      $.getJSON(`${baseUrl}/gettransferhistory`, {
        id_gudang_asal: asalId,
        id_gudang_tujuan: tujuanId,
      }).done((history) => {
        if (history.length > 0) {
          let historyHtml = "<strong>Riwayat Transfer:</strong><br>"
          history.forEach((item) => {
            historyHtml += `<div class="history-item">${item.no_surat_jalan} - ${item.waktu_kirim} (${item.total_items} items)</div>`
          })
          $("#transferHistory").html(historyHtml).show()
        } else {
          $("#transferHistory").hide()
        }
      })
    } else {
      $("#warehouseFlow").hide()
      $("#transferHistory").hide()
    }
  }

  // Event Handlers
  $("#btnTambahItem").on("click", addRow)

  $("#items-container").on("click", ".btn-delete", function () {
    $(this).closest(".item-row").remove()
    updateSummary()
  })

  $("#tanggal, #gudang_asal, #gudang_tujuan").on("change", () => {
    updateWarehouseFlow()
    $(".item-row").each((i, el) => updateStockComparison($(el)))
  })

  $("#items-container").on("change", ".produk-select", function () {
    updateStockComparison($(this).closest(".item-row"))
  })

  $("#items-container").on("input", ".quantity-input", function () {
    updateStockComparison($(this).closest(".item-row"))
  })

  $("#formOperstock").on("submit", function (e) {
    e.preventDefault()

    // Reset validation
    $(".form-group").removeClass("error")
    $(".validation-error").hide()

    let isValid = true

    // Validate required fields
    if (!$("#no_surat_jalan").val()) {
      $("#no_surat_jalan").closest(".form-group").addClass("error")
      $("#no_surat_jalan").siblings(".validation-error").show()
      isValid = false
    }

    if (!$("#tanggal").val()) {
      $("#tanggal").closest(".form-group").addClass("error")
      $("#tanggal").siblings(".validation-error").show()
      isValid = false
    }

    if (!$("#gudang_asal").val()) {
      $("#gudang_asal").closest(".form-group").addClass("error")
      $("#gudang_asal").siblings(".validation-error").show()
      isValid = false
    }

    if (!$("#gudang_tujuan").val()) {
      $("#gudang_tujuan").closest(".form-group").addClass("error")
      $("#gudang_tujuan").siblings(".validation-error").show()
      isValid = false
    }

    if ($("#gudang_asal").val() === $("#gudang_tujuan").val() && $("#gudang_asal").val()) {
      alert("Gudang asal dan tujuan tidak boleh sama.")
      $("#gudang_tujuan").closest(".form-group").addClass("error")
      isValid = false
    }

    // Validate items
    if ($("#items-container .item-row").length === 0) {
      alert("Harap tambahkan minimal satu item untuk dipindahkan.")
      return
    }

    let hasValidItem = false
    $("#items-container .item-row").each(function () {
      const produk = $(this).find(".produk-select").val()
      const dus = Number.parseInt($(this).find('input[name*="jumlah_dus"]').val()) || 0
      const satuan = Number.parseInt($(this).find('input[name*="jumlah_satuan"]').val()) || 0

      if (!produk) {
        $(this).find(".produk-select").closest(".form-group").addClass("error")
        $(this).find(".produk-select").siblings(".validation-error").show()
        isValid = false
      }

      if (produk && (dus > 0 || satuan > 0)) {
        hasValidItem = true
      }
    })

    if (!hasValidItem) {
      alert("Harap lengkapi minimal satu item dengan jumlah yang valid.")
      isValid = false
    }

    if (!isValid) {
      $("html, body").animate({ scrollTop: $(".form-group.error").first().offset().top - 100 }, 500)
      return
    }

    const btn = $(this).find('button[type="submit"]')
    btn.html('<span class="spinner"></span> Menyimpan...').prop("disabled", true)

    $.ajax({
      url: `${baseUrl}/simpan`,
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: (response) => {
        const msgBox = $("#formMessage")
        msgBox
          .text(response.message)
          .removeClass("alert-danger alert-success")
          .addClass(response.success ? "alert-success" : "alert-danger")
          .show()

        if (response.success) {
          $("#formOperstock")[0].reset()
          $("#items-container").empty()
          $("#warehouseFlow").hide()
          $("#transferHistory").hide()
          updateSummary()
          addRow()
        }

        $("html, body").animate({ scrollTop: 0 }, 500)
        setTimeout(() => msgBox.slideUp(), 5000)
      },
      error: () => {
        $("#formMessage")
          .text("Terjadi kesalahan koneksi.")
          .removeClass("alert-success")
          .addClass("alert-danger")
          .show()
        $("html, body").animate({ scrollTop: 0 }, 500)
        setTimeout(() => $("#formMessage").slideUp(), 5000)
      },
      complete: () => {
        btn.html('<i class="fas fa-exchange-alt"></i> Simpan Perpindahan').prop("disabled", false)
      },
    })
  })

  // Initialize
  addRow()
})
