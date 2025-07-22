<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="<?= base_url('css/form_operpack_seleksi.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-clipboard-check"></i> Form Input Hasil Seleksi</h1>
        <p>Input hasil seleksi produk overpack dengan mudah dan akurat</p>
    </div>

    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-edit"></i>
            <h2>Data Seleksi Produk</h2>
        </div>
        
        <div class="form-body">
            <div id="formMessage" class="alert"></div>
            
            <form id="form-seleksi">
                <input type="hidden" name="action" value="simpan_seleksi">
                
                <div class="form-group">
                    <label for="seleksi_produk">
                        <i class="fas fa-box"></i> Pilih Produk
                    </label>
                    <select name="id_produk" id="seleksi_produk" class="form-control" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach ($produk_list as $produk): ?>
                            <option value="<?= $produk['id_produk'] ?>">
                                <?= esc($produk['nama_produk']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="stok-display" class="stok-display"></div>
                </div>

                <div class="form-group">
                    <label for="seleksi_tanggal">
                        <i class="fas fa-calendar"></i> Tanggal Seleksi
                    </label>
                    <input type="date" name="tanggal" id="seleksi_tanggal" class="form-control"
                           value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="pcs_aman">
                            <i class="fas fa-check-circle"></i> Jumlah Pcs Aman
                        </label>
                        <input type="number" name="pcs_aman" id="pcs_aman" class="form-control"
                               value="0" min="0" required>
                        <div class="input-helper">
                            <i class="fas fa-info-circle"></i>
                            Produk dalam kondisi baik
                        </div>
                        <div class="quick-actions">
                            <button type="button" class="quick-btn" onclick="setQuickValue('pcs_aman', 10)">+10</button>
                            <button type="button" class="quick-btn" onclick="setQuickValue('pcs_aman', 50)">+50</button>
                            <button type="button" class="quick-btn" onclick="setQuickValue('pcs_aman', 100)">+100</button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pcs_curah">
                            <i class="fas fa-exclamation-triangle"></i> Jumlah Pcs Curah
                        </label>
                        <input type="number" name="pcs_curah" id="pcs_curah" class="form-control"
                               value="0" min="0" required>
                        <div class="input-helper">
                            <i class="fas fa-info-circle"></i>
                            Produk perlu dikemas ulang
                        </div>
                        <div class="quick-actions">
                            <button type="button" class="quick-btn" onclick="setQuickValue('pcs_curah', 10)">+10</button>
                            <button type="button" class="quick-btn" onclick="setQuickValue('pcs_curah', 50)">+50</button>
                            <button type="button" class="quick-btn" onclick="setQuickValue('pcs_curah', 100)">+100</button>
                        </div>
                    </div>
                </div>

                <div class="calculation-panel" id="calculation-panel">
                    <div class="calculation-row">
                        <span class="calculation-label">Pcs Aman:</span>
                        <span class="calculation-value" id="calc-aman">0 pcs</span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Pcs Curah:</span>
                        <span class="calculation-value" id="calc-curah">0 pcs</span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Total Input:</span>
                        <span class="calculation-value" id="calc-total">0 pcs</span>
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
                    <span>Simpan Hasil Seleksi</span>
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
<script src="<?= base_url('js/form_operpack_seleksi.js') ?>"></script>
<?= $this->endSection() ?>
