<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="/css/laporan_stok_saat_ini.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-boxes-stacked"></i> Laporan Stok Produk</h1>
        <p>Monitoring real-time stok produk di seluruh gudang dengan akurasi tinggi</p>
    </div>

    <div class="card fade-in">
        <div class="card-header">
            <h2><i class="fas fa-filter"></i> Filter & Pencarian</h2>
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
                        <label for="tanggal_laporan" class="filter-label">
                            <i class="fas fa-calendar"></i> Tanggal Laporan
                        </label>
                        <input type="date" id="tanggal_laporan" name="tanggal_laporan" class="filter-input" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="filter-group">
                        <label for="id_gudang" class="filter-label">
                            <i class="fas fa-warehouse"></i> Gudang
                        </label>
                        <select id="id_gudang" name="id_gudang" class="filter-select">
                            <option value="semua">-- Total Semua Gudang --</option>
                            <?php foreach ($gudang_list as $gudang): ?>
                                <option value="<?= $gudang['id_gudang']; ?>">
                                    <?= esc($gudang['nama_gudang']) . " (" . esc($gudang['tipe_gudang']) . ")"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="search" class="filter-label">
                            <i class="fas fa-search"></i> Cari Produk
                        </label>
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="search" name="search" class="filter-input search-input" placeholder="Ketik nama produk...">
                        </div>
                    </div>
                    <div class="filter-group">
                        <button type="button" onclick="clearSearch()" class="btn btn-primary" style="margin-top: 1.75rem;">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="stats-grid fade-in">
        <div class="stat-card">
            <div class="stat-icon products"><i class="fas fa-box"></i></div>
            <span class="stat-number" id="stat-total-products">0</span>
            <div class="stat-label">Total Produk</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon dus"><i class="fas fa-cubes"></i></div>
            <span class="stat-number" id="stat-total-dus">0</span>
            <div class="stat-label">Total Dus</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon satuan"><i class="fas fa-cube"></i></div>
            <span class="stat-number" id="stat-total-satuan">0</span>
            <div class="stat-label">Total Satuan</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon active"><i class="fas fa-check-circle"></i></div>
            <span class="stat-number" id="stat-products-with-stock">0</span>
            <div class="stat-label">Produk Berisi</div>
        </div>
    </div>

    <div class="table-container fade-in">
        <div class="card-header">
            <h2 id="table-title"><i class="fas fa-table"></i> Stok Produk - <span id="selected-date"><?= date('d F Y') ?></span> - Total Semua Gudang</h2>
            <div id="table-row-count" style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem;">
                0 Produk
            </div>
        </div>
        <div class="table-wrapper">
            <table class="report-table" id="reportTable">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th class="text-left">Nama Produk</th>
                        <th>Jumlah Dus</th>
                        <th>Jumlah Satuan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem;">
                            <i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Memuat data...
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
<script src="/js/laporan_stok_saat_ini.js"></script>
<?= $this->endSection() ?>
