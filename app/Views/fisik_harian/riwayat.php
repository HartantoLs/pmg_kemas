<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="/css/riwayat_fisik_harian.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="main-container">
    <div class="page-header">
        <h1><i class="fas fa-history"></i> Riwayat Stok Fisik Harian</h1>
        <p>Laporan perbandingan stok fisik dengan pembukuan sistem</p>
    </div>

    <div class="form-card fade-in">
        <div class="form-header">
            <h2><i class="fas fa-filter"></i> Filter Data</h2>
        </div>
        <div class="form-content">
            <form id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_dari" class="form-label">Tanggal Dari</label>
                            <input type="date" id="tanggal_dari" name="tanggal_dari" class="form-input">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_sampai" class="form-label">Tanggal Sampai</label>
                            <input type="date" id="tanggal_sampai" name="tanggal_sampai" class="form-input">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="produk_id" class="form-label">Produk</label>
                            <select id="produk_id" name="produk_id" class="form-input">
                                <option value="">Semua Produk</option>
                                <?php foreach ($produk_list as $produk): ?>
                                    <option value="<?= $produk['id_produk'] ?>"><?= esc($produk['nama_produk']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="gudang_id" class="form-label">Gudang</label>
                            <select id="gudang_id" name="gudang_id" class="form-input">
                                <option value="">Semua Gudang</option>
                                <?php foreach ($gudang_list as $gudang): ?>
                                    <option value="<?= $gudang['id_gudang'] ?>"><?= esc($gudang['nama_gudang']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" id="btnFilter" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter Data
                        </button>
                        <button type="button" id="btnReset" class="btn btn-secondary">
                            <i class="fas fa-refresh"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="form-card fade-in">
        <div class="form-header">
            <h2><i class="fas fa-table"></i> Data Riwayat</h2>
        </div>
        <div class="form-content">
            <div id="tableContainer">
                <div class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Gunakan filter untuk menampilkan data</p>
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
<script src="/js/riwayat_fisik_harian.js"></script>
<?= $this->endSection() ?>
