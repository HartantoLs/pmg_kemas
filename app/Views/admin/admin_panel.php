<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="/css/admin_panel.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-cogs"></i> Admin Panel</h1>
            <p>Kelola data master sistem dengan mudah dan efisien</p>
        </div>

        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <!-- Filter Card -->
        <div class="filter-card">
            <div class="filter-header">
                <i class="fas fa-list-ul"></i>
                <h3>Pilih Jenis Data</h3>
            </div>
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="dataType">
                        <i class="fas fa-database"></i> Jenis Data
                    </label>
                    <select class="form-control" id="dataType" onchange="showForm()">
                        <option value="">-- Pilih Jenis Data --</option>
                        <option value="gudang">Gudang</option>
                        <option value="produk">Produk</option>
                        <option value="jenis_produksi">Jenis Produksi</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Gudang Section -->
        <div id="gudangSection" class="data-section" style="display: none;">
            <div class="table-container">
                <div class="table-header">
                    <div class="table-title">
                        <i class="fas fa-warehouse"></i>
                        <span>Data Gudang</span>
                    </div>
                    <button class="btn btn-primary" onclick="openModal('gudangModal')">
                        <i class="fas fa-plus"></i> Tambah Gudang
                    </button>
                </div>
                <div class="table-wrapper">
                    <table class="report-table" id="gudangTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Gudang</th>
                                <th>Tipe Gudang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Produk Section -->
        <div id="produkSection" class="data-section" style="display: none;">
            <div class="table-container">
                <div class="table-header">
                    <div class="table-title">
                        <i class="fas fa-box"></i>
                        <span>Data Produk</span>
                    </div>
                    <button class="btn btn-primary" onclick="openModal('produkModal')">
                        <i class="fas fa-plus"></i> Tambah Produk
                    </button>
                </div>
                <div class="table-wrapper">
                    <table class="report-table" id="produkTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Produk</th>
                                <th>Satuan per Dus</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Jenis Produksi Section -->
        <div id="jenis_produksiSection" class="data-section" style="display: none;">
            <div class="table-container">
                <div class="table-header">
                    <div class="table-title">
                        <i class="fas fa-industry"></i>
                        <span>Data Jenis Produksi</span>
                    </div>
                    <button class="btn btn-primary" onclick="openModal('jenisProduksiModal')">
                        <i class="fas fa-plus"></i> Tambah Jenis Produksi
                    </button>
                </div>
                <div class="table-wrapper">
                    <table class="report-table" id="jenisProduksiTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Jenis Produksi</th>
                                <th>Group</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Gudang Modal -->
    <div id="gudangModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-warehouse"></i> Form Gudang</h2>
                <button class="close" onclick="closeModal('gudangModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formGudang">
                    <input type="hidden" id="gudang_id" name="id_gudang">
                    <div class="form-group">
                        <label for="nama_gudang">
                            <i class="fas fa-tag"></i> Nama Gudang
                        </label>
                        <input type="text" id="nama_gudang" name="nama_gudang" required>
                    </div>
                    <div class="form-group">
                        <label for="tipe_gudang">
                            <i class="fas fa-layer-group"></i> Tipe Gudang
                        </label>
                        <select id="tipe_gudang" name="tipe_gudang" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="Bahan Baku">Bahan Baku</option>
                            <option value="Produk Jadi">Produk Jadi</option>
                            <option value="Overpack">Overpack</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Simpan Gudang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Produk Modal -->
    <div id="produkModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-box"></i> Form Produk</h2>
                <button class="close" onclick="closeModal('produkModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formProduk">
                    <input type="hidden" id="produk_id" name="id_produk">
                    <div class="form-group">
                        <label for="nama_produk">
                            <i class="fas fa-tag"></i> Nama Produk
                        </label>
                        <input type="text" id="nama_produk" name="nama_produk" required>
                    </div>
                    <div class="form-group">
                        <label for="satuan_per_dus">
                            <i class="fas fa-calculator"></i> Satuan per Dus
                        </label>
                        <input type="number" id="satuan_per_dus" name="satuan_per_dus" required>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Simpan Produk
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Jenis Produksi Modal -->
    <div id="jenisProduksiModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-industry"></i> Form Jenis Produksi</h2>
                <button class="close" onclick="closeModal('jenisProduksiModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formJenisProduksi">
                    <input type="hidden" id="jenis_produksi_id" name="nom_jenis_produksi">
                    <input type="hidden" id="is_edit" name="is_edit" value="0">
                    <div class="form-group">
                        <label for="jenis_produksi">
                            <i class="fas fa-tag"></i> Nama Jenis Produksi
                        </label>
                        <input type="text" id="jenis_produksi" name="jenis_produksi" required>
                    </div>
                    <div class="form-group">
                        <label for="group_jenis_produksi">
                            <i class="fas fa-layer-group"></i> Group Jenis Produksi
                        </label>
                        <input type="text" id="group_jenis_produksi" name="group_jenis_produksi" required>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">
                            <i class="fas fa-comment"></i> Keterangan
                        </label>
                        <input type="text" id="keterangan" name="keterangan">
                    </div>
                    <div class="form-group" id="satuanPerDusGroup">
                        <label for="satuan_per_dus_produksi">
                            <i class="fas fa-calculator"></i> Satuan per Dus
                        </label>
                        <input type="number" id="satuan_per_dus_produksi" name="satuan_per_dus" required>
                    </div>

                    <!-- Bahan Baku Section -->
                    <div class="bahan-baku-section">
                        <div class="section-header">
                            <h4><i class="fas fa-cubes"></i> Bahan Baku yang Diperlukan</h4>
                            <button type="button" class="btn btn-success btn-sm" onclick="addBahanBaku()">
                                <i class="fas fa-plus"></i> Tambah Bahan
                            </button>
                        </div>
                        <div id="bahanBakuContainer">
                            <!-- Bahan baku cards will be added here -->
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Simpan Jenis Produksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading">
            <i class="fas fa-spinner"></i>
            <p>Memproses data...</p>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="/js/admin_panel.js"></script>
<?= $this->endSection() ?>
