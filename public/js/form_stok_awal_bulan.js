;(($) => {
  const selfUrl = "/stok_awal_bulan"

  function showAlert(type, message, duration = 5000) {
    const alert = $("#form-messages")
    alert
      .removeClass("alert-success alert-error alert-warning")
      .addClass("alert-" + type)
      .show()
    alert.html(`<i class="fas fa-${type === "success" ? "check-circle" : "exclamation-triangle"}"></i> ${message}`)
    setTimeout(() => {
      alert.fadeOut()
    }, duration)
  }

  // Fungsi untuk menghitung dan import stok
  $("#btnTarikStok").on("click", function () {
    const selectedMonth = $("#tanggal_opname_month").val()
    const actionText = `Ini akan menghitung dan mengisi form dengan data stok awal bulan ${selectedMonth} berdasarkan opname dan mutasi bulan sebelumnya.`

    if (!confirm(`${actionText}\n\nSemua data yang sudah Anda input akan ditimpa!\n\nLanjutkan?`)) {
      return
    }

    const btn = $(this)
    const originalText = btn.html()
    btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Menghitung...')

    $.ajax({
      url: selfUrl + "/calculateBeginningStock",
      method: "POST",
      data: {
        tanggal_opname_month: selectedMonth,
      },
      dataType: "json",
      success: (response) => {
        if (response.success) {
          let filledCount = 0
          $(".table-input").each(function () {
            const $input = $(this)
            const name = $input.attr("name")
            const match = name.match(/items\[(\d+)\]\[(\d+)\]\[(dus|satuan)\]/)
            if (match) {
              const prodId = match[1]
              const gudangId = match[2]
              const type = match[3]
              if (response.calculated_stock[prodId] && response.calculated_stock[prodId][gudangId]) {
                const newValue = response.calculated_stock[prodId][gudangId][type] || 0
                if (!$input.is(":disabled")) {
                  $input.val(newValue).trigger("input")
                  filledCount++
                }
              }
            }
          })

          const message = response.opname_found
            ? `Perhitungan berhasil! Stok awal bulan lalu ditambah mutasi telah diisi.`
            : `Stok opname bulan lalu tidak ditemukan. Perhitungan dimulai dari 0 ditambah mutasi.`
          showAlert("success", `${message} ${filledCount} field diisi.`)
        } else {
          showAlert("error", "Gagal menghitung stok: " + (response.message || "Error tidak diketahui"))
        }
      },
      error: () => {
        showAlert("error", "Gagal terhubung ke server untuk menghitung stok.")
      },
      complete: () => {
        btn.prop("disabled", false).html(originalText)
      },
    })
  })

  // Submit form opname
  $("#formOpnameData").on("submit", function (e) {
    e.preventDefault()
    const submitBtn = $(this).find('button[type="submit"]')
    const originalText = submitBtn.html()
    const actionText = submitBtn.text().includes("Update") ? "Memperbarui" : "Menyimpan"

    if (!confirm(`Apakah Anda yakin ingin ${actionText.toLowerCase()} data stock opname ini?`)) return

    submitBtn.prop("disabled", true).html(`<i class="fas fa-spinner fa-spin"></i> ${actionText}...`)
    $("#loading-overlay").show()

    $.ajax({
      url: selfUrl + "/saveOpname",
      method: "POST",
      data: new FormData(this),
      processData: false,
      contentType: false,
      dataType: "json",
      success: (response) => {
        showAlert(response.success ? "success" : "error", response.message)
        if (response.success) {
          // Update nilai original agar tidak ada highlight hijau lagi
          $(".table-input").each(function () {
            $(this).data("original-value", $(this).val()).removeClass("changed")
          })
          $("html, body").animate({ scrollTop: $("#form-messages").offset().top - 100 }, 500)
        }
      },
      error: () => {
        showAlert("error", "Terjadi kesalahan saat menyimpan data.")
      },
      complete: () => {
        submitBtn.prop("disabled", false).html(originalText)
        $("#loading-overlay").hide()
      },
    })
  })

  // Event listener untuk perubahan input
  $(".table-input").on("input change", function () {
    const $input = $(this)
    if ($input.val() != $input.data("original-value")) {
      $input.addClass("changed")
    } else {
      $input.removeClass("changed")
    }
  })

  // Tombol clear form
  $("#btnClearForm").on("click", () => {
    if (confirm("Yakin ingin mengosongkan semua isian form?")) {
      $(".table-input:not(:disabled)").val(0).trigger("input")
      showAlert("success", "Form berhasil dikosongkan.")
    }
  })
})(window.jQuery)
