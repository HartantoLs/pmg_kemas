;(() => {
  const selfUrl = "/fisik_harian"
  const $ = window.jQuery // Declare the $ variable

  function showLoading() {
    $("#loading-overlay").show()
  }

  function hideLoading() {
    $("#loading-overlay").hide()
  }

  $(document).ready(() => {
    // Filter button click
    $("#btnFilter").on("click", () => {
      const formData = {
        tanggal_dari: $("#tanggal_dari").val(),
        tanggal_sampai: $("#tanggal_sampai").val(),
        produk_id: $("#produk_id").val(),
        gudang_id: $("#gudang_id").val(),
      }

      showLoading()

      $.ajax({
        url: selfUrl + "/filterData",
        method: "POST",
        data: formData,
        success: (response) => {
          $("#tableContainer").html(response)
        },
        error: () => {
          $("#tableContainer").html(`
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                            <p class="text-danger">Terjadi kesalahan saat memuat data</p>
                        </div>
                    `)
        },
        complete: () => {
          hideLoading()
        },
      })
    })

    // Reset button click
    $("#btnReset").on("click", () => {
      $("#filterForm")[0].reset()
      $("#tableContainer").html(`
                <div class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Gunakan filter untuk menampilkan data</p>
                </div>
            `)
    })
  })
})()
