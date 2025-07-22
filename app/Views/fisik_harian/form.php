<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="<?= base_url('css/form_fisik_harian.css') ?>" rel="stylesheet">
 <?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-clipboard-list"></i> Input Stok Fisik Harian</h1>
        <p>Sistem pencatatan dan perbandingan stok fisik dengan pembukuan yang terintegrasi</p>
    </div>

    <!-- Date Selection Card -->
    <div class="card fade-in">
        <div class="card-header">
            <h2><i class="fas fa-calendar-alt"></i> Pilih Tanggal Pengecekan</h2>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tanggal_fisik" class="form-label">
                            <i class="fas fa-calendar"></i> Tanggal Pengecekan Fisik
                        </label>
                        <input type="date" name="tanggal_fisik" id="tanggal_fisik" class="form-input"
                                value="<?php echo htmlspecialchars($selected_tanggal ?? date('Y-m-d')); ?>" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Tampilkan Form
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($selected_tanggal): ?>
        <div class="fade-in">
            <!-- Alert Messages -->
            <div id="formMessage" class="alert"></div>
            
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number" id="total-products"><?php echo count($produk_list); ?></span>
                    <div class="stat-label">Total Produk</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number" id="total-warehouses"><?php echo count($gudang_list); ?></span>
                    <div class="stat-label">Total Gudang</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number" id="filled-entries">0</span>
                    <div class="stat-label">Entri Terisi</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number" id="completion-percent">0%</span>
                    <div class="stat-label">Progress</div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-header">
                    <span class="font-semibold">Progress Pengisian</span>
                    <span class="text-sm" id="progress-text">0%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
            </div>

            <!-- Main Form -->
            <form id="formFisikHarian">
                <input type="hidden" name="tanggal_fisik" value="<?php echo htmlspecialchars($selected_tanggal); ?>">
                
                <div class="table-container">
                    <div class="card-header">
                        <h2><i class="fas fa-table"></i> Data Stok Fisik - <?php echo date('d F Y', strtotime($selected_tanggal)); ?></h2>
                        <button type="button" id="btnTarikStok" class="btn btn-info">
                            <i class="fas fa-download"></i>
                            Import Stok Pembukuan
                        </button>
                    </div>
                    
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th rowspan="2">Nama Produk</th>
                                    <?php foreach ($gudang_list as $gudang): ?>
                                        <th colspan="2"><?php echo htmlspecialchars($gudang['nama_gudang']); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <?php foreach ($gudang_list as $gudang): ?>
                                        <th>Dus</th>
                                        <th>Satuan</th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produk_list as $produk): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($produk['nama_produk']); ?>
                                            <div class="text-sm mb-2" style="color: #f7931e;">
                                                <?php echo ($produk['satuan_per_dus'] > 1) ? $produk['satuan_per_dus'] . ' satuan/dus' : 'Satuan only'; ?>
                                            </div>
                                        </td>
                                        <?php foreach ($gudang_list as $gudang): ?>
                                            <?php
                                                $is_satuan_only = (!isset($produk['satuan_per_dus']) || $produk['satuan_per_dus'] <= 1);
                                                $is_gudang_produksi = in_array($gudang['nama_gudang'], ['P1', 'P2', 'P3']);
                                                $disable_dus = $is_satuan_only;
                                                $disable_satuan = (!$is_satuan_only && $is_gudang_produksi);
                                                
                                                $dus_val = $existing_data[$produk['id_produk']][$gudang['id_gudang']]['dus'] ?? 0;
                                                $satuan_val = $existing_data[$produk['id_produk']][$gudang['id_gudang']]['satuan'] ?? 0;
                                                
                                                $stok_buku_dus = $stok_pembukuan_map[$produk['id_produk']][$gudang['id_gudang']]['jumlah_dus'] ?? 0;
                                                $stok_buku_satuan = $stok_pembukuan_map[$produk['id_produk']][$gudang['id_gudang']]['jumlah_satuan'] ?? 0;
                                            ?>
                                            <td>
                                                <input type="number"
                                                        name="items[<?php echo $produk['id_produk']; ?>][<?php echo $gudang['id_gudang']; ?>][dus]"
                                                        value="<?php echo $dus_val; ?>"
                                                        min="0"
                                                        data-stok-buku="<?php echo $stok_buku_dus; ?>"
                                                        data-original-value="<?php echo $dus_val; ?>"
                                                        class="table-input"
                                                        title="Stok Pembukuan: <?php echo $stok_buku_dus; ?> dus"
                                                        <?php if ($disable_dus) echo 'disabled'; ?>>
                                            </td>
                                            <td>
                                                <input type="number"
                                                        name="items[<?php echo $produk['id_produk']; ?>][<?php echo $gudang['id_gudang']; ?>][satuan]"
                                                        value="<?php echo $satuan_val; ?>"
                                                        min="0"
                                                        data-stok-buku="<?php echo $stok_buku_satuan; ?>"
                                                        data-original-value="<?php echo $satuan_val; ?>"
                                                        class="table-input"
                                                        title="Stok Pembukuan: <?php echo $stok_buku_satuan; ?> satuan"
                                                        <?php if ($disable_satuan) echo 'disabled'; ?>>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="padding: 2rem; background: #f8fafc; border-top: 2px solid #e5e7eb;">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i>
                            Simpan Data Fisik Harian
                        </button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loading-overlay">
    <div class="loading-spinner"></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="<?= base_url('js/form_fisik_harian.js') ?>"></script>
<?= $this->endSection() ?>
