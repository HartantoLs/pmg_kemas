let bahanBakuCounter = 0
let barangList = []
const $ = window.jQuery

$(document).ready(() => {
  // Load barang list for dropdown
  loadBarangList()
})

function showForm() {
  const dataType = $("#dataType").val()

  // Hide all sections first
  $(".data-section").hide()

  if (dataType) {
    // Show the selected section with animation
    $(`#${dataType}Section`).fadeIn(300)

    // Load data for the selected type
    switch (dataType) {
      case "gudang":
        loadGudangList()
        break
      case "produk":
        loadProdukList()
        break
      case "jenis_produksi":
        loadJenisProduksiList()
        break
    }
  }
}

// Modal Functions
function openModal(modalId) {
  $(`#${modalId}`).fadeIn(300)
  $("body").addClass("modal-open")
}

function closeModal(modalId) {
  $(`#${modalId}`).fadeOut(300)
  $("body").removeClass("modal-open")

  // Reset form when closing
  const formId = modalId.replace("Modal", "")
  resetForm(formId)
}

// Close modal when clicking outside
$(document).on("click", ".modal", function (e) {
  if (e.target === this) {
    $(this).fadeOut(300)
    $("body").removeClass("modal-open")
  }
})

// Gudang Functions
function loadGudangList() {
  showLoading()
  $.get("/admin/getgudanglist")
    .done((response) => {
      if (response.success) {
        let tbody = ""
        if (response.data.length === 0) {
          tbody = `
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h3>Tidak ada data gudang</h3>
                                <p>Silakan tambah data gudang baru</p>
                            </td>
                        </tr>
                    `
        } else {
          response.data.forEach((item) => {
            tbody += `
                            <tr>
                                <td>${item.id_gudang}</td>
                                <td class="text-left">${item.nama_gudang}</td>
                                <td><span class="badge badge-info">${item.tipe_gudang}</span></td>
                                <td>
                                    <button class="btn btn-edit" onclick="editGudang(${item.id_gudang}, '${item.nama_gudang}', '${item.tipe_gudang}')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-delete" onclick="deleteGudang(${item.id_gudang})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `
          })
        }
        $("#gudangTable tbody").html(tbody)
      }
    })
    .fail(() => {
      showAlert("error", "Gagal memuat data gudang")
    })
    .always(() => {
      hideLoading()
    })
}

function editGudang(id, nama, tipe) {
  $("#gudang_id").val(id)
  $("#nama_gudang").val(nama)
  $("#tipe_gudang").val(tipe)
  openModal("gudangModal")
}

function deleteGudang(id) {
  if (confirm("Apakah Anda yakin ingin menghapus gudang ini?")) {
    showLoading()
    $.post("/admin/deletegudang", { id: id })
      .done((response) => {
        if (response.success) {
          showAlert("success", response.message)
          loadGudangList()
        } else {
          showAlert("error", response.message)
        }
      })
      .fail(() => {
        showAlert("error", "Gagal menghapus data gudang")
      })
      .always(() => {
        hideLoading()
      })
  }
}

$("#formGudang").on("submit", function (e) {
  e.preventDefault()
  showLoading()

  $.post("/admin/savegudang", $(this).serialize())
    .done((response) => {
      if (response.success) {
        showAlert("success", response.message)
        loadGudangList()
        closeModal("gudangModal")
      } else {
        showAlert("error", response.message)
      }
    })
    .fail(() => {
      showAlert("error", "Gagal menyimpan data gudang")
    })
    .always(() => {
      hideLoading()
    })
})

// Produk Functions
function loadProdukList() {
  showLoading()
  $.get("/admin/getproduklist")
    .done((response) => {
      if (response.success) {
        let tbody = ""
        if (response.data.length === 0) {
          tbody = `
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h3>Tidak ada data produk</h3>
                                <p>Silakan tambah data produk baru</p>
                            </td>
                        </tr>
                    `
        } else {
          response.data.forEach((item) => {
            tbody += `
                            <tr>
                                <td>${item.id_produk}</td>
                                <td class="text-left">${item.nama_produk}</td>
                                <td><span class="badge badge-primary">${item.satuan_per_dus}</span></td>
                                <td>
                                    <button class="btn btn-edit" onclick="editProduk(${item.id_produk}, '${item.nama_produk}', ${item.satuan_per_dus})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-delete" onclick="deleteProduk(${item.id_produk})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `
          })
        }
        $("#produkTable tbody").html(tbody)
      }
    })
    .fail(() => {
      showAlert("error", "Gagal memuat data produk")
    })
    .always(() => {
      hideLoading()
    })
}

