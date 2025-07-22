<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="<?= base_url('css/laporan_kartu_stok.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-book"></i> Laporan Kartu Stok Produk</h1>
        <p>Sistem pelacakan mutasi stok produk yang komprehensif dan real-time</p>
    </div>

    <div class="card fade-in">
        <div class="card-header">
            <h2><i class="fas fa-filter"></i> Filter Laporan</h2>
            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="window.print()" class="btn btn-export">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" onclick="exportToCSV()" class="btn btn-export">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="filterForm">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="produk_id" class="filter-label">
                            <i class="fas fa-box"></i> Produk <span style="color: var(--error-red);">*</span>
                        </label>
                        <select id="produk_id" name="produk_id" class="filter-select" required>
                            <option value="">-- WAJIB PILIH PRODUK --</option>
                            <?php foreach ($produk_list as $produk): ?>
                                <option value="<?= $produk['id_produk']; ?>" <?= ($filter_produk == $produk['id_produk']) ? 'selected' : ''; ?>>
                                    <?= esc($produk['nama_produk']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="gudang_id" class="filter-label">
                            <i class="fas fa-warehouse"></i> Gudang
                        </label>
                        <select id="gudang_id" name="gudang_id" class="filter-select">
                            <option value="semua">-- Semua Gudang --</option>
                            <?php foreach ($gudang_list as $gudang): ?>
                                <option value="<?= $gudang['id_gudang']; ?>" <?= ($filter_gudang == $gudang['id_gudang']) ? 'selected' : ''; ?>>
                                    <?= esc($gudang['nama_gudang']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="tanggal_mulai" class="filter-label">
                            <i class="fas fa-calendar-alt"></i> Dari Tanggal
                        </label>
                        <input type="date" class="filter-input" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($tgl_mulai); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="tanggal_akhir" class="filter-label">
                            <i class="fas fa-calendar-check"></i> Sampai Tanggal
                        </label>
                        <input type="date" class="filter-input" id="tanggal_akhir" name="tanggal_akhir" value="<?= esc($tgl_akhir); ?>">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="info-cards fade-in" id="info-cards" style="display: none;">
        <div class="info-card">
            <div class="info-card-icon"><i class="fas fa-box"></i></div>
            <div class="info-card-title">Produk</div>
            <div class="info-card-value" id="info-produk">-</div>
        </div>
        <div class="info-card">
            <div class="info-card-icon"><i class="fas fa-warehouse"></i></div>
            <div class="info-card-title">Gudang</div>
            <div class="info-card-value" id="info-gudang">-</div>
        </div>
        <div class="info-card">
            <div class="info-card-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="info-card-title">Periode</div>
            <div class="info-card-value" id="info-periode">-</div>
        </div>
        <div class="info-card">
            <div class="info-card-icon"><i class="fas fa-list"></i></div>
            <div class="info-card-title">Total Transaksi</div>
            <div class="info-card-value" id="info-total-transaksi">-</div>
        </div>
    </div>

    <div class="alert alert-warning fade-in" id="alert-pilih-produk">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Silakan pilih produk untuk menampilkan laporan kartu stok.</span>
    </div>

    <div class="table-container fade-in">
        <div class="card-header">
            <h2><i class="fas fa-table"></i> Kartu Stok - <span id="table-title">Pilih Produk</span></h2>
        </div>
        <div class="table-wrapper">
            <table class="report-table" id="reportTable">
                <thead>
                    <tr>
                        <th rowspan="2">Tanggal</th>
                        <th rowspan="2">ID Gudang</th>
                        <th rowspan="2">Keterangan</th>
                        <th colspan="2">Masuk</th>
                        <th colspan="2">Keluar</th>
                        <th colspan="2">Saldo</th>
                    </tr>
                    <tr>
                        <th>Dus</th><th>Pcs</th>
                        <th>Dus</th><th>Pcs</th>
                        <th>Dus</th><th>Pcs</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr><td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-gray);">Silakan pilih produk untuk menampilkan laporan.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loading-overlay" style="display: none;">
    <div class="loading-spinner"></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="<?= base_url('js/laporan_kartu_stok.js') ?>"></script>
<?= $this->endSection() ?>
