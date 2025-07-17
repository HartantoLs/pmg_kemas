<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="/css/form_penjualan.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="main-container">
    <div class="form-card anim-slide-up">
        <div class="form-header">
            <h1><i class="fas fa-shopping-cart"></i> Form Penjualan (Stok Keluar)</h1>
        </div>
        <div class="form-content">
            <div id="formMessage" class="alert"></div>
            <form id="formPenjualan">
                <div class="section-card">
                    <h3 class="section-title"><i class="fas fa-file-invoice"></i> Informasi Penjualan</h3>
                    <div class="grid-container">
                        <div class="form-group">
                            <label for="no_surat_jalan"><i class="fas fa-receipt"></i> No. Surat Jalan</label>
                            <input type="text" name="no_surat_jalan" id="no_surat_jalan" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="pelat_mobil"><i class="fas fa-truck"></i> No. Pelat Mobil</label>
                            <input type="text" name="pelat_mobil" id="pelat_mobil" class="form-control" placeholder="B 1234 ABC">
                        </div>
                        <div class="form-group">
                            <label for="customer"><i class="fas fa-user-tie"></i> Customer</label>
                            <input type="text" name="customer" id="customer" class="form-control" placeholder="Nama customer">
                        </div>
                        <div class="form-group">
                            <label for="tanggal"><i class="fas fa-calendar"></i> Tanggal Penjualan</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="summary-card" id="summaryCard" style="display: none;">
                    <h3 class="section-title"><i class="fas fa-chart-bar"></i> Ringkasan Penjualan</h3>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="label">Total Item</div>
                            <div class="value" id="totalItems">0</div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Total Dus</div>
                            <div class="value" id="totalDus">0</div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Total Satuan</div>
                            <div class="value" id="totalSatuan">0</div>
                        </div>
                        <div class="summary-item">
                            <div class="label">Status Stok</div>
                            <div class="value" id="stockStatus">✓ Aman</div>
                        </div>
                    </div>
                </div>
                <div class="items-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="section-title mb-0">Item Produk</h3>
                        <button type="button" id="btnTambahItem" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Item</button>
                    </div>
                    <div id="items-container"></div>
                </div>

                <div class="submit-section">
                    <button type="submit" class="submit-btn"><i class="fas fa-save"></i> Simpan Penjualan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
    // Variabel ini akan di-passing dari PHP ke JS
    const produkOptions = `<?php foreach ($produk_list as $p) { echo "<option value='{$p['id_produk']}'>".esc($p['nama_produk'])."</option>"; } ?>`;
    const gudangOptions = `<?php foreach ($gudang_list as $g) { echo "<option value='{$g['id_gudang']}'>".esc($g['nama_gudang'])."</option>"; } ?>`;
</script>
<script src="/js/form_penjualan.js"></script>
<?= $this->endSection() ?>