<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="<?= base_url('css/form_operpack_kerusakan.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="main-container">
    <div class="form-card anim-fade-in">
        <div class="form-header">
            <h1>
                <i class="fas fa-screwdriver-wrench "></i>
                Form Penerimaan Barang Rusak
            </h1>
        </div>
        <div class="form-content">
            <div id="formMessage" class="alert"></div>
            
            <form id="formKerusakan">
                <!-- Header Information -->
                <div class="section-card anim-slide-up">
                    <h3 class="section-title">
                        <i class="fas fa-file-alt"></i>
                        Detail Penerimaan
                    </h3>
                    <div class="grid-container">
                        <div class="form-group">
                            <label for="no_surat_jalan">
                                <i class="fas fa-receipt"></i>
                                No. Surat Jalan / Referensi
                            </label>
                            <input type="text" name="no_surat_jalan" id="no_surat_jalan" class="form-control" required>
                            <div class="validation-error">Nomor surat jalan wajib diisi</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="tanggal">
                                <i class="fas fa-calendar"></i>
                                Tanggal Diterima
                            </label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            <div class="validation-error">Tanggal wajib diisi</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="kategori_asal">
                                <i class="fas fa-tags"></i>
                                Kategori Asal
                            </label>
                            <select name="kategori_asal" id="kategori_asal" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Eksternal">Eksternal (dari Customer)</option>
                                <option value="Internal">Internal (dari Gudang Produksi)</option>
                            </select>
                            <div class="validation-error">Pilih kategori asal terlebih dahulu</div>
                        </div>
                        
                        <div id="asal-wrapper" class="form-group">
                            <!-- Dynamic content based on kategori_asal -->
                        </div>
                    </div>
                    
                    <!-- Source Indicator -->
                    <div class="source-indicator" id="sourceIndicator" style="display: none;">
                        <div class="source-box" id="sourceBox">
                            <div class="source-type">
                                <i class="fas fa-map-marker-alt"></i> <span id="sourceType">SUMBER</span>
                            </div>
                            <div id="sourceName" class="source-name">-</div>
                        </div>
                    </div>
                    <div class="damage-history" id="damageHistory"></div>
                </div>
                
                <!-- Summary Card -->
                <div class="summary-card anim-slide-up" id="summaryCard" style="display: none; animation-delay: 0.1s;">
                    <h3 class="section-title">
                        <i class="fas fa-chart-bar"></i>
                        Ringkasan Kerusakan
                    </h3>
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
                            <div class="label">Total PCS</div>
                            <div class="value" id="totalPcs" style="color: #dc2626;">0</div>
                        </div>
                    </div>
                </div>
                
                <!-- Items Section -->
                <div class="items-section anim-slide-up" style="animation-delay: 0.2s;">
                    <div class="section-title">
                        <i class="fas fa-boxes"></i>
                        Item Produk Rusak
                        <div class="section-actions">
                            <button type="button" id="btnTambahItem" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Tambah Item
                            </button>
                        </div>
                    </div>
                    <div id="items-container"></div>
                </div>
                
                <div class="submit-section">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i>
                        Simpan Data Kerusakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Pass produk data to JavaScript
const produkOptions = `<?php foreach ($produk_list as $produk): ?>
    <option value="<?= $produk['id_produk'] ?>" data-satuan-per-dus="<?= $produk['satuan_per_dus'] ?>">
        <?= esc($produk['nama_produk']) ?>
    </option>
<?php endforeach; ?>`;
</script>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="<?= base_url('js/form_operpack_kerusakan.js') ?>"></script>
<?= $this->endSection() ?>
