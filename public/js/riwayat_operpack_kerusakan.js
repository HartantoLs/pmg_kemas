// Import jQuery library
$(document).ready(() => {
  const modal = $("#editModal")
  const dataTableBody = $("#dataTableBody")
  const loadingState = $("#loadingState")
  const baseUrl = "/operpack_kerusakan"

  // Variabel untuk validasi stok
  const stokTersedia = { dus: 0, satuan: 0 }
  let penjualanData = null

  function showNotification(message, type = "success") {
    const toast = $("#notification-toast")
    toast.removeClass("success error").addClass(type)
    toast.text(message).fadeIn()
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
        produk_id: $("#produk_id").val(),
        kategori_asal: $("#kategori_asal").val(),
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
    const rowCount = $("#dataTableBody tr").length
    $("#totalRows").text(rowCount)
  }

  function validateInputs() {
    const dusBaru = Number.parseInt($("#editJumlahDus").val()) || 0
    const satuanBaru = Number.parseInt($("#editJumlahSatuan").val()) || 0
    let isError = false

    // Untuk kategori Internal, validasi dengan stok tersedia
    if (stokTersedia.dus > 0 || stokTersedia.satuan > 0) {
      if (dusBaru > stokTersedia.dus) {
        $("#editJumlahDus").addClass("input-error")
        isError = true
      } else {
        $("#editJumlahDus").removeClass("input-error")
      }

      if (satuanBaru > stokTersedia.satuan) {
        $("#editJumlahSatuan").addClass("input-error")
        isError = true
      } else {
        $("#editJumlahSatuan").removeClass("input-error")
      }

      if (isError) {
        $("#editErrorMsg").text("Stok tidak mencukupi untuk jumlah afkir baru!").show()
        $("#submitBtn").prop("disabled", true).css("opacity", "0.6")
      } else {
        $("#editErrorMsg").hide()
        $("#submitBtn").prop("disabled", false).css("opacity", "1")
      }
    } else {
      // Untuk kategori Eksternal, tidak ada validasi stok ketat
      $("#editJumlahDus, #editJumlahSatuan").prop("disabled", false).removeClass("input-error")
      $("#editErrorMsg").hide()
      $("#submitBtn").prop("disabled", false).css("opacity", "1")
    }
  }

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

          // Reset tampilan modal
          $("#editForm").trigger("reset")
          $("#editErrorMsg").hide()
          $("#stokInfoContainer").hide()
          $("#penjualanInfoContainer").hide()
          $("#eksternalAlert").hide()
          $("#submitBtn").show().prop("disabled", false).css("opacity", "1")
          $("#editJumlahDus, #editJumlahSatuan").prop("disabled", false).removeClass("input-error")

          // Isi data utama
          $("#editDetailId").val(data.id)
          $("#editProdukId").val(data.produk_id)
          $("#editNamaProduk").text(data.nama_produk)
          $("#editAsal").text(data.kategori_asal + " - " + data.asal)
          $("#editJumlahDus").val(data.jumlah_dus_kembali)
          $("#editJumlahSatuan").val(data.jumlah_satuan_kembali)
          $("#editJumlahDusLama").val(data.jumlah_dus_kembali)
          $("#editJumlahSatuanLama").val(data.jumlah_satuan_kembali)

          if (data.kategori_asal === "Internal") {
            $("#editGudangAsalId").val(data.gudang_asal_id)

            // Ambil stok jika internal
            const tanggal = data.waktu_diterima.split(" ")[0] // Ambil tanggal saja
            $.post(
              `${baseUrl}/getstock`,
              {
                produk_id: data.produk_id,
                gudang_id: data.gudang_asal_id,
                tanggal: tanggal,
              },
              (stokData) => {
                const dusLama = Number.parseInt(data.jumlah_dus_kembali) || 0
                const satuanLama = Number.parseInt(data.jumlah_satuan_kembali) || 0

                stokTersedia.dus = (stokData.dus || 0) + dusLama
                stokTersedia.satuan = (stokData.satuan || 0) + satuanLama

                $("#editStokInfo").text(`${stokTersedia.dus} Dus, ${stokTersedia.satuan} Satuan (termasuk item ini)`)
                $("#stokInfoContainer").show()
                validateInputs() // Validasi awal
              },
              "json",
            )
          } else if (data.kategori_asal === "Eksternal") {
            // Jika Eksternal, tampilkan info penjualan
            penjualanData = data.penjualan_data
            if (penjualanData) {
              $("#editPenjualanInfo").html(`
                            <div><strong>Data Penjualan:</strong></div>
                            <div>Terjual: ${penjualanData.jumlah_dus} dus, ${penjualanData.jumlah_satuan} satuan</div>
                            <div>Gudang: ${penjualanData.nama_gudang}</div>
                        `)
              $("#penjualanInfoContainer").show()
            }

            // Reset stok tersedia untuk eksternal
            stokTersedia.dus = 0
            stokTersedia.satuan = 0
            validateInputs() // Validasi awal
          }

          modal.show()
        } else {
          showNotification("Gagal mengambil data detail.", "error")
        }
        btn.html('<i class="fas fa-edit"></i> Edit').prop("disabled", false)
      },
      "json",
    )
  })

  // Event listener untuk input jumlah
  $("#editJumlahDus, #editJumlahSatuan").on("input", validateInputs)

  $("body").on("click", ".btn-delete", function () {
    if (!confirm("Anda yakin ingin menghapus riwayat ini? Jika dari Internal, stok akan dikembalikan.")) return

    const id = $(this).data("id")
    $.post(
      `${baseUrl}/hapusriwayat`,
      { id: id },
      (response) => {
        if (response.success) {
          showNotification(response.message, "success")
          fetchFilteredData()
        } else {
          showNotification(response.message, "error")
        }
      },
      "json",
    )
  })

  $("#editForm").on("submit", function (e) {
    e.preventDefault()
    const submitBtn = $("#submitBtn")
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

  // Search functionality
  $("#searchInput").on("keyup", function () {
    const value = $(this).val().toLowerCase()
    $("#dataTableBody tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    })
  })

  // Initialize
  fetchFilteredData()
})
