$(document).ready(() => {
  const selfUrl = `${baseUrl}/operpack_seleksi`;
  const modal = $("#editModal")
  const loadingState = $("#loadingState")
  const dataTableBody = $("#dataTableBody")

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
        dataTableBody.html('<tr><td colspan="5" class="text-center text-danger">Gagal memuat data.</td></tr>')
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
    const stokTersedia = Number.parseInt($("#stokRusakBelumSeleksi").text()) || 0
    const originalAman = Number.parseInt($("#originalPcsAman").val()) || 0
    const originalCurah = Number.parseInt($("#originalPcsCurah").val()) || 0
    const newAman = Number.parseInt($("#editPcsAman").val()) || 0
    const newCurah = Number.parseInt($("#editPcsCurah").val()) || 0

    const totalSelisih = newAman - originalAman + (newCurah - originalCurah)
    const isError = totalSelisih > stokTersedia

    $("#editPcsAman, #editPcsCurah").toggleClass("input-error", isError)
    $("#editErrorMsg").toggle(isError)
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
          $("#editDetailId").val(data.id)
          $("#editNamaProduk").text(data.nama_produk)
          $("#editPcsAman").val(data.pcs_aman)
          $("#editPcsCurah").val(data.pcs_curah)
          $("#originalPcsAman").val(data.pcs_aman)
          $("#originalPcsCurah").val(data.pcs_curah)
          $("#stokRusakBelumSeleksi").text(data.stok_rusak_belum_seleksi || 0)

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
    if (!confirm("Anda yakin ingin menghapus riwayat ini?")) return

    const id = $(this).data("id")
    if (!id) return alert("ID tidak ditemukan!")

    const btn = $(this)
    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop("disabled", true)

    $.post(
      selfUrl + "/delete-seleksi",
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

  $("#editPcsAman, #editPcsCurah").on("input", validateEditInput)

  $("#editForm").on("submit", function (e) {
    e.preventDefault()
    if (!validateEditInput()) return

    const submitBtn = $("#submitEdit")
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop("disabled", true)

    $.post(
      selfUrl + "/update-seleksi",
      $(this).serialize(),
      (response) => {
        if (response.success) {
          modal.hide()
          alert(response.message)
          fetchFilteredData() // Refresh data
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