function editProduk(id, nama, satuan) {
  $("#produk_id").val(id)
  $("#nama_produk").val(nama)
  $("#satuan_per_dus").val(satuan)
  openModal("produkModal")
}

function deleteProduk(id) {
  if (confirm("Apakah Anda yakin ingin menghapus produk ini?")) {
    showLoading()
    $.post("/admin/deleteproduk", { id: id })
      .done((response) => {
        if (response.success) {
          showAlert("success", response.message)
          loadProdukList()
        } else {
          showAlert("error", response.message)
        }
      })
      .fail(() => {
        showAlert("error", "Gagal menghapus data produk")
      })
      .always(() => {
        hideLoading()
      })
  }
}

$("#formProduk").on("submit", function (e) {
  e.preventDefault()
  showLoading()

  $.post("/admin/saveproduk", $(this).serialize())
    .done((response) => {
      if (response.success) {
        showAlert("success", response.message)
        loadProdukList()
        closeModal("produkModal")
      } else {
        showAlert("error", response.message)
      }
    })
    .fail(() => {
      showAlert("error", "Gagal menyimpan data produk")
    })
    .always(() => {
      hideLoading()
    })
})

// Jenis Produksi Functions
function loadJenisProduksiList() {
  showLoading()
  $.get("/admin/getjenisproduksilist")
    .done((response) => {
      if (response.success) {
        let tbody = ""
        if (response.data.length === 0) {
          tbody = `
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h3>Tidak ada data jenis produksi</h3>
                                <p>Silakan tambah data jenis produksi baru</p>
                            </td>
                        </tr>
                    `
        } else {
          response.data.forEach((item) => {
            tbody += `
                            <tr>
                                <td>${item.nom_jenis_produksi}</td>
                                <td class="text-left">${item.jenis_produksi}</td>
                                <td><span class="badge badge-success">${item.group_jenis_produksi}</span></td>
                                <td class="text-left">${item.keterangan || "-"}</td>
                                <td>
                                    <button class="btn btn-edit" onclick="editJenisProduksi(${item.nom_jenis_produksi})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-delete" onclick="deleteJenisProduksi(${item.nom_jenis_produksi})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `
          })
        }
        $("#jenisProduksiTable tbody").html(tbody)
      }
    })
    .fail(() => {
      showAlert("error", "Gagal memuat data jenis produksi")
    })
    .always(() => {
      hideLoading()
    })
}

function loadBarangList() {
  $.get("/admin/getbaranglist")
    .done((response) => {
      if (response.success) {
        barangList = response.data
      }
    })
    .fail(() => {
      showAlert("error", "Gagal memuat data barang")
    })
}

function addBahanBaku() {
  bahanBakuCounter++

  let barangOptions = '<option value="">-- Pilih Barang --</option>'
  barangList.forEach((barang) => {
    barangOptions += `<option value="${barang.kode_barang}">${barang.nama_barang} (${barang.satuan})</option>`
  })

  const bahanCard = `
        <div class="bahan-baku-card" id="bahanCard${bahanBakuCounter}">
            <div class="card-header">
                <span><i class="fas fa-cube"></i> Bahan Baku #${bahanBakuCounter}</span>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeBahanBaku(${bahanBakuCounter})" title="Hapus">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Barang</label>
                    <select class="bahan-barang" name="bahan_barang[]" required>
                        ${barangOptions}
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" class="bahan-jumlah" name="bahan_jumlah[]" step="0.01" required>
                </div>
            </div>
        </div>
    `

  $("#bahanBakuContainer").append(bahanCard)
  $(`#bahanCard${bahanBakuCounter}`).hide().fadeIn(300)
}

function removeBahanBaku(id) {
  $(`#bahanCard${id}`).fadeOut(300, function () {
    $(this).remove()
  })
}

