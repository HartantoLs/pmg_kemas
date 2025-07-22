// Ensure jQuery is imported before using the $ variable
const $ = window.jQuery

$(document).ready(() => {
  const selfUrl = `${baseUrl}/operpack_kemas_ulang`
  let currentMaxUnit = 0
  let productData = {}

  function showLoading() {
    $("#loading-overlay").show()
  }

  function hideLoading() {
    $("#loading-overlay").hide()
  }

  function setQuickValue(value) {
    const currentValue = Number.parseInt($("#jumlah_kemas_unit").val()) || 0
    const newValue = Math.min(currentValue + value, currentMaxUnit)
    $("#jumlah_kemas_unit").val(newValue).trigger("input")
  }

  function setMaxValue() {
    $("#jumlah_kemas_unit").val(currentMaxUnit).trigger("input")
  }

  // Make functions global
  window.setQuickValue = setQuickValue
  window.setMaxValue = setMaxValue

  function getStok(idProduk) {
    const display = $("#stok-display")
    const infoPanels = $("#info-panels")
    const conversionPanel = $("#conversion-panel")
    const unitLabel = $("#unit-label")
    const inputValidationInfo = $("#input-validation-info")
    const quickActions = $("#quick-actions")

    if (!idProduk) {
      display.hide()
      infoPanels.hide()
      conversionPanel.hide()
      quickActions.hide()
      currentMaxUnit = 0
      productData = {}
      unitLabel.text("Unit")
      inputValidationInfo.html('<i class="fas fa-info-circle"></i> Minimal: 0')
      updateValidation()
      return
    }

    showLoading()
    $.getJSON(selfUrl + "/get-stok-repack", { id_produk: idProduk })
      .done((data) => {
        productData = data
        currentMaxUnit = data.max_unit

        // Update unit label
        unitLabel.text(data.unit_label)

        // Update input validation info
        inputValidationInfo.html(
          `<i class="fas fa-info-circle"></i> Minimal: 0, Maksimal: ${currentMaxUnit.toLocaleString()} ${data.unit_label}`,
        )

        // Update display utama
        display.removeClass("info warning danger")
        if (currentMaxUnit === 0) {
          if (data.hasil_seleksi_aman === 0) {
            display.addClass("danger")
            display.html('<i class="fas fa-times-circle"></i> Belum ada hasil seleksi aman yang bisa dikemas')
          } else {
            display.addClass("danger")
            display.html('<i class="fas fa-times-circle"></i> Semua hasil seleksi aman sudah dikemas')
          }
        } else if (currentMaxUnit < 5) {
          display.addClass("warning")
          display.html(
            `<i class="fas fa-exclamation-triangle"></i> Stok tersedia: <strong>${currentMaxUnit.toLocaleString()} ${data.unit_label}</strong> (Terbatas)`,
          )
        } else {
          display.addClass("info")
          display.html(
            `<i class="fas fa-info-circle"></i> Stok tersedia: <strong>${currentMaxUnit.toLocaleString()} ${data.unit_label}</strong>`,
          )
        }

        // Update info panels
        $("#info-seleksi-aman").text(data.hasil_seleksi_aman.toLocaleString() + " Pcs")
        $("#info-sudah-kemas").text(data.hasil_kemas_ulang.toLocaleString() + " Pcs")
        $("#info-tersedia").text(data.stok_aman_siap_repack_pcs.toLocaleString() + " Pcs")

        // Update kapasitas content
        let kapasitasHtml = ""
        if (data.unit_type === "dus") {
          kapasitasHtml = `
                        <div class="info-row">
                            <span class="info-label">Maksimal Dus:</span>
                            <span class="info-value">${data.max_unit.toLocaleString()} Dus</span>
                        </div>
                    `
          if (data.sisa_pcs > 0) {
            kapasitasHtml += `
                            <div class="info-row">
                                <span class="info-label">Sisa Pcs:</span>
                                <span class="info-value">${data.sisa_pcs} Pcs</span>
                            </div>
                        `
          }
        } else {
          kapasitasHtml = `
                        <div class="info-row">
                            <span class="info-label">Maksimal Satuan:</span>
                            <span class="info-value">${data.max_unit.toLocaleString()} Pcs</span>
                        </div>
                    `
        }
        $("#kapasitas-content").html(kapasitasHtml)

        // Update conversion panel
        if (data.unit_type === "dus") {
          $("#conversion-highlight").text(`Konversi: 1 Dus = ${data.satuan_per_dus} Pcs`)
          $("#conversion-detail").text(`Input dalam ${data.unit_label} akan otomatis dikonversi ke Pcs`)
        } else {
          $("#conversion-highlight").text("Produk ini menggunakan kemasan satuan/pcs")
          $("#conversion-detail").text(`Input langsung dalam ${data.unit_label}`)
        }

        display.show()
        infoPanels.show()
        conversionPanel.show()
        if (currentMaxUnit > 0) {
          quickActions.show()
        }
        updateValidation()
      })
      .fail(() => {
        display.addClass("danger").html('<i class="fas fa-exclamation-triangle"></i> Gagal memuat data stok').show()
        infoPanels.hide()
        conversionPanel.hide()
        quickActions.hide()
        currentMaxUnit = 0
        productData = {}
        unitLabel.text("Unit")
        updateValidation()
      })
      .always(() => {
        hideLoading()
      })
  }

  function updateValidation() {
    const jumlahKemasUnit = Number.parseInt($("#jumlah_kemas_unit").val()) || 0

    const calculationPanel = $("#calculation-panel")
    const validationStatus = $("#validation-status")
    const submitBtn = $("#submit-btn")
    const progressFill = $("#progress-fill")
    const inputKemas = $("#jumlah_kemas_unit")

    // Update calculation display
    const unitLabel = productData.unit_label || "Unit"
    $("#calc-input").text(`${jumlahKemasUnit.toLocaleString()} ${unitLabel}`)

    // Update conversion to pcs
    let totalPcs = 0
    if (productData.unit_type === "dus" && jumlahKemasUnit > 0) {
      totalPcs = jumlahKemasUnit * productData.satuan_per_dus
    } else if (productData.unit_type === "satuan") {
      totalPcs = jumlahKemasUnit
    }
    $("#calc-pcs").text(totalPcs.toLocaleString() + " pcs")

    // Update sisa stok
    const sisa = currentMaxUnit - jumlahKemasUnit
    $("#calc-sisa").text(`${sisa.toLocaleString()} ${unitLabel}`)

    // Show calculation panel if there's input or product selected
    if (jumlahKemasUnit > 0 || currentMaxUnit > 0) {
      calculationPanel.show()
    } else {
      calculationPanel.hide()
    }

    // Update progress bar
    const progressPercent = currentMaxUnit > 0 ? Math.min((jumlahKemasUnit / currentMaxUnit) * 100, 100) : 0
    progressFill.css("width", progressPercent + "%")

    // Update validation status
    validationStatus.removeClass("valid invalid neutral")
    if (jumlahKemasUnit === 0) {
      validationStatus.addClass("neutral")
      validationStatus.html('<i class="fas fa-info-circle"></i> Belum ada input')
      submitBtn.prop("disabled", true)
      submitBtn.find("span").text("Simpan Hasil Kemas Ulang")
      inputKemas.removeClass("error")
    } else if (jumlahKemasUnit > currentMaxUnit) {
      validationStatus.addClass("invalid")
      validationStatus.html(
        `<i class="fas fa-times-circle"></i> Melebihi stok tersedia (${currentMaxUnit.toLocaleString()} ${unitLabel})`,
      )
      submitBtn.prop("disabled", true)
      submitBtn.find("span").text("Input Melebihi Stok")
      inputKemas.addClass("error")
    } else {
      const sisaPercent = currentMaxUnit > 0 ? ((sisa / currentMaxUnit) * 100).toFixed(1) : 0
      validationStatus.addClass("valid")
      validationStatus.html(
        `<i class="fas fa-check-circle"></i> Valid - Sisa: ${sisa.toLocaleString()} ${unitLabel} (${sisaPercent}%)`,
      )
      submitBtn.prop("disabled", false)
      submitBtn.find("span").text("Simpan Hasil Kemas Ulang")
      inputKemas.removeClass("error")
    }

    // Update input max attribute
    inputKemas.attr("max", currentMaxUnit)
  }

  function showAlert(type, message) {
    const alert = $("#formMessage")
    alert.removeClass("alert-success alert-error")
    alert.addClass("alert-" + type)
    alert.html(
      '<i class="fas fa-' + (type === "success" ? "check-circle" : "exclamation-triangle") + '"></i> ' + message,
    )
    alert.show()

    setTimeout(() => {
      alert.hide()
    }, 7000)
  }

  // Event handlers
  $("#repack_produk").on("change", function () {
    getStok($(this).val())
    // Reset input when changing product
    $("#jumlah_kemas_unit").val(0)
    updateValidation()
  })

  $("#jumlah_kemas_unit").on("input", () => {
    updateValidation()
  })

  $("#form-repack").on("submit", function (e) {
    e.preventDefault()

    const jumlahKemasUnit = Number.parseInt($("#jumlah_kemas_unit").val()) || 0

    // Double check validation
    if (jumlahKemasUnit > currentMaxUnit) {
      const unitLabel = productData.unit_label || "unit"
      showAlert(
        "error",
        `Jumlah yang dikemas (${jumlahKemasUnit.toLocaleString()} ${unitLabel}) melebihi stok yang tersedia (${currentMaxUnit.toLocaleString()} ${unitLabel}).`,
      )
      return
    }

    if (jumlahKemasUnit === 0) {
      showAlert("error", "Harap isi jumlah yang akan dikemas ulang.")
      return
    }

    const form = $(this)
    const btn = form.find('button[type="submit"]')
    const originalBtnText = btn.find("span").text()

    // Konfirmasi jika tidak menggunakan semua stok
    if (currentMaxUnit > 0 && jumlahKemasUnit < currentMaxUnit) {
      const unitLabel = productData.unit_label || "unit"
      const sisa = currentMaxUnit - jumlahKemasUnit
      if (
        !confirm(
          `Anda hanya mengemas ${jumlahKemasUnit.toLocaleString()} dari ${currentMaxUnit.toLocaleString()} ${unitLabel} yang tersedia.\n\nSisa ${sisa.toLocaleString()} ${unitLabel} akan tetap dalam status "siap dikemas".\n\nLanjutkan?`,
        )
      ) {
        return
      }
    }

    btn.find("span").text("Menyimpan...")
    btn.prop("disabled", true)
    showLoading()

    $.ajax({
      url: selfUrl + "/simpan-repack",
      type: "POST",
      data: form.serialize(),
      dataType: "json",
      success: (response) => {
        if (response.success) {
          showAlert("success", response.message)
          form.find('input[type="number"]').val(0)
          getStok(form.find("#repack_produk").val())
        } else {
          showAlert("error", response.message)
        }
      },
      error: (xhr) => {
        const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan sistem."
        showAlert("error", errorMsg)
      },
      complete: () => {
        btn.find("span").text(originalBtnText)
        btn.prop("disabled", false)
        hideLoading()
      },
    })
  })

  // Initialize validation
  updateValidation()
})
