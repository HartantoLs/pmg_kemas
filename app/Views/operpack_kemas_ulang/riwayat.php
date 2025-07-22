<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="/css/riwayat_operpack_kemas_ulang.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-box-open"></i> Riwayat Kemas Ulang</h1>
        <p>Kelola dan pantau produk yang berhasil dikemas ulang dari Overpack</p>
    </div>
    
    <div class="filter-card">
        <div class="filter-header">
            <i class="fas fa-filter"></i>
            <h3>Filter Data</h3>
        </div>
        <form id="filterForm">
            <div class="filter-grid">
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Dari Tanggal:</label>
                    <input type="date" class="form-filter" id="tanggal_mulai" name="tanggal_mulai" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Sampai Tanggal:</label>
                    <input type="date" class="form-filter" id="tanggal_akhir" name="tanggal_akhir" value="<?= date('Y-m-t') ?>">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-box"></i> Produk:</label>
                    <select name="produk_id" id="produk_id" class="form-filter">
                        <option value="semua">-- Semua Produk --</option>
                        <?php foreach($produk_list as $p): ?>
                            <option value="<?= $p['id_produk'] ?>">
                                <?= esc($p['nama_produk']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
    
    <div class="search-container">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari berdasarkan nama produk...">
            <i class="fas fa-search"></i>
        </div>
    </div>
    
    <div class="table-container">
        <div class="table-header">
            <div class="table-title">
                <i class="fas fa-table"></i>
                Data Hasil Kemas Ulang
            </div>
            <div class="table-stats">
                Total: <span id="totalRows">0</span> data
            </div>
        </div>
        
        <div class="loading" id="loadingState">
            <i class="fas fa-spinner fa-spin fa-2x"></i> Memuat data...
        </div>
        
        <table class="report-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th class="text-left">Produk</th>
                    <th>Jumlah Kemas (Pcs)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Edit Kemas Ulang</h2>
            <button class="close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editForm">
                <input type="hidden" name="action" value="update_kemas_ulang">
                <input type="hidden" id="editDetailId" name="id">
                
                <div class="form-group" style="background-color: #fff5f0; padding: 15px; border-radius: 10px; border: 1px solid #ffe8d6;">
                    <label style="color: #ff6b35;"><i class="fas fa-info-circle"></i> Stok Siap Kemas</label>
                    <p style="background: none; padding: 0; color: #ff6b35; font-weight: bold; margin-top: 5px;">
                        <span id="maxKemas">0</span> pcs
                    </p>
                    <small class="text-muted">* Jumlah kemas tidak boleh melebihi stok ini.</small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-box"></i> Produk:</label>
                    <p id="editNamaProduk"></p>
                </div>
                
                <div class="form-group">
                    <label for="editJumlahKemas"><i class="fas fa-boxes"></i> Jumlah Kemas (Pcs):</label>
                    <input type="number" id="editJumlahKemas" name="jumlah_kemas" min="0" required>
                    <small class="text-muted">Harus kelipatan <strong id="satuanPerDus">1</strong>.</small>
                </div>
                <div id="editErrorMsg" style="color: #dc3545; font-size: 0.875em; display: none; margin-bottom: 15px; text-align: center; font-weight: bold;">
                    Input tidak valid!
                </div>
                
                <button type="submit" id="submitEdit" class="btn-submit">
                    <i class="fas fa-save"></i> Update Data
                </button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="<?= base_url('js/riwayat_operpack_kemas_ulang.js') ?>"></script>
<?= $this->endSection() ?>
