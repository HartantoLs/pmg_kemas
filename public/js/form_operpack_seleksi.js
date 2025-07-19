// Ensure jQuery is imported before using the $ variable
const $ = window.jQuery

$(document).ready(() => {
  const baseUrl = window.location.origin
  let currentStok = 0

  function showLoading() {
    $("#loading-overlay").show()
  }

  function hideLoading() {
    $("#loading-overlay").hide()
  }

  function setQuickValue(fieldId, value) {
    const currentValue = Number.parseInt($("#" + fieldId).val()) || 0
    $("#" + fieldId)
      .val(currentValue + value)
      .trigger("input")
  }

  // Make setQuickValue global
  window.setQuickValue = setQuickValue

  function getStok(idProduk) {
    const display = $("#stok-display")
    if (!idProduk) {
      display.hide()
      currentStok = 0
      updateValidation()
      return
    }

    showLoading()
    $.getJSON(baseUrl + "/operpack_seleksi/get-stok-seleksi", { id_produk: idProduk })
      .done((data) => {
        currentStok = data.belum_seleksi < 0 ? 0 : data.belum_seleksi

        // Update display with styling based on stok
        display.removeClass("info warning danger")
        if (currentStok === 0) {
          display.addClass("danger")
          display.html('<i class="fas fa-times-circle"></i> Tidak ada stok yang perlu diseleksi')
        } else if (currentStok < 10) {
          display.addClass("warning")
          display.html(
            `<i class="fas fa-exclamation-triangle"></i> Stok tersedia: <strong>${currentStok.toLocaleString()} Pcs</strong> (Terbatas)`,
          )
        } else {
          display.addClass("info")
          display.html(
            `<i class="fas fa-info-circle"></i> Stok tersedia: <strong>${currentStok.toLocaleString()} Pcs</strong>`,
          )
        }

        display.show()
        updateValidation()
      })
      .fail(() => {
        display.addClass("danger").html('<i class="fas fa-exclamation-triangle"></i> Gagal memuat data stok').show()
        currentStok = 0
        updateValidation()
      })
      .always(() => {
        hideLoading()
      })
  }

  function updateValidation() {
    const pcsAman = Number.parseInt($("#pcs_aman").val()) || 0
    const pcsCurah = Number.parseInt($("#pcs_curah").val()) || 0
    const total = pcsAman + pcsCurah

    const calculationPanel = $("#calculation-panel")
    const validationStatus = $("#validation-status")
    const submitBtn = $("#submit-btn")
    const progressFill = $("#progress-fill")

    // Update calculation display
    $("#calc-aman").text(pcsAman.toLocaleString() + " pcs")
    $("#calc-curah").text(pcsCurah.toLocaleString() + " pcs")
    $("#calc-total").text(total.toLocaleString() + " pcs")

    // Show calculation panel if there's input
    if (total > 0 || currentStok > 0) {
      calculationPanel.show()
    } else {
      calculationPanel.hide()
    }

    // Update progress bar
    const progressPercent = currentStok > 0 ? Math.min((total / currentStok) * 100, 100) : 0
    progressFill.css("width", progressPercent + "%")

    // Update validation status
    validationStatus.removeClass("valid invalid neutral")
    if (total === 0) {
      validationStatus.addClass("neutral")
      validationStatus.html('<i class="fas fa-info-circle"></i> Belum ada input')
      submitBtn.prop("disabled", true)
      submitBtn.find("span").text("Simpan Hasil Seleksi")
    } else if (total > currentStok) {
      validationStatus.addClass("invalid")
      validationStatus.html(
        `<i class="fas fa-times-circle"></i> Melebihi stok tersedia (${currentStok.toLocaleString()} Pcs)`,
      )
      submitBtn.prop("disabled", true)
      submitBtn.find("span").text("Input Melebihi Stok")

      // Add error styling to inputs
      $("#pcs_aman, #pcs_curah").addClass("error")
    } else {
      const sisa = currentStok - total
      validationStatus.addClass("valid")
      validationStatus.html(
        `<i class="fas fa-check-circle"></i> Valid - Sisa: ${sisa.toLocaleString()} Pcs (${((sisa / currentStok) * 100).toFixed(1)}%)`,
      )
      submitBtn.prop("disabled", false)
      submitBtn.find("span").text("Simpan Hasil Seleksi")

      // Remove error styling
      $("#pcs_aman, #pcs_curah").removeClass("error")
    }

    // Update input max attributes
    $("#pcs_aman").attr("max", currentStok)
    $("#pcs_curah").attr("max", currentStok)
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
    }, 5000)
  }

  // Event handlers
  $("#seleksi_produk").on("change", function () {
    getStok($(this).val())
    // Reset inputs when changing product
    $("#pcs_aman, #pcs_curah").val(0)
    updateValidation()
  })

  $("#pcs_aman, #pcs_curah").on("input", () => {
    updateValidation()
  })

  $("#form-seleksi").on("submit", function (e) {
    e.preventDefault()

    // Double check validation before submit
    const pcsAman = Number.parseInt($("#pcs_aman").val()) || 0
    const pcsCurah = Number.parseInt($("#pcs_curah").val()) || 0
    const total = pcsAman + pcsCurah

    if (total > currentStok) {
      showAlert(
        "error",
        `Jumlah yang diinput (${total.toLocaleString()} pcs) melebihi stok yang tersedia (${currentStok.toLocaleString()} pcs).`,
      )
      return
    }

    if (total === 0) {
      showAlert("error", "Harap isi jumlah pcs aman atau curah.")
      return
    }

    const form = $(this)
    const btn = form.find('button[type="submit"]')
    const originalBtnText = btn.find("span").text()

    // Konfirmasi jika tidak menggunakan semua stok
    if (currentStok > 0 && total < currentStok) {
      const sisa = currentStok - total
      if (
        !confirm(
          `Anda hanya menginput ${total.toLocaleString()} dari ${currentStok.toLocaleString()} pcs yang tersedia.\n\nSisa ${sisa.toLocaleString()} pcs akan tetap dalam status "belum diseleksi".\n\nLanjutkan?`,
        )
      ) {
        return
      }
    }

    btn.find("span").text("Menyimpan...")
    btn.prop("disabled", true)
    showLoading()

    $.ajax({
      url: baseUrl + "/operpack_seleksi/simpan-seleksi",
      type: "POST",
      data: form.serialize(),
      dataType: "json",
      success: (response) => {
        if (response.success) {
          showAlert("success", response.message)
          form.find('input[type="number"]').val(0)
          getStok(form.find("#seleksi_produk").val())
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
