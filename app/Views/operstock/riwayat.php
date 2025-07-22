<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="/css/riwayat_operstock.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="header anim-fade-in">
        <h1><i class="fas fa-exchange-alt"></i> Riwayat Operstock</h1>
        <p>Kelola dan pantau riwayat transfer stok antar gudang</p>
    </div>
    <div id="notification-toast" class="notification-toast"></div>
    
    <div class="filter-card anim-slide-up">
        <div class="filter-header"><h3><i class="fas fa-filter"></i> Filter Data</h3></div>
        <form id="filterForm">
            <div class="filter-grid">
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Dari Tanggal:</label>
                    <input type="date" class="form-filter" id="tanggal_mulai" value="<?= esc($tgl_mulai) ?>">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Sampai Tanggal:</label>
                    <input type="date" class="form-filter" id="tanggal_akhir" value="<?= esc($tgl_akhir) ?>">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-warehouse"></i> Gudang (Asal/Tujuan):</label>
                    <select id="gudang_id" class="form-filter">
                        <option value="semua">-- Semua --</option>
                        <?php foreach($gudang_list as $g): ?>
                            <option value="<?= $g['id_gudang'] ?>"><?= esc($g['nama_gudang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-box"></i> Produk:</label>
                    <select id="produk_id" class="form-filter">
                        <option value="semua">-- Semua --</option>
                        <?php foreach($produk_list as $p): ?>
                            <option value="<?= $p['id_produk'] ?>"><?= esc($p['nama_produk']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
    
    <div class="search-container">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari berdasarkan produk, gudang, atau nomor surat jalan...">
            <i class="fas fa-search"></i>
        </div>
    </div>
        
    <div class="table-container anim-slide-up" style="animation-delay: 0.1s;">
        <div class="table-header">
            <div class="table-title"><i class="fas fa-table"></i> Data Riwayat</div>
            <div class="table-stats">Total: <span id="totalRows">0</span> data</div>
        </div>
        <div class="loading" id="loadingState" style="display: none; padding: 40px; text-align: center;">
            <i class="fas fa-spinner fa-spin fa-2x"></i> Memuat data...
        </div>
        <div class="table-responsive">
            <table class="report-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar"></i> Tanggal Kirim</th>
                        <th><i class="fas fa-file-alt"></i> No. SJ</th>
                        <th class="text-left"><i class="fas fa-box"></i> Produk</th>
                        <th><i class="fas fa-warehouse"></i> Gudang Asal</th>
                        <th><i class="fas fa-warehouse"></i> Gudang Tujuan</th>
                        <th><i class="fas fa-cubes"></i> Jml Dus</th>
                        <th><i class="fas fa-cube"></i> Jml Satuan</th>
                        <th><i class="fas fa-cogs"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    <!-- Data akan dimuat via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk Edit -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Edit Operstock</h2>
            <button class="close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editForm">
                <input type="hidden" id="editDetailId" name="detail_id">
                <input type="hidden" id="editProdukId" name="produk_id">
                <input type="hidden" id="editGudangAsalId" name="gudang_asal_id">
                <input type="hidden" id="editGudangTujuanId" name="gudang_tujuan_id">
                <input type="hidden" id="editJumlahDusLama" name="jumlah_dus_lama">
                <input type="hidden" id="editJumlahSatuanLama" name="jumlah_satuan_lama">
                <input type="hidden" id="editSatuanPerDus" name="satuan_per_dus">
                
                <div class="form-group">
                    <label><i class="fas fa-box"></i> Produk:</label>
                    <p id="editNamaProduk"></p>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-exchange-alt"></i> Transfer:</label>
                    <p id="editInfoGudang"></p>
                </div>

                <div class="form-group info-box">
                    <label><i class="fas fa-history"></i> Stok Tersedia di Gudang Asal (Saat Itu)</label>
                    <p id="editStokInfoAsal"></p>
                </div>

                <div class="form-group info-box">
                    <label><i class="fas fa-info-circle"></i> Stok Saat Ini di Gudang Tujuan</label>
                    <p id="editStokInfoTujuan"></p>
                </div>
                
                <div class="form-group">
                    <label for="editJumlahDus"><i class="fas fa-cubes"></i> Jumlah Dus:</label>
                    <input type="number" id="editJumlahDus" name="jumlah_dus" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="editJumlahSatuan"><i class="fas fa-cube"></i> Jumlah Satuan:</label>
                    <input type="number" id="editJumlahSatuan" name="jumlah_satuan" min="0" required>
                </div>

                <div id="editErrorMsg" class="validation-error" style="display: none;"></div>
                
                <button type="submit" id="submitEdit" class="btn-submit">
                    <i class="fas fa-save"></i> Update Data
                </button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
    const BASE_URL = '<?= base_url() ?>';
</script>
<script src="/js/riwayat_operstock.js"></script>
<?= $this->endSection() ?>
