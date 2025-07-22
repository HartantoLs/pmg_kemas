// Declare the $ variable before using it
const $ = window.jQuery

$(document).ready(() => {
  const selfUrl = `${baseUrl}/operpack_kemas_ulang`;
  const modal = $("#editModal")
  const loadingState = $("#loadingState")
  const dataTableBody = $("#dataTableBody")

  let currentSatuanPerDus = 1
  let currentMaxKemas = 0

  function fetchFilteredData() {
    loadingState.show()
    dataTableBody.hide()

    const formData = {
      action: "filter_data",
      tanggal_mulai: $("#tanggal_mulai").val(),
      tanggal_akhir: $("#tanggal_akhir").val(),
      produk_id: $("#produk_id").val(),
    }

    $.post(selfUrl + "/filter-data", formData)
      .done((response) => {
        dataTableBody.html(response)
        updateRowCount()
      })
      .fail(() => {
        dataTableBody.html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data.</td></tr>')
      })
      .always(() => {
        loadingState.hide()
        dataTableBody.show()
      })
  }

  $(".form-filter").on("change", fetchFilteredData)

  $("#searchInput").on("keyup", function () {
    const value = $(this).val().toLowerCase()
    $("#dataTableBody tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    })
    updateRowCount()
  })

  function updateRowCount() {
    const visibleRows = $("#dataTableBody tr:visible").length
    $("#totalRows").text(visibleRows)
  }

  function validateEditInput() {
    const num = Number.parseInt($("#editJumlahKemas").val()) || 0
    let isError = false
    let errorMsg = ""

    if (num < 0) {
      isError = true
      errorMsg = "Jumlah tidak boleh negatif."
    } else if (num > currentMaxKemas) {
      isError = true
      errorMsg = `Jumlah melebihi stok siap kemas (${currentMaxKemas} pcs).`
    } else if (currentSatuanPerDus > 1 && num % currentSatuanPerDus !== 0) {
      isError = true
      errorMsg = `Jumlah harus kelipatan ${currentSatuanPerDus}.`
    }

    $("#editJumlahKemas").toggleClass("input-error", isError)
    $("#editErrorMsg").text(errorMsg).toggle(isError)
    $("#submitEdit")
      .prop("disabled", isError)
      .css("opacity", isError ? "0.6" : "1")

    return !isError
  }

  $("body").on("click", ".btn-edit", function () {
    const id = $(this).data("id")
    if (!id) return alert("ID tidak ditemukan!")

    $.post(
      selfUrl + "/get-detail",
      { id: id },
      (response) => {
        if (response.success && response.data) {
          const data = response.data
          currentSatuanPerDus = data.satuan_per_dus || 1
          currentMaxKemas = data.stok_siap_kemas_plus_current || 0

          $("#editDetailId").val(data.id)
          $("#editNamaProduk").text(data.nama_produk)
          $("#editJumlahKemas").val(data.jumlah_kemas)
          $("#maxKemas").text(currentMaxKemas.toLocaleString())
          $("#satuanPerDus").text(currentSatuanPerDus.toLocaleString())

          validateEditInput()
          modal.show()
        } else {
          alert("Gagal mengambil data: " + (response.message || "Error"))
        }
      },
      "json",
    )
  })

  $("body").on("click", ".btn-delete", function () {
    if (!confirm("Yakin ingin menghapus data ini? Stok produk jadi di Gudang Overpack akan dikurangi.")) return

    const id = $(this).data("id")
    if (!id) return alert("ID tidak ditemukan!")

    const btn = $(this)
    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop("disabled", true)

    $.post(
      selfUrl + "/delete-kemas-ulang",
      { id: id },
      (response) => {
        if (response.success) {
          $("#row-" + id).fadeOut(500, function () {
            $(this).remove()
            updateRowCount()
          })
          alert(response.message)
        } else {
          alert("Error: " + response.message)
          btn.html('<i class="fas fa-trash"></i> Hapus').prop("disabled", false)
        }
      },
      "json",
    )
  })

  $("#editJumlahKemas").on("input", validateEditInput)

  $("#editForm").on("submit", function (e) {
    e.preventDefault()
    if (!validateEditInput()) return

    const submitBtn = $("#submitEdit")
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop("disabled", true)

    $.post(
      selfUrl + "/update-kemas-ulang",
      $(this).serialize(),
      (response) => {
        if (response.success) {
          modal.hide()
          alert(response.message)
          fetchFilteredData()
        } else {
          alert("Error: " + response.message)
        }
        submitBtn.html('<i class="fas fa-save"></i> Update Data')
        validateEditInput()
      },
      "json",
    )
  })

  $(".close").on("click", () => {
    modal.hide()
  })

  $(window).on("click", (event) => {
    if ($(event.target).is(modal)) modal.hide()
  })

  // Muat data pertama kali
  fetchFilteredData()
})
