<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="<?= base_url('css/form_operstock.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="main-container">
    <div class="form-card anim-slide-up">
        <div class="form-header">
            <h1><i class="fas fa-exchange-alt"></i> Form Pindah Stok Antar Gudang</h1>
        </div>
        <div class="form-content">
            <div id="formMessage" class="alert"></div>
            <form id="formOperstock">
                <div class="section-card">
                    <h3 class="section-title"><i class="fas fa-file-alt"></i> Detail Perpindahan</h3>
                    <div class="grid-container">
                        <div class="form-group">
                            <label for="no_surat_jalan"><i class="fas fa-receipt"></i> No. Surat Jalan / Referensi</label>
                            <input type="text" name="no_surat_jalan" id="no_surat_jalan" class="form-control" required>
                            <div class="validation-error">Nomor surat jalan wajib diisi</div>
                        </div>
                        <div class="form-group">
                            <label for="tanggal"><i class="fas fa-calendar"></i> Tanggal Kirim</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                            <div class="validation-error">Tanggal wajib diisi</div>
                        </div>
                        <div class="form-group">
                            <label for="gudang_asal"><i class="fas fa-warehouse"></i> Gudang Asal</label>
                            <select name="gudang_asal" id="gudang_asal" class="form-control" required>
                                <option value="">-- Pilih Gudang Asal --</option>
                                <?php foreach ($gudang_list as $gudang): ?>
                                    <option value="<?= $gudang['id_gudang'] ?>"><?= esc($gudang['nama_gudang']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="validation-error">Pilih gudang asal terlebih dahulu</div>
                        </div>
                        <div class="form-group">
                            <label for="gudang_tujuan"><i class="fas fa-warehouse"></i> Gudang Tujuan</label>
                            <select name="gudang_tujuan" id="gudang_tujuan" class="form-control" required>
                                <option value="">-- Pilih Gudang Tujuan --</option>
                                <?php foreach ($gudang_list as $gudang): ?>
                                    <option value="<?= $gudang['id_gudang'] ?>"><?= esc($gudang['nama_gudang']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="validation-error">Pilih gudang tujuan terlebih dahulu</div>
                            <div class="transfer-history" id="transferHistory" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="warehouse-flow" id="warehouseFlow" style="display: none;">
                        <div class="warehouse-box asal">
                            <div><i class="fas fa-upload"></i> ASAL</div>
                            <div id="namaGudangAsal">-</div>
                        </div>
                        <div class="flow-arrow"><i class="fas fa-arrow-right"></i></div>
                        <div class="warehouse-box tujuan">
                            <div><i class="fas fa-download"></i> TUJUAN</div>
                            <div id="namaGudangTujuan">-</div>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="summary-card" id="summaryCard" style="display: none;">
                    <h3 class="section-title"><i class="fas fa-chart-bar"></i> Ringkasan Transfer</h3>
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
                            <div class="value" id="stockStatus">✓ Siap Transfer</div>
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
                    <button type="submit" class="submit-btn"><i class="fas fa-exchange-alt"></i> Simpan Perpindahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
    const BASE_URL = '<?= base_url() ?>';
    const produkOptions = `<?php foreach ($produk_list as $p) { echo "<option value='{$p['id_produk']}' data-satuan-per-dus='{$p['satuan_per_dus']}'>".esc($p['nama_produk'])."</option>"; } ?>`;
</script>
<script src="<?= base_url('js/form_operstock.js') ?>"></script>
<?= $this->endSection() ?>
