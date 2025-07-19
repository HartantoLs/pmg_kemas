<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="/css/form_operpack_kemas_ulang.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-box-open"></i> Form Input Hasil Kemas Ulang</h1>
        <p>Input hasil kemas ulang produk overpack dengan sistem yang akurat</p>
    </div>

    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-edit"></i>
            <h2>Data Kemas Ulang Produk</h2>
        </div>
        
        <div class="form-body">
            <div id="formMessage" class="alert"></div>
            
            <form id="form-repack">
                <input type="hidden" name="action" value="simpan_repack">
                
                <div class="form-group">
                    <label for="repack_produk">
                        <i class="fas fa-box"></i> Pilih Produk
                    </label>
                    <select name="id_produk" id="repack_produk" class="form-control" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach ($produk_list as $produk): ?>
                            <option value="<?= $produk['id_produk'] ?>">
                                <?= esc($produk['nama_produk']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="stok-display" class="stok-display"></div>
                    
                    <div class="info-panels" id="info-panels" style="display: none;">
                        <div class="info-panel">
                            <h4><i class="fas fa-chart-bar"></i> Detail Stok</h4>
                            <div class="info-row">
                                <span class="info-label">Hasil Seleksi Aman:</span>
                                <span class="info-value" id="info-seleksi-aman">0 Pcs</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Sudah Dikemas:</span>
                                <span class="info-value" id="info-sudah-kemas">0 Pcs</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Tersedia untuk Dikemas:</span>
                                <span class="info-value" id="info-tersedia">0 Pcs</span>
                            </div>
                        </div>
                        
                        <div class="info-panel">
                            <h4><i class="fas fa-calculator"></i> Kapasitas Kemas</h4>
                            <div id="kapasitas-content">
                                <!-- Dynamic content -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="conversion-panel" id="conversion-panel">
                        <div class="conversion-highlight" id="conversion-highlight"></div>
                        <div class="conversion-detail" id="conversion-detail"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="repack_tanggal">
                        <i class="fas fa-calendar"></i> Tanggal Kemas Ulang
                    </label>
                    <input type="date" name="tanggal" id="repack_tanggal" class="form-control"
                           value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label for="jumlah_kemas_unit">
                        <i class="fas fa-boxes"></i> Jumlah yang Dikemas Ulang 
                        <span id="unit-label" class="unit-badge">Unit</span>
                    </label>
                    <input type="number" name="jumlah_kemas_unit" id="jumlah_kemas_unit" class="form-control"
                           value="0" min="0" required>
                    <div class="input-helper" id="input-validation-info">
                        <i class="fas fa-info-circle"></i>
                        Minimal: 0
                    </div>
                    <div class="quick-actions" id="quick-actions" style="display: none;">
                        <button type="button" class="quick-btn" onclick="setQuickValue(10)">+10</button>
                        <button type="button" class="quick-btn" onclick="setQuickValue(25)">+25</button>
                        <button type="button" class="quick-btn" onclick="setQuickValue(50)">+50</button>
                        <button type="button" class="quick-btn" onclick="setMaxValue()">Max</button>
                    </div>
                </div>

                <div class="calculation-panel" id="calculation-panel">
                    <div class="calculation-header">
                        <i class="fas fa-calculator"></i>
                        <span>Perhitungan Kemas Ulang</span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Input:</span>
                        <span class="calculation-value" id="calc-input">0</span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Konversi ke Pcs:</span>
                        <span class="calculation-value" id="calc-pcs">0 pcs</span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Sisa Stok:</span>
                        <span class="calculation-value" id="calc-sisa">0</span>
                    </div>
                    
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill"></div>
                    </div>
                    
                    <div class="validation-status" id="validation-status">
                        <i class="fas fa-info-circle"></i> Belum ada input
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <i class="fas fa-save"></i>
                    <span>Simpan Hasil Kemas Ulang</span>
                </button>
            </form>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loading-overlay">
    <div class="loading-spinner"></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="<?= base_url('js/form_operpack_kemas_ulang.js') ?>"></script>
<?= $this->endSection() ?>
