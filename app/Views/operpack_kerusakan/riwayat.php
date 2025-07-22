<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="<?= base_url('css/riwayat_operpack_kerusakan.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="header anim-fade-in">
        <h1><i class="fas fa-screwdriver-wrench"></i> Riwayat Barang Rusak</h1>
        <p>Kelola dan pantau riwayat penerimaan barang rusak dari internal dan eksternal</p>
    </div>
    
    <div id="notification-toast" class="notification-toast"></div>
    
    <div class="filter-card anim-slide-up">
        <div class="filter-header">
            <h3><i class="fas fa-filter"></i> Filter Data</h3>
        </div>
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
                    <label><i class="fas fa-box"></i> Produk:</label>
                    <select id="produk_id" class="form-filter">
                        <option value="semua">-- Semua Produk --</option>
                        <?php foreach($produk_list as $p): ?>
                            <option value="<?= $p['id_produk'] ?>"><?= esc($p['nama_produk']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-tags"></i> Kategori Asal:</label>
                    <select id="kategori_asal" class="form-filter">
                        <option value="semua">-- Semua Kategori --</option>
                        <option value="Internal">Internal</option>
                        <option value="Eksternal">Eksternal</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
    
    <div class="search-container">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari berdasarkan produk, asal, atau nomor surat jalan...">
            <i class="fas fa-search"></i>
        </div>
    </div>
    
    <div class="table-container anim-slide-up" style="animation-delay: 0.1s;">
        <div class="table-header">
            <div class="table-title">
                <i class="fas fa-table"></i>
                Data Riwayat Barang Rusak
            </div>
            <div class="table-stats">
                Total: <span id="totalRows">0</span> data
            </div>
        </div>
        
        <!-- <div class="loading" id="loadingState">
            <i class="fas fa-spinner fa-spin fa-2x"></i> Memuat data...
        </div> -->
        
        <div class="table-responsive">
            <table class="report-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar"></i> Tanggal Terima</th>
                        <th><i class="fas fa-file-alt"></i> No. SJ</th>
                        <th><i class="fas fa-tags"></i> Kategori</th>
                        <th class="text-left"><i class="fas fa-map-marker-alt"></i> Asal</th>
                        <th class="text-left"><i class="fas fa-box"></i> Produk</th>
                        <th><i class="fas fa-cubes"></i> Jml Dus</th>
                        <th><i class="fas fa-cube"></i> Jml Satuan</th>
                        <th><i class="fas fa-cogs"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    <div class="loading" id="loadingState">
                        <i class="fas fa-spinner fa-spin fa-2x"></i> Memuat data...
                    </div>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk Edit -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Edit Penerimaan Kerusakan</h2>
            <button class="close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editForm">
                <input type="hidden" id="editDetailId" name="detail_id">
                <input type="hidden" id="editProdukId" name="produk_id">
                <input type="hidden" id="editGudangAsalId" name="gudang_asal_id">
                <input type="hidden" id="editJumlahDusLama" name="jumlah_dus_lama">
                <input type="hidden" id="editJumlahSatuanLama" name="jumlah_satuan_lama">
                
                <div class="form-group">
                    <label><i class="fas fa-box"></i> Produk:</label>
                    <p id="editNamaProduk" class="form-display"></p>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Asal:</label>
                    <p id="editAsal" class="form-display"></p>
                </div>
                
                <!-- Info Stok untuk Internal -->
                <div id="stokInfoContainer" class="form-group info-container" style="display: none;">
                    <label style="color: #1976d2;"><i class="fas fa-info-circle"></i> Stok Tersedia di Gudang Asal</label>
                    <p id="editStokInfo" class="info-text"></p>
                </div>
                
                <!-- Info Penjualan untuk Eksternal -->
                <div id="penjualanInfoContainer" class="form-group info-container" style="display: none;">
                    <label style="color: #f57c00;"><i class="fas fa-receipt"></i> Data Penjualan</label>
                    <p id="editPenjualanInfo" class="info-text"></p>
                </div>
                
                <div class="form-group">
                    <label for="editJumlahDus"><i class="fas fa-cubes"></i> Jumlah Dus:</label>
                    <input type="number" id="editJumlahDus" name="jumlah_dus" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="editJumlahSatuan"><i class="fas fa-cube"></i> Jumlah Satuan:</label>
                    <input type="number" id="editJumlahSatuan" name="jumlah_satuan" min="0" required>
                </div>
                
                <div id="editErrorMsg" class="error-message" style="display: none;"></div>
                
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-save"></i> Update Data
                </button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="<?= base_url('js/riwayat_operpack_kerusakan.js') ?>"></script>
<?= $this->endSection() ?>
