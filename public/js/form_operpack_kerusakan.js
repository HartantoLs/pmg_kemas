$(document).ready(() => {
  let itemIndex = 0
  let selectedGudang = null
  let selectedPenjualan = null
  const baseUrl = "/operpack_kerusakan"
  // Template Input Asal
  const asalEksternalHtml = `
        <label for="asal">
            <i class="fas fa-receipt"></i>
            No. Surat Jalan Penjualan
        </label>
        <input type="text" name="asal" class="form-control" placeholder="Masukkan nomor surat jalan penjualan" required>
        <div class="penjualan-info"></div>
        <div class="validation-error">Nomor surat jalan penjualan wajib diisi</div>
    `

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
                        <div class="stock-info"></div>
                        <div class="validation-error">Pilih produk terlebih dahulu</div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-cubes"></i> Jumlah Dus</label>
                        <input type="number" name="items[${index}][jumlah_dus]" class="form-control jumlah-input" value="0" min="0">
                        <div class="unit-type">Dus</div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-cube"></i> Jumlah Satuan</label>
                        <input type="number" name="items[${index}][jumlah_satuan]" class="form-control jumlah-input" value="0" min="0">
                        <div class="unit-type">Satuan</div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-check-circle"></i> Status</label>
                        <div class="validation-status neutral">
                            <i class="fas fa-minus"></i> Belum divalidasi
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-danger btn-delete">
                            <i class="fas fa-trash"></i>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        `
  }

  function addRow() {
    $("#items-container").append(createNewRow(itemIndex))
    itemIndex++
    updateSummary()
  }

  function updateSummary() {
    const items = $("#items-container .item-row")
    const totalItems = items.length
    let totalDus = 0
    let totalSatuan = 0
    let totalPcs = 0

    items.each(function () {
      const dus = Number.parseInt($(this).find('input[name*="jumlah_dus"]').val()) || 0
      const satuan = Number.parseInt($(this).find('input[name*="jumlah_satuan"]').val()) || 0
      const satuanPerDus = Number.parseInt($(this).find(".produk-select option:selected").data("satuan-per-dus")) || 1

      totalDus += dus
      totalSatuan += satuan
      totalPcs += dus * satuanPerDus + satuan
    })

    $("#totalItems").text(totalItems)
    $("#totalDus").text(totalDus.toLocaleString("id-ID"))
    $("#totalSatuan").text(totalSatuan.toLocaleString("id-ID"))
    $("#totalPcs").text(totalPcs.toLocaleString("id-ID"))

    if (totalItems > 0) {
      $("#summaryCard").show()
    } else {
      $("#summaryCard").hide()
    }
  }

  function validatePenjualan(noSuratJalan) {
    const penjualanInfo = $(".penjualan-info")

    if (!noSuratJalan) {
      penjualanInfo.removeClass("loading available error").text("")
      selectedPenjualan = null
      return
    }

    penjualanInfo.removeClass("available error").addClass("loading")
    penjualanInfo.html('<i class="fas fa-spinner fa-spin"></i> Memvalidasi nomor surat jalan...')

    $.get(`${baseUrl}/validatepenjualan`, {
      no_surat_jalan: noSuratJalan,
    })
      .done((data) => {
        if (data.exists) {
          selectedPenjualan = data.data
          penjualanInfo.removeClass("loading").addClass("available")
          penjualanInfo.html(`
                    <div class="penjualan-valid">
                        <div><strong>✓ Surat Jalan Valid</strong></div>
                        <div>Customer: ${data.data.customer}</div>
                        <div>Tanggal: ${new Date(data.data.tanggal).toLocaleDateString("id-ID")}</div>
                    </div>
                `)

          // Update semua produk yang sudah dipilih
          $(".produk-select").each(function () {
            if ($(this).val()) {
              updatePenjualanProduk($(this))
            }
          })
        } else {
          selectedPenjualan = null
          penjualanInfo.removeClass("loading").addClass("error")
          penjualanInfo.html(
            '<div style="color: #dc2626;"><i class="fas fa-times-circle"></i> Nomor surat jalan tidak ditemukan</div>',
          )
        }
      })
      .fail(() => {
        selectedPenjualan = null
        penjualanInfo.removeClass("loading").addClass("error")
        penjualanInfo.html(
          '<div style="color: #dc2626;"><i class="fas fa-exclamation-triangle"></i> Error validasi</div>',
        )
      })
  }

  function updateStockInfo($select) {
    const row = $select.closest(".item-row")
    const idProduk = $select.val()
    const stockInfo = row.find(".stock-info")
    const validationStatus = row.find(".validation-status")
    const tanggal = $("#tanggal").val()

    if (!idProduk || $("#kategori_asal").val() !== "Internal" || !selectedGudang) {
      stockInfo.removeClass("loading available warning error").text("")
      validationStatus
        .removeClass("valid invalid")
        .addClass("neutral")
        .html('<i class="fas fa-minus"></i> Belum divalidasi')
      return
    }

    stockInfo.removeClass("available warning error").addClass("loading")
    stockInfo.html('<i class="fas fa-spinner fa-spin"></i> Mengecek stok...')
    validationStatus
      .removeClass("valid invalid")
      .addClass("neutral")
      .html('<i class="fas fa-spinner fa-spin"></i> Validasi...')

    $.get(`${baseUrl}/getstokproduk`, {
      id_gudang: selectedGudang,
      id_produk: idProduk,
      tanggal: tanggal,
    })
      .done((data) => {
        if (data.exists) {
          const satuanType = data.satuan_per_dus > 1 ? "Dus" : "Satuan"
          stockInfo.removeClass("loading").addClass("available")
          stockInfo.html(`
                    <div><strong>Stok Tersedia:</strong></div>
                    <div>${data.jumlah_dus} dus, ${data.jumlah_satuan} satuan</div>
                    <div style="font-size: 10px; color: #6b7280;">(Tipe: ${satuanType})</div>
                `)

          // Update unit type display
          if (data.satuan_per_dus > 1) {
            row.find(".unit-type").eq(0).text("Dus")
            row.find(".unit-type").eq(1).text("Satuan")
          } else {
            row.find(".unit-type").eq(0).text("Satuan (sebagai dus)")
            row.find(".unit-type").eq(1).text("Satuan")
          }

          // Store stok data for validation
          row.data("stok", data)
          validateInput(row)
        } else {
          stockInfo.removeClass("loading").addClass("error")
          stockInfo.html('<div style="color: #dc2626;"><i class="fas fa-times-circle"></i> Tidak ada stok</div>')
          validationStatus
            .removeClass("valid neutral")
            .addClass("invalid")
            .html('<i class="fas fa-times-circle"></i> Tidak tersedia')
          row.removeData("stok")
        }
      })
      .fail(() => {
        stockInfo.removeClass("loading").addClass("error")
        stockInfo.html('<div style="color: #dc2626;"><i class="fas fa-exclamation-triangle"></i> Error</div>')
        validationStatus
          .removeClass("valid neutral")
          .addClass("invalid")
          .html('<i class="fas fa-exclamation-triangle"></i> Error')
      })
  }

  function updatePenjualanProduk($select) {
    const row = $select.closest(".item-row")
    const idProduk = $select.val()
    const stockInfo = row.find(".stock-info")
    const validationStatus = row.find(".validation-status")

    if (!idProduk || $("#kategori_asal").val() !== "Eksternal" || !selectedPenjualan) {
      stockInfo.removeClass("loading available warning error").text("")
      validationStatus
        .removeClass("valid invalid")
        .addClass("neutral")
        .html('<i class="fas fa-minus"></i> Belum divalidasi')
      return
    }

    stockInfo.removeClass("available warning error").addClass("loading")
    stockInfo.html('<i class="fas fa-spinner fa-spin"></i> Mengecek data penjualan...')
    validationStatus
      .removeClass("valid invalid")
      .addClass("neutral")
      .html('<i class="fas fa-spinner fa-spin"></i> Validasi...')

    $.get(`${baseUrl}/getpenjualanproduk`, {
      no_surat_jalan: selectedPenjualan.no_surat_jalan,
      id_produk: idProduk,
    })
      .done((data) => {
        if (data.exists) {
          const produkData = data.data
          const satuanType = produkData.satuan_per_dus > 1 ? "Dus" : "Satuan"
          stockInfo.removeClass("loading").addClass("available")
          stockInfo.html(`
                    <div><strong>Data Penjualan:</strong></div>
                    <div>Terjual: ${produkData.jumlah_dus} dus, ${produkData.jumlah_satuan} satuan</div>
                    <div>Gudang: ${produkData.nama_gudang}</div>
                    <div style="font-size: 10px; color: #6b7280;">(Tipe: ${satuanType})</div>
                `)

          // Update unit type display
          if (produkData.satuan_per_dus > 1) {
            row.find(".unit-type").eq(0).text("Dus")
            row.find(".unit-type").eq(1).text("Satuan")
          } else {
            row.find(".unit-type").eq(0).text("Satuan (sebagai dus)")
            row.find(".unit-type").eq(1).text("Satuan")
          }

          // Store penjualan data (tidak perlu validasi ketat untuk eksternal)
          row.data("penjualan", produkData)
          validationStatus
            .removeClass("invalid neutral")
            .addClass("valid")
            .html('<i class="fas fa-check-circle"></i> Produk tersedia')
        } else {
          stockInfo.removeClass("loading").addClass("error")
          stockInfo.html(
            '<div style="color: #dc2626;"><i class="fas fa-times-circle"></i> Produk tidak ada di surat jalan</div>',
          )
          validationStatus
            .removeClass("valid neutral")
            .addClass("invalid")
            .html('<i class="fas fa-times-circle"></i> Tidak tersedia')
          row.removeData("penjualan")
        }
      })
      .fail(() => {
        stockInfo.removeClass("loading").addClass("error")
        stockInfo.html('<div style="color: #dc2626;"><i class="fas fa-exclamation-triangle"></i> Error</div>')
        validationStatus
          .removeClass("valid neutral")
          .addClass("invalid")
          .html('<i class="fas fa-exclamation-triangle"></i> Error')
      })
  }

  function validateInput(row) {
    const stokData = row.data("stok")
    if (!stokData) return

    const inputDus = Number.parseInt(row.find('input[name*="jumlah_dus"]').val()) || 0
    const inputSatuan = Number.parseInt(row.find('input[name*="jumlah_satuan"]').val()) || 0
    const validationStatus = row.find(".validation-status")

    const validDus = inputDus <= stokData.jumlah_dus
    const validSatuan = inputSatuan <= stokData.jumlah_satuan
    const hasInput = inputDus > 0 || inputSatuan > 0

    // Update input styling
    row.find('input[name*="jumlah_dus"]').toggleClass("input-error", !validDus && inputDus > 0)
    row.find('input[name*="jumlah_satuan"]').toggleClass("input-error", !validSatuan && inputSatuan > 0)

    // Update status
    if (!hasInput) {
      validationStatus
        .removeClass("valid invalid")
        .addClass("neutral")
        .html('<i class="fas fa-minus"></i> Belum divalidasi')
    } else if (validDus && validSatuan) {
      validationStatus
        .removeClass("invalid neutral")
        .addClass("valid")
        .html('<i class="fas fa-check-circle"></i> Valid')
    } else {
      validationStatus
        .removeClass("valid neutral")
        .addClass("invalid")
        .html('<i class="fas fa-exclamation-triangle"></i> Melebihi stok')
    }
  }

  function updateSourceIndicator() {
    const kategori = $("#kategori_asal").val()
    const asal = $('input[name="asal"], select[name="asal"]').val()
    const asalText = $('input[name="asal"], select[name="asal"] option:selected').text() || asal

    if (kategori && asal) {
      $("#sourceType").text(kategori.toUpperCase())
      $("#sourceName").text(asalText)
      $("#sourceBox").removeClass("external internal").addClass(kategori.toLowerCase())
      $("#sourceIndicator").show()

      // Load damage history
      $.getJSON(`${baseUrl}/getdamagehistory`, {
        kategori_asal: kategori,
        asal: asal,
      }).done((history) => {
        if (history.length > 0) {
          let historyHtml = "<strong>Riwayat Kerusakan:</strong><br>"
          history.forEach((item) => {
            historyHtml += `<div class="history-item">${item.no_surat_jalan} - ${item.waktu_diterima} (${item.total_items} items, ${item.total_pcs} pcs)</div>`
          })
          $("#damageHistory").html(historyHtml).show()
        } else {
          $("#damageHistory").hide()
        }
      })
    } else {
      $("#sourceIndicator").hide()
      $("#damageHistory").hide()
    }
  }

  // Event Handlers
  $("#kategori_asal").on("change", function () {
    const wrapper = $("#asal-wrapper")
    const kategori = $(this).val()
    wrapper.empty()
    selectedGudang = null
    selectedPenjualan = null

    if (kategori === "Eksternal") {
      wrapper.html(asalEksternalHtml)
    } else if (kategori === "Internal") {
      wrapper.html(`
                <label for="asal">
                    <i class="fas fa-warehouse"></i>
                    Asal Gudang (Internal)
                </label>
                <select name="asal" class="form-control" required>
                    <option value="">Memuat gudang...</option>
                </select>
            `)

      // Load gudang via AJAX
      $.get(`${baseUrl}/getgudanginternal`)
        .done((data) => {
          let options = '<option value="">-- Pilih Gudang --</option>'
          data.forEach((gudang) => {
            options += `<option value="${gudang.id_gudang}">${gudang.nama_gudang}</option>`
          })
          wrapper.find('select[name="asal"]').html(options)
        })
        .fail(() => {
          wrapper.find('select[name="asal"]').html('<option value="">Gagal memuat</option>')
        })
    }

    // Reset semua info ketika kategori berubah
    $(".stock-info").removeClass("loading available warning error").text("")
    $(".validation-status")
      .removeClass("valid invalid")
      .addClass("neutral")
      .html('<i class="fas fa-minus"></i> Belum divalidasi')
    $(".form-control").removeClass("input-error")
    $("#sourceIndicator").hide()
    $("#damageHistory").hide()
  })

  $(document).on("change", 'select[name="asal"]', function () {
    if ($("#kategori_asal").val() === "Internal") {
      selectedGudang = $(this).val()
      // Reset stok info dan update semua item
      $(".stock-info").removeClass("loading available warning error").text("")
      $(".validation-status")
        .removeClass("valid invalid")
        .addClass("neutral")
        .html('<i class="fas fa-minus"></i> Belum divalidasi')
      $(".form-control").removeClass("input-error")
      $(".produk-select").each(function () {
        if ($(this).val()) {
          updateStockInfo($(this))
        }
      })
    }
    updateSourceIndicator()
  })

  $(document).on("input", 'input[name="asal"]', function () {
    if ($("#kategori_asal").val() === "Eksternal") {
      const noSuratJalan = $(this).val()
      validatePenjualan(noSuratJalan)
    }
    updateSourceIndicator()
  })

  $("#btnTambahItem").on("click", addRow)

  $("#items-container").on("click", ".btn-delete", function () {
    $(this).closest(".item-row").remove()
    updateSummary()
  })

  $("#items-container").on("change", ".produk-select", function () {
    const kategori = $("#kategori_asal").val()
    if (kategori === "Internal") {
      updateStockInfo($(this))
    } else if (kategori === "Eksternal") {
      updatePenjualanProduk($(this))
    }
    updateSummary()
  })

  $("#items-container").on("input change", ".jumlah-input", function () {
    const row = $(this).closest(".item-row")
    if (row.data("stok")) {
      validateInput(row)
    }
    updateSummary()
  })

  $("#tanggal").on("change", () => {
    // Update semua stok info ketika tanggal berubah (hanya untuk Internal)
    if ($("#kategori_asal").val() === "Internal") {
      $(".produk-select").each(function () {
        if ($(this).val()) {
          updateStockInfo($(this))
        }
      })
    }
  })

  // Form validation and submission
  $("#formKerusakan").on("submit", function (e) {
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

    if (!$("#kategori_asal").val()) {
      $("#kategori_asal").closest(".form-group").addClass("error")
      $("#kategori_asal").siblings(".validation-error").show()
      isValid = false
    }

    // Validate asal
    const asalValue = $('input[name="asal"], select[name="asal"]').val()
    if (!asalValue) {
      $('input[name="asal"], select[name="asal"]').closest(".form-group").addClass("error")
      $('input[name="asal"], select[name="asal"]').siblings(".validation-error").show()
      isValid = false
    }

    // Validate eksternal penjualan
    if ($("#kategori_asal").val() === "Eksternal" && !selectedPenjualan) {
      $('input[name="asal"]').closest(".form-group").addClass("error")
      alert("Nomor surat jalan penjualan tidak valid.")
      isValid = false
    }

    // Validate items
    if ($("#items-container .item-row").length === 0) {
      alert("Harap tambahkan minimal satu item produk.")
      return
    }

    // Validasi stok untuk kategori Internal
    if ($("#kategori_asal").val() === "Internal") {
      let hasError = false
      $(".item-row").each(function () {
        const row = $(this)
        const stokData = row.data("stok")
        if (stokData) {
          const inputDus = Number.parseInt(row.find('input[name*="jumlah_dus"]').val()) || 0
          const inputSatuan = Number.parseInt(row.find('input[name*="jumlah_satuan"]').val()) || 0

          if (inputDus > stokData.jumlah_dus || inputSatuan > stokData.jumlah_satuan) {
            hasError = true
          }
        }
      })

      if (hasError) {
        alert("Terdapat item dengan jumlah yang melebihi stok tersedia. Silakan periksa kembali.")
        isValid = false
      }
    }

    // Validasi produk untuk kategori Eksternal
    if ($("#kategori_asal").val() === "Eksternal") {
      let hasError = false
      $(".item-row").each(function () {
        const row = $(this)
        const penjualanData = row.data("penjualan")
        const produkSelect = row.find(".produk-select")

        if (produkSelect.val() && !penjualanData) {
          hasError = true
        }
      })

      if (hasError) {
        alert("Terdapat produk yang tidak ada dalam surat jalan penjualan. Silakan periksa kembali.")
        isValid = false
      }
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
          .removeClass("alert-error alert-success")
          .addClass(response.success ? "alert-success" : "alert-error")
          .show()

        if (response.success) {
          $("#formKerusakan")[0].reset()
          $("#items-container").empty()
          $("#asal-wrapper").empty()
          $("#sourceIndicator").hide()
          $("#damageHistory").hide()
          selectedGudang = null
          selectedPenjualan = null
          updateSummary()
          addRow()
        }

        $("html, body").animate({ scrollTop: 0 }, 500)
        setTimeout(() => msgBox.slideUp(), 5000)
      },
      error: (xhr) => {
        const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan koneksi."
        $("#formMessage").text(errorMsg).removeClass("alert-success").addClass("alert-error").show()
        $("html, body").animate({ scrollTop: 0 }, 500)
        setTimeout(() => $("#formMessage").slideUp(), 5000)
      },
      complete: () => {
        btn.html('<i class="fas fa-save"></i> Simpan Data Kerusakan').prop("disabled", false)
      },
    })
  })

  // Initialize
  addRow()
})
