$(document).ready(() => {
  const selfUrl = `${baseUrl}/laporan`;
  let filterTimeout;
  const $ = window.jQuery;

  function fetchData() {
    const formData = {
      tanggal_laporan: $("#tanggal_laporan").val(),
      id_gudang: $("#id_gudang").val(),
      search: $("#search").val(),
    };

    // ==================================================
    // PERUBAHAN DIMULAI DI SINI
    // ==================================================

    // Tampilkan loading overlay sebelum request dimulai
    $(".loading-overlay").show();
    // Untuk pengalaman pengguna yang lebih baik, Anda bisa menyembunyikan data lama
    // $("#table-body").hide(); 

    $.post(
      `${selfUrl}/filterstok`,
      formData,
      (response) => {
        if (response.success) {
          // Update table body
          $("#table-body").html(response.table_body);

          // Update stats
          $("#stat-total-products").text(Number(response.stats.total_products).toLocaleString("id-ID"));
          $("#stat-total-dus").text(Number(response.stats.total_dus).toLocaleString("id-ID"));
          $("#stat-total-satuan").text(Number(response.stats.total_satuan).toLocaleString("id-ID"));
          $("#stat-products-with-stock").text(Number(response.stats.products_with_stock).toLocaleString("id-ID"));

          // Update table header title with date and gudang
          const selectedDate = new Date($("#tanggal_laporan").val()).toLocaleDateString("id-ID", {
            day: "numeric",
            month: "long",
            year: "numeric",
          });
          const selectedGudangText = $("#id_gudang option:selected").text().trim();
          $("#table-title").html(`<i class="fas fa-table"></i> Stok Produk - ${selectedDate} - ${selectedGudangText}`);

          // Update table row count
          $("#table-row-count").text(`${response.stats.total_products} Produk`);
        } else {
          $("#table-body").html(
            '<tr><td colspan="5" class="text-center text-danger p-5">Gagal memuat data: ' +
            response.message +
            "</td></tr>",
          );
        }
      },
      "json",
    ).fail(() => {
      $("#table-body").html(
        '<tr><td colspan="5" class="text-center text-danger p-5">Gagal memuat data. Silakan coba lagi.</td></tr>',
      );
    }).always(() => {
      // Selalu jalankan ini setelah request selesai (baik sukses maupun gagal)
      // Sembunyikan loading overlay
      $(".loading-overlay").hide();
      // Tampilkan kembali table body jika Anda menyembunyikannya
      // $("#table-body").show();
    });

    // ==================================================
    // PERUBAHAN SELESAI DI SINI
    // ==================================================
  }

  // Event listeners for filters
  $("#tanggal_laporan, #id_gudang, #search").on("change input", () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(fetchData, 500);
  });

  // Clear search function
  window.clearSearch = () => {
    $("#search").val("");
    fetchData();
  };

  // Export to CSV function
  window.exportToCSV = () => {
    const formData = {
      tanggal_laporan: $("#tanggal_laporan").val(),
      id_gudang: $("#id_gudang").val(),
      search: $("#search").val(),
    };

    // Create a form and submit it
    const form = $("<form>", {
      method: "POST",
      action: `${selfUrl}/exportstokcsv`,
    });

    // Add form data as hidden inputs
    Object.keys(formData).forEach((key) => {
      form.append(
        $("<input>", {
          type: "hidden",
          name: key,
          value: formData[key],
        }),
      );
    });

    $("body").append(form);
    form.submit();
    form.remove();
  };

  // Initial data load
  fetchData();
});