function editJenisProduksi(id) {
  showLoading()
  $.get("/admin/getjenisproduksidetail", { id: id })
    .done((response) => {
      if (response.success) {
        const data = response.data

        // Fill form data
        $("#jenis_produksi_id").val(data.jenis_produksi.nom_jenis_produksi)
        $("#jenis_produksi").val(data.jenis_produksi.jenis_produksi)
        $("#group_jenis_produksi").val(data.jenis_produksi.group_jenis_produksi)
        $("#keterangan").val(data.jenis_produksi.keterangan)
        $("#is_edit").val("1")

        // Hide satuan per dus field for edit mode
        $("#satuanPerDusGroup").hide()

        // Clear and populate bahan baku
        $("#bahanBakuContainer").empty()
        bahanBakuCounter = 0

        if (data.bahan_baku && data.bahan_baku.length > 0) {
          data.bahan_baku.forEach((bahan) => {
            addBahanBaku()
            const currentCard = $(`#bahanCard${bahanBakuCounter}`)
            currentCard.find(".bahan-barang").val(bahan.kode_barang)
            currentCard.find(".bahan-jumlah").val(bahan.jumlah)
          })
        }

        openModal("jenisProduksiModal")
      }
    })
    .fail(() => {
      showAlert("error", "Gagal memuat detail jenis produksi")
    })
    .always(() => {
      hideLoading()
    })
}

function deleteJenisProduksi(id) {
  if (
    confirm(
      "Apakah Anda yakin ingin menghapus jenis produksi ini?\n\nPerhatian: Data bahan baku terkait juga akan dihapus.",
    )
  ) {
    showLoading()
    $.post("/admin/deletejenisproduksi", { id: id })
      .done((response) => {
        if (response.success) {
          showAlert("success", response.message)
          loadJenisProduksiList()
        } else {
          showAlert("error", response.message)
        }
      })
      .fail(() => {
        showAlert("error", "Gagal menghapus data jenis produksi")
      })
      .always(() => {
        hideLoading()
      })
  }
}

$("#formJenisProduksi").on("submit", function (e) {
  e.preventDefault()

  // Collect bahan baku data
  const bahanBaku = []
  $(".bahan-baku-card").each(function () {
    const kodeBarang = $(this).find(".bahan-barang").val()
    const jumlah = $(this).find(".bahan-jumlah").val()

    if (kodeBarang && jumlah) {
      bahanBaku.push({
        kode_barang: kodeBarang,
        jumlah: Number.parseFloat(jumlah),
      })
    }
  })

  const formData = $(this).serialize() + "&bahan_baku=" + encodeURIComponent(JSON.stringify(bahanBaku))

  showLoading()
  $.post("/admin/savejenisproduksi", formData)
    .done((response) => {
      if (response.success) {
        showAlert("success", response.message)
        loadJenisProduksiList()
        closeModal("jenisProduksiModal")
      } else {
        showAlert("error", response.message)
      }
    })
    .fail(() => {
      showAlert("error", "Gagal menyimpan data jenis produksi")
    })
    .always(() => {
      hideLoading()
    })
})

// Utility Functions
function resetForm(type) {
  switch (type) {
    case "gudang":
      $("#formGudang")[0].reset()
      $("#gudang_id").val("")
      break
    case "produk":
      $("#formProduk")[0].reset()
      $("#produk_id").val("")
      break
    case "jenisProduksi":
      $("#formJenisProduksi")[0].reset()
      $("#jenis_produksi_id").val("")
      $("#is_edit").val("0")
      $("#bahanBakuContainer").empty()
      $("#satuanPerDusGroup").show() // Show satuan per dus field for add mode
      bahanBakuCounter = 0
      break
  }
}

function showLoading() {
  $("#loadingOverlay").fadeIn(200)
}

function hideLoading() {
  $("#loadingOverlay").fadeOut(200)
}

function showAlert(type, message) {
  const alertClass = type === "success" ? "alert-success" : "alert-error"
  const iconClass = type === "success" ? "fas fa-check-circle" : "fas fa-exclamation-triangle"

  const alertHtml = `
        <div class="alert ${alertClass}">
            <i class="${iconClass}"></i>
            ${message}
        </div>
    `

  // Remove existing alerts
  $("#alertContainer .alert").remove()

  // Add new alert
  $("#alertContainer").html(alertHtml)
  $("#alertContainer .alert").show()

  // Auto hide after 5 seconds
  setTimeout(() => {
    $("#alertContainer .alert").fadeOut(500, function () {
      $(this).remove()
    })
  }, 5000)

  // Scroll to alert
  $("html, body").animate(
    {
      scrollTop: $("#alertContainer").offset().top - 100,
    },
    300,
  )
}
