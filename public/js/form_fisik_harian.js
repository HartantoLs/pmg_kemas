;(($) => {
  const selfUrl = `${baseUrl}/fisik_harian`;

  // Initialize input change tracking
  function initializeChangeTracking() {
    $(".table-input").each(function () {
      const $input = $(this)
      const originalValue = $input.data("original-value") || $input.val()
      $input.data("original-value", originalValue)
    })

    // Track input changes and add green highlight
    $(".table-input").on("input change", function () {
      const $input = $(this)
      const currentValue = $input.val()
      const originalValue = $input.data("original-value")

      if (currentValue != originalValue && currentValue !== "") {
        $input.addClass("changed")
      } else {
        $input.removeClass("changed")
      }

      updateStats()
    })
  }

  function showLoading() {
    $("#loading-overlay").show()
  }

  function hideLoading() {
    $("#loading-overlay").hide()
  }

  function updateStats() {
    const totalInputs = $(".table-input:not(:disabled)").length
    const filledInputs = $(".table-input:not(:disabled)").filter(function () {
      return $(this).val() > 0
    }).length

    const completionPercent = totalInputs > 0 ? Math.round((filledInputs / totalInputs) * 100) : 0

    $("#filled-entries").text(filledInputs)
    $("#completion-percent").text(completionPercent + "%")
    $("#progress-text").text(completionPercent + "%")
    $("#progress-fill").css("width", completionPercent + "%")
  }

  function showAlert(type, message) {
    const alert = $("#form-messages")
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

  // Initialize
  $(document).ready(() => {
    initializeChangeTracking()
    updateStats()

    // Filter form submission
    $("#filterForm").on("submit", (e) => {
      e.preventDefault()
      const tglValue = $("#tanggal_fisik").val()
      if (tglValue) {
        showLoading()
        window.location.href = selfUrl + "/form?tanggal_fisik=" + tglValue
      }
    })

    // Main form submission
    $("#formFisikHarian").on("submit", function (e) {
      e.preventDefault()

      if (!confirm("Apakah Anda yakin data yang diinput sudah benar? Data untuk hari ini akan disimpan/diperbarui.")) {
        return
      }

      const btn = $(this).find('button[type="submit"]')
      const originalText = btn.html()
      btn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop("disabled", true)
      showLoading()

      $.ajax({
        url: selfUrl + "/saveFisikHarian",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: (response) => {
          if (response.success) {
            showAlert("success", response.message)
            // Update original values after successful save
            $(".table-input").each(function () {
              $(this).data("original-value", $(this).val())
              $(this).removeClass("changed")
            })
          } else {
            showAlert("error", response.message)
          }
        },
        error: () => {
          showAlert("error", "Terjadi kesalahan koneksi.")
        },
        complete: () => {
          btn.html(originalText).prop("disabled", false)
          hideLoading()
        },
      })
    })

    // Import stock button
    $("#btnTarikStok").on("click", function () {
      if (
        !confirm(
          "Ini akan mengisi form dengan data stok pembukuan saat ini. Data yang sudah diinput akan ditimpa. Lanjutkan?",
        )
      ) {
        return
      }

      const btn = $(this)
      const originalText = btn.html()
      btn.html('<i class="fas fa-spinner fa-spin"></i> Mengimpor...').prop("disabled", true)

      setTimeout(() => {
        let importedCount = 0
        $(".table-input").each(function () {
          const stokBuku = $(this).data("stok-buku") || 0
          if (!$(this).is(":disabled")) {
            const oldValue = $(this).val()
            $(this).val(stokBuku)

            // Add import highlight animation
            if (oldValue != stokBuku) {
              $(this).addClass("import-highlight")
              setTimeout(() => {
                $(this).removeClass("import-highlight")
                // Check if changed from original
                if (stokBuku != $(this).data("original-value")) {
                  $(this).addClass("changed")
                } else {
                  $(this).removeClass("changed")
                }
              }, 2000)
            }
            importedCount++
          }
        })

        updateStats()
        btn.html(originalText).prop("disabled", false)
        showAlert("success", `Form telah diisi dengan data stok pembukuan! (${importedCount} field diimpor)`)
      }, 1000)
    })
  })
})(window.jQuery)
