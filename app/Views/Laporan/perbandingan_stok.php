<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="<?= base_url('css/laporan_perbandingan_stok.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container" data-base-url="<?= site_url('laporan') ?>">
    <div class="page-header anim-fade-in">
        <h1><i class="fas fa-balance-scale"></i> Laporan Perbandingan Stok</h1>
        <p>Analisis perbandingan stok fisik vs pembukuan untuk kontrol akurasi inventori</p>
        <div class="date-highlight" id="header-date">
            <i class="fas fa-calendar-day"></i> Tanggal: <?= date('d F Y', strtotime($selected_date)); ?>
        </div>
    </div>

    <div id="notification-toast" class="notification-toast"></div>

    <div class="card fade-in">
        <div class="card-header">
            <h2><i class="fas fa-filter"></i> Filter Laporan</h2>
            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="printLaporan()" class="btn btn-export">
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
                        <label for="tanggal" class="filter-label">
                            <i class="fas fa-calendar-alt"></i> Tanggal Pengecekan
                        </label>
                        <input type="date" class="filter-input" id="tanggal" name="tanggal" 
                               value="<?= esc($selected_date); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="id_gudang" class="filter-label">
                            <i class="fas fa-warehouse"></i> Gudang
                        </label>
                        <select id="id_gudang" name="id_gudang" class="filter-select">
                            <option value="semua">-- Semua Gudang --</option>
                            <?php foreach ($gudang_list as $gudang): ?>
                                <option value="<?= $gudang['id_gudang']; ?>" 
                                        <?= ($filter_gudang == $gudang['id_gudang']) ? 'selected' : ''; ?>>
                                    <?= esc($gudang['nama_gudang']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="produk_id" class="filter-label">
                            <i class="fas fa-box"></i> Produk
                        </label>
                        <select id="produk_id" name="produk_id" class="filter-select">
                            <option value="semua">-- Semua Produk --</option>
                            <?php foreach ($produk_list as $produk): ?>
                                <option value="<?= $produk['id_produk']; ?>" 
                                        <?= ($filter_produk == $produk['id_produk']) ? 'selected' : ''; ?>>
                                    <?= esc($produk['nama_produk']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="stats-grid fade-in">
        <div class="stat-card">
            <div class="stat-icon total"><i class="fas fa-list"></i></div>
            <span class="stat-number" id="stat-total-records">0</span>
            <div class="stat-label">Total Records</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon difference"><i class="fas fa-exclamation-triangle"></i></div>
            <span class="stat-number" id="stat-records-with-difference">0</span>
            <div class="stat-label">Ada Selisih</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon dus"><i class="fas fa-cubes"></i></div>
            <span class="stat-number" id="stat-total-selisih-dus">0</span>
            <div class="stat-label">Total Selisih Dus</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon satuan"><i class="fas fa-cube"></i></div>
            <span class="stat-number" id="stat-total-selisih-satuan">0</span>
            <div class="stat-label">Total Selisih Satuan</div>
        </div>
    </div>

    <div class="alert alert-info fade-in">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Filter Aktif:</strong>
            Gudang: <span id="info-gudang">-</span> |
            Produk: <span id="info-produk">-</span> |
            Tanggal: <span id="info-tanggal">-</span>
        </div>
    </div>

    <div class="table-container fade-in">
        <div class="card-header">
            <h2><i class="fas fa-table"></i> Perbandingan Stok Fisik vs Pembukuan</h2>
            <div id="table-record-count" style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem;">
                0 Records
            </div>
        </div>
        <div class="table-wrapper">
            <table class="report-table" id="reportTable">
                <thead>
                    <tr>
                        <th rowspan="2">NO</th>
                        <th rowspan="2" class="text-left">PRODUK</th>
                        <th rowspan="2" class="text-left">GUDANG</th>
                        <th colspan="2" class="header-group-sistem">STOK SISTEM</th>
                        <th colspan="2" class="header-group-fisik">STOK FISIK</th>
                        <th colspan="2" class="header-group-selisih">SELISIH</th>
                    </tr>
                    <tr>
                        <th class="header-group-sistem">Dus</th>
                        <th class="header-group-sistem">Pcs</th>
                        <th class="header-group-fisik">Dus</th>
                        <th class="header-group-fisik">Pcs</th>
                        <th class="header-group-selisih">Dus</th>
                        <th class="header-group-selisih">Pcs</th>
                    </tr>
                </thead>
                <tbody id="report-table-body">
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin fa-2x"></i> Memuat data...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loading-overlay">
    <div class="loading-spinner"></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="<?= base_url('js/laporan_perbandingan_stok.js') ?>"></script>
<?= $this->endSection() ?>
