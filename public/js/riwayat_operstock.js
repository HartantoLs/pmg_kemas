$(document).ready(() => {
  const modal = $("#editModal")
  const dataTableBody = $("#dataTableBody")
  const loadingState = $("#loadingState")
  const baseUrl = "/operstock"

  const stokTersediaAsal = { dus: 0, satuan: 0 }
  const stokSaatIniTujuan = { dus: 0, satuan: 0 }
  let satuanPerDus = 1

  function showNotification(message, type = "success") {
    const toast = $("#notification-toast")
    toast.removeClass("success error").addClass(type).text(message).fadeIn()
    setTimeout(() => toast.fadeOut(), 3000)
  }

  function fetchFilteredData() {
    loadingState.show()
    dataTableBody.html("")

    $.ajax({
      url: `${baseUrl}/filterriwayat`,
      type: "POST",
      data: {
        tanggal_mulai: $("#tanggal_mulai").val(),
        tanggal_akhir: $("#tanggal_akhir").val(),
        gudang_id: $("#gudang_id").val(),
        produk_id: $("#produk_id").val(),
      },
      cache: false,
      success: (response) => {
        dataTableBody.html(response)
        updateRowCount()
      },
      error: () => dataTableBody.html('<tr><td colspan="8">Gagal memuat data.</td></tr>'),
      complete: () => loadingState.hide(),
    })
  }

  function updateRowCount() {
    const visibleRows = $("#dataTableBody tr:visible").length
    $("#totalRows").text(visibleRows)
  }

  function handleSatuanPerDusEdit(satuanPerDus) {
    const dusInput = $("#editJumlahDus")
    const satuanInput = $("#editJumlahSatuan")

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

  function validateInputs() {
    const dusLama = Number.parseInt($("#editJumlahDusLama").val()) || 0
    const satuanLama = Number.parseInt($("#editJumlahSatuanLama").val()) || 0
    const dusBaru = Number.parseInt($("#editJumlahDus").val()) || 0
    const satuanBaru = Number.parseInt($("#editJumlahSatuan").val()) || 0

    let isError = false
    let errorMessage = ""

    if (dusBaru > stokTersediaAsal.dus || satuanBaru > stokTersediaAsal.satuan) {
      $("#editJumlahDus, #editJumlahSatuan").addClass("input-error")
      isError = true
      errorMessage = "Stok di gudang asal tidak mencukupi!"
    } else {
      $("#editJumlahDus, #editJumlahSatuan").removeClass("input-error")
    }

    const selisihDus = dusLama - dusBaru
    const selisihSatuan = satuanLama - satuanBaru

    if (stokSaatIniTujuan.dus - selisihDus < 0 || stokSaatIniTujuan.satuan - selisihSatuan < 0) {
      $("#editJumlahDus, #editJumlahSatuan").addClass("input-error")
      isError = true
      errorMessage = "Stok di gudang tujuan akan menjadi minus!"
    }

    if (isError) {
      $("#editErrorMsg").text(errorMessage).show()
      $("#submitEdit").prop("disabled", true).css("opacity", "0.6")
    } else {
      $("#editErrorMsg").hide()
      $("#submitEdit").prop("disabled", false).css("opacity", "1")
    }
  }

  // Search functionality (client-side)
  $("#searchInput").on("keyup", function () {
    const value = $(this).val().toLowerCase()
    $("#dataTableBody tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    })
    updateRowCount()
  })

  // Event Handlers
  $(".form-filter").on("change", fetchFilteredData)

  $("body").on("click", ".btn-edit", function () {
    const id = $(this).data("id")
    const btn = $(this)
    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop("disabled", true)

    $.post(
      `${baseUrl}/getdetailriwayat`,
      { id: id },
      (response) => {
        if (response.success) {
          const data = response.data
          $("#editDetailId").val(data.id)
          $("#editProdukId").val(data.produk_id)
          $("#editGudangAsalId").val(data.gudang_asal_id)
          $("#editGudangTujuanId").val(data.gudang_tujuan_id)
          $("#editJumlahDusLama").val(data.jumlah_dus_dikirim)
          $("#editJumlahSatuanLama").val(data.jumlah_satuan_dikirim)
          $("#editSatuanPerDus").val(data.satuan_per_dus)
          $("#editNamaProduk").text(data.nama_produk)
          $("#editInfoGudang").html(
            `<b>${data.nama_gudang_asal}</b> <i class="fas fa-long-arrow-alt-right"></i> <b>${data.nama_gudang_tujuan}</b>`,
          ) // Corrected line
          $("#editStokInfoAsal").text(`${data.stok_asal_saat_itu_dus} Dus, ${data.stok_asal_saat_itu_satuan} Satuan`)
          $("#editStokInfoTujuan").text(
            `${data.stok_tujuan_saat_itu_dus} Dus, ${data.stok_tujuan_saat_itu_satuan} Satuan`,
          )
          $("#editJumlahDus").val(data.jumlah_dus_dikirim)
          $("#editJumlahSatuan").val(data.jumlah_satuan_dikirim)

          satuanPerDus = data.satuan_per_dus || 1

          // Handle satuan per dus logic
          handleSatuanPerDusEdit(satuanPerDus)

          // Set stok data for validation
          stokTersediaAsal.dus = data.stok_asal_saat_itu_dus
          stokTersediaAsal.satuan = data.stok_asal_saat_itu_satuan
          stokSaatIniTujuan.dus = data.stok_tujuan_saat_itu_dus
          stokSaatIniTujuan.satuan = data.stok_tujuan_saat_itu_satuan

          validateInputs()
          modal.show()
        } else {
          showNotification("Gagal mengambil data detail.", "error")
        }
        btn.html('<i class="fas fa-edit"></i>').prop("disabled", false)
      },
      "json",
    )
  })

  $("body").on("click", ".btn-delete", function () {
    if (
      !confirm(
        "Anda yakin ingin menghapus riwayat ini? Stok akan dikembalikan ke gudang asal dan dikurangi dari gudang tujuan.",
      )
    )
      return

    const id = $(this).data("id")
    const btn = $(this)
    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop("disabled", true)

    $.post(
      `${baseUrl}/hapusriwayat`,
      { id: id },
      (response) => {
        if (response.success) {
          showNotification(response.message, "success")
          $(`#row-${id}`).fadeOut(500, function () {
            $(this).remove()
            updateRowCount()
          })
        } else {
          showNotification(response.message, "error")
          btn.html('<i class="fas fa-trash"></i> Hapus').prop("disabled", false)
        }
      },
      "json",
    )
  })

  $("#editJumlahDus, #editJumlahSatuan").on("input", validateInputs)

  $("#editForm").on("submit", function (e) {
    e.preventDefault()
    const submitBtn = $(this).find("#submitEdit")
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop("disabled", true)

    $.post(
      `${baseUrl}/updateriwayat`,
      $(this).serialize(),
      (response) => {
        if (response.success) {
          modal.hide()
          showNotification(response.message, "success")
          fetchFilteredData()
        } else {
          showNotification(response.message, "error")
        }
        submitBtn.html('<i class="fas fa-save"></i> Update Data').prop("disabled", false)
      },
      "json",
    )
  })

  $(".close").on("click", () => modal.hide())
  $(window).on("click", (event) => {
    if ($(event.target).is(modal)) modal.hide()
  })

  // Initialize
  fetchFilteredData()
})
