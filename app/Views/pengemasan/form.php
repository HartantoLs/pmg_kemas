<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="<?= base_url('css/form_pengemasan.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="main-container">
    <div class="form-card">
        <div class="form-header">
            <h2><i class="fas fa-box"></i> Input Hasil Pengemasan</h2>
        </div>
        <div class="form-content">
            <div id="form-messages" class="alert"></div>
            <form id="pengemasan-form">
                <div class="header-section">
                    <div class="grid-container">
                        <div class="form-group">
                            <label for="tTanggal"><i class="fas fa-calendar"></i> Tanggal</label>
                            <input type="datetime-local" name="tTanggal" id="tTanggal" value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="tShift"><i class="fas fa-clock"></i> Shift</label>
                            <select name="tShift" id="tShift" required>
                                <option value="1">Shift 1</option>
                                <option value="2">Shift 2</option>
                                <option value="3">Shift 3</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="section-title">
                    <h3><i class="fas fa-cogs"></i> Data Produksi</h3>
                    <button type="button" id="btn-tambah-item" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Produksi</button>
                </div>
                <div id="items-container"></div>
                <div class="submit-section">
                    <button type="submit" class="submit-btn"><i class="fas fa-save"></i> Simpan Semua Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="<?= base_url('js/form_pengemasan.js') ?>"></script>
<?= $this->endSection() ?>