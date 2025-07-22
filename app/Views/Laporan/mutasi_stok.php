<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="/css/laporan_mutasi_stok.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-chart-line"></i> Laporan Mutasi Stok Per Produk</h1>
        <div class="subtitle" id="header-subtitle">Pilih filter untuk menampilkan laporan</div>
    </div>

    <div class="filter-card">
        <div class="filter-header"><i class="fas fa-filter"></i> Filter Laporan</div>
        <form id="filterForm">
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="tipe_laporan">Tipe Laporan</label>
                    <select id="tipe_laporan" name="tipe_laporan" class="form-control">
                        <option value="harian" <?= ($tipe_laporan == 'harian') ? 'selected' : ''; ?>>Harian</option>
                        <option value="rekap" <?= ($tipe_laporan == 'rekap') ? 'selected' : ''; ?>>Rekapitulasi</option>
                    </select>
                </div>
                <div class="filter-group" id="filter-harian">
                    <label for="tanggal">Tanggal Laporan</label>
                    <input type="date" class="form-control data-filter" id="tanggal" name="tanggal" value="<?= esc($tgl_laporan); ?>">
                </div>
                <div class="filter-group" id="filter-rekap-mulai" style="display:none;">
                    <label for="tanggal_mulai">Dari Tanggal</label>
                    <input type="date" class="form-control data-filter" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($tgl_mulai); ?>">
                </div>
                <div class="filter-group" id="filter-rekap-akhir" style="display:none;">
                    <label for="tanggal_akhir">Sampai Tanggal</label>
                    <input type="date" class="form-control data-filter" id="tanggal_akhir" name="tanggal_akhir" value="<?= esc($tgl_akhir); ?>">
                </div>
                <div class="filter-group">
                    <label for="gudang_id">Gudang</label>
                    <select id="gudang_id" name="gudang_id" class="form-control data-filter">
                        <option value="semua">-- Semua Gudang --</option>
                        <?php foreach ($gudang_list as $gudang): ?>
                            <option value="<?= $gudang['id_gudang']; ?>" <?= ($filter_gudang == $gudang['id_gudang']) ? 'selected' : ''; ?>>
                                <?= esc($gudang['nama_gudang']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="produk_id">Produk</label>
                    <select id="produk_id" name="produk_id" class="form-control data-filter">
                        <option value="semua">-- Semua Produk --</option>
                        <?php foreach ($produk_list as $produk): ?>
                            <option value="<?= $produk['id_produk']; ?>" <?= ($filter_produk == $produk['id_produk']) ? 'selected' : ''; ?>>
                                <?= esc($produk['nama_produk']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-primary w-100" id="refresh-button">
                        <i class="fas fa-sync-alt"></i> Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="summary-cards">
        <div class="summary-card saldo-awal">
            <div class="icon"><i class="fas fa-box"></i></div>
            <div class="label">Saldo Awal</div>
            <div class="value" id="summary-saldo-awal">-</div>
        </div>
        <div class="summary-card penerimaan">
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
            <div class="label">Total Penerimaan</div>
            <div class="value" id="summary-penerimaan">-</div>
        </div>
        <div class="summary-card pengeluaran">
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
            <div class="label">Total Pengeluaran</div>
            <div class="value" id="summary-pengeluaran">-</div>
        </div>
        <div class="summary-card saldo-akhir">
            <div class="icon"><i class="fas fa-chart-bar"></i></div>
            <div class="label">Saldo Akhir</div>
            <div class="value" id="summary-saldo-akhir">-</div>
        </div>
    </div>

    <div class="action-buttons">
        <button class="btn btn-secondary" onclick="exportExcel()">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>

    <div class="report-table-container" id="report-container">
        <div class="table-wrapper">
            <table class="report-table">
                <thead id="report-table-head">
                    <!-- Header akan diupdate via JavaScript -->
                </thead>
                <tbody id="report-table-body">
                    <tr>
                        <td colspan="10" style="text-align:center;padding:40px;color:#666;">
                            <i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="info-box" id="info-box-container" style="display: none;">
        <div class="icon"><i class="fas fa-info-circle"></i></div>
        <div class="content" id="info-box-content"></div>
    </div>
</div>

<div class="loading-overlay" id="loading-overlay">
    <i class="fas fa-spinner"></i>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="/js/laporan_mutasi_stok.js"></script>
<?= $this->endSection() ?>
