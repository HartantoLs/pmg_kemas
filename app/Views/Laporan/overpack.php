<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="/css/laporan_overpack.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-recycle"></i> Laporan Stok Overpack</h1>
        <div class="subtitle">Monitoring dan Tracking Stok Produk Overpack</div>
    </div>

    <div class="filter-card">
        <div class="filter-header"><i class="fas fa-filter"></i> Filter Laporan</div>
        <form id="filterForm">
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="tipe_laporan">Tipe Laporan</label>
                    <select id="tipe_laporan" name="tipe_laporan" class="form-filter">
                        <option value="harian" <?= ($tipe_laporan == 'harian') ? 'selected' : ''; ?>>Laporan Status Harian</option>
                        <option value="rekap" <?= ($tipe_laporan == 'rekap') ? 'selected' : ''; ?>>Rekapitulasi Periode</option>
                    </select>
                </div>
                <div class="filter-group filter-harian">
                    <label for="tanggal">Pilih Tanggal</label>
                    <input type="date" class="form-filter" id="tanggal" name="tanggal" value="<?= esc($selected_date); ?>">
                </div>
                <div class="filter-group filter-rekap" style="display:none;">
                    <label for="tanggal_mulai">Dari Tanggal</label>
                    <input type="date" class="form-filter" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($start_date); ?>">
                </div>
                <div class="filter-group filter-rekap" style="display:none;">
                    <label for="tanggal_akhir">Sampai Tanggal</label>
                    <input type="date" class="form-filter" id="tanggal_akhir" name="tanggal_akhir" value="<?= esc($end_date); ?>">
                </div>
                <div class="filter-group">
                    <label for="produk_id">Produk</label>
                    <select id="produk_id" name="produk_id" class="form-filter">
                        <option value="semua">-- Semua Produk --</option>
                        <?php foreach ($produk_list as $produk): ?>
                            <option value="<?= $produk['id_produk']; ?>" <?= ($filter_produk == $produk['id_produk']) ? 'selected' : ''; ?>>
                                <?= esc($produk['nama_produk']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="date-info" id="date-info">
        <i class="fas fa-calendar-check"></i>
        <span id="date-info-text">Memuat informasi tanggal...</span>
    </div>

    <div class="action-buttons">
        <button class="btn btn-secondary" onclick="exportCSV()">
            <i class="fas fa-file-excel"></i> Export CSV
        </button>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>

    <div class="report-table-container">
        <div class="table-wrapper">
            <div id="table-content">
                <div style="text-align: center; padding: 3rem;">
                    <i class="fas fa-spinner fa-spin fa-2x"></i><br><br>
                    Memuat data...
                </div>
            </div>
        </div>
    </div>
</div>
<div class="loading-overlay" id="loading-overlay">
    <div class="loading-spinner"></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="/js/laporan_overpack.js"></script>
<?= $this->endSection() ?>
