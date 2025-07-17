<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="/css/riwayat_pengemasan.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-boxes"></i> Riwayat Pengemasan</h1>
        <p>Kelola dan pantau riwayat hasil produksi dan pengemasan</p>
    </div>
    
    <div class="filter-card">
        <div class="filter-header"><h3><i class="fas fa-filter"></i> Filter Data</h3></div>
        <form id="filterForm">
            <div class="filter-grid">
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Dari Tanggal:</label>
                    <input type="date" class="form-filter" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($tgl_mulai) ?>">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Sampai Tanggal:</label>
                    <input type="date" class="form-filter" id="tanggal_akhir" name="tanggal_akhir" value="<?= esc($tgl_akhir) ?>">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-warehouse"></i> Gudang:</label>
                    <select name="gudang_id" id="gudang_id" class="form-filter">
                        <option value="semua">-- Semua Gudang --</option>
                        <?php foreach($gudang_list as $g): ?>
                            <option value="<?= $g['id_gudang'] ?>"><?= esc($g['nama_gudang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-box"></i> Produk:</label>
                    <select name="produk_id" id="produk_id" class="form-filter">
                        <option value="semua">-- Semua Produk --</option>
                        <?php foreach($produk_list as $p): ?>
                            <option value="<?= $p['id_produk'] ?>"><?= esc($p['nama_produk']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
    
    <div class="table-container">
        <div class="table-header">
            <div class="table-title"><i class="fas fa-table"></i> Data Riwayat</div>
            <div class="table-stats">Total: <span id="totalRows">0</span> data</div>
        </div>
        <div class="loading" id="loadingState"><i class="fas fa-spinner fa-spin fa-2x"></i> Memuat data...</div>
        <table class="report-table" id="dataTable">
            <thead>
                <tr>
                    <th><i class="fas fa-calendar"></i> Tanggal</th>
                    <th><i class="fas fa-clock"></i> Shift</th>
                    <th class="text-left"><i class="fas fa-cogs"></i> Mesin</th>
                    <th class="text-left"><i class="fas fa-box"></i> Produk</th>
                    <th><i class="fas fa-warehouse"></i> Gudang</th>
                    <th><i class="fas fa-cubes"></i> Jml Dus</th>
                    <th><i class="fas fa-cube"></i> Jml Satuan</th>
                    <th><i class="fas fa-bolt"></i> Aksi</th>
                </tr>
            </thead>
            <tbody id="dataTableBody"></tbody>
        </table>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Edit Pengemasan</h2>
            <button class="close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editForm">
                <input type="hidden" name="action" value="update_pengemasan">
                <input type="hidden" id="editId" name="id">
                
                <div class="form-group"><label>Produk:</label><p id="editNamaProduk"></p></div>
                <div class="form-group"><label>Gudang:</label><p id="editNamaGudang"></p></div>
                
                <div class="form-group info-box">
                    <label><i class="fas fa-history"></i> Stok Pada Saat Transaksi</label>
                    <p id="editStokInfo"></p>
                    <small class="text-muted">*Perubahan nilai akan divalidasi agar stok saat ini tidak negatif.</small>
                </div>

                <div class="form-group">
                    <label for="editJumlahDus">Jumlah Dus:</label>
                    <input type="number" id="editJumlahDus" name="jumlah_dus" min="0" required>
                </div>
                <div class="form-group">
                    <label for="editJumlahSatuan">Jumlah Satuan:</label>
                    <input type="number" id="editJumlahSatuan" name="jumlah_satuan" min="0" required>
                </div>
                
                <button type="submit" id="submitEdit" class="btn-submit"><i class="fas fa-save"></i> Update Data</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="/js/riwayat_pengemasan.js"></script>
<?= $this->endSection() ?>