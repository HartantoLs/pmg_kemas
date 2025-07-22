<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="<?= base_url('css/riwayat_penjualan.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="header anim-fade-in">
        <h1><i class="fas fa-cart-arrow-down"></i> Riwayat Penjualan</h1>
        <p>Kelola dan pantau riwayat transaksi penjualan produk</p>
    </div>

    <div id="notification-toast" class="notification-toast"></div>

    <div class="filter-card anim-slide-up">
        <div class="filter-header"><h3><i class="fas fa-filter"></i> Filter Data</h3></div>
        <form id="filterForm">
            <div class="filter-grid">
                <div class="filter-group"><label><i class="fas fa-calendar-alt"></i> Dari Tanggal:</label><input type="date" class="form-filter" id="tanggal_mulai" value="<?= esc($tgl_mulai) ?>"></div>
                <div class="filter-group"><label><i class="fas fa-calendar-alt"></i> Sampai Tanggal:</label><input type="date" class="form-filter" id="tanggal_akhir" value="<?= esc($tgl_akhir) ?>"></div>
                <div class="filter-group"><label><i class="fas fa-warehouse"></i> Gudang:</label><select id="gudang_id" class="form-filter"><option value="semua">-- Semua Gudang --</option><?php foreach($gudang_list as $g): ?><option value="<?= $g['id_gudang'] ?>"><?= esc($g['nama_gudang']) ?></option><?php endforeach; ?></select></div>
                <div class="filter-group"><label><i class="fas fa-box"></i> Produk:</label><select id="produk_id" class="form-filter"><option value="semua">-- Semua Produk --</option><?php foreach($produk_list as $p): ?><option value="<?= $p['id_produk'] ?>"><?= esc($p['nama_produk']) ?></option><?php endforeach; ?></select></div>
            </div>
        </form>
    </div>
    
    <div class="table-container anim-slide-up" style="animation-delay: 0.1s;">
        <div class="table-header"><div class="table-title"><i class="fas fa-table"></i> Data Riwayat Penjualan</div><div class="table-stats">Total: <span id="totalRows">0</span> data</div></div>
        <div class="table-responsive">
            <table class="report-table">
                <thead><tr><th>Tanggal</th><th>No. SJ</th><th class="text-left">Customer</th><th class="text-left">Produk</th><th>Gudang</th><th>Jml Dus</th><th>Jml Satuan</th><th>Aksi</th></tr></thead>
                <tbody id="dataTableBody"><div class="loading" id="loadingState"><i class="fas fa-spinner fa-spin fa-2x"></i> Memuat data...</div></tbody>
            </table>
        </div>
    </div>
</div>

<div id="editModal" class="modal"><div class="modal-content"><div class="modal-header"><h2><i class="fas fa-edit"></i> Edit Penjualan</h2><button class="close">&times;</button></div><div class="modal-body"><form id="editForm"><input type="hidden" name="detail_id" id="editDetailId"><div class="form-group"><label>Produk:</label><p id="editNamaProduk"></p></div><div class="form-group"><label>Gudang:</label><p id="editNamaGudang"></p></div><div class="form-group info-box"><label><i class="fas fa-history"></i> Stok Tersedia Saat Transaksi</label><p id="editStokInfo"></p><small class="text-muted">Jumlah penjualan baru tidak boleh melebihi stok ini.</small></div><div class="form-group"><label for="editJumlahDus">Jumlah Dus:</label><input type="number" id="editJumlahDus" name="jumlah_dus" min="0" required></div><div class="form-group"><label for="editJumlahSatuan">Jumlah Satuan:</label><input type="number" id="editJumlahSatuan" name="jumlah_satuan" min="0" required></div><div id="editErrorMsg" class="validation-error"></div><button type="submit" id="submitEdit" class="btn-submit"><i class="fas fa-save"></i> Update Data</button></form></div></div></div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="<?= base_url('js/riwayat_penjualan.js') ?>"></script>
<?= $this->endSection() ?>