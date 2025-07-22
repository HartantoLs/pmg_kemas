<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="page-header">
        <h1><i class="fas fa-clipboard-list"></i> Input Stok Fisik Harian</h1>
        <p>Sistem pencatatan dan perbandingan stok fisik dengan pembukuan yang terintegrasi</p>
    </div>

    <div class="card fade-in">
        <div class="card-header">
            <h2><i class="fas fa-calendar-alt"></i> Pilih Tanggal Pengecekan</h2>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" action="<?= base_url('fisik_harian/form') ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tanggal_fisik" class="form-label">
                            <i class="fas fa-calendar"></i> Tanggal Pengecekan Fisik
                        </label>
                        <input type="date" name="tanggal_fisik" id="tanggal_fisik" class="form-input"
                               value="<?= $selected_tanggal ?? date('Y-m-d') ?>" required>
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
            <div id="formMessage" class="alert"></div>

            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number" id="total-products"><?= count($produk_list) ?></span>
                    <div class="stat-label">Total Produk</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number" id="total-warehouses"><?= count($gudang_list) ?></span>
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

            <div class="progress-container">
                <div class="progress-header">
                    <span class="font-semibold">Progress Pengisian</span>
                    <span class="text-sm" id="progress-text">0%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
            </div>

            <form id="formFisikHarian">
                <input type="hidden" name="tanggal_fisik" value="<?= $selected_tanggal ?>">

                <div class="table-container">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-table"></i> 
                            Data Stok Fisik - <?= date('d F Y', strtotime($selected_tanggal)) ?>
                        </h2>
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
                                        <th colspan="2"><?= esc($gudang['nama_gudang']) ?></th>
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
                                            <?= esc($produk['nama_produk']) ?>
                                            <div class="text-sm mb-2" style="color: #f7931e;">
                                                <?= ($produk['satuan_per_dus'] > 1) ? $produk['satuan_per_dus'] . ' satuan/dus' : 'Satuan only' ?>
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
                                                       name="items[<?= $produk['id_produk'] ?>][<?= $gudang['id_gudang'] ?>][dus]"
                                                       value="<?= $dus_val ?>"
                                                       min="0"
                                                       data-stok-buku="<?= $stok_buku_dus ?>"
                                                       data-original-value="<?= $dus_val ?>"
                                                       class="table-input"
                                                       title="Stok Pembukuan: <?= $stok_buku_dus ?> dus"
                                                       <?= $disable_dus ? 'disabled' : '' ?>>
                                            </td>
                                            <td>
                                                <input type="number"
                                                       name="items[<?= $produk['id_produk'] ?>][<?= $gudang['id_gudang'] ?>][satuan]"
                                                       value="<?= $satuan_val ?>"
                                                       min="0"
                                                       data-stok-buku="<?= $stok_buku_satuan ?>"
                                                       data-original-value="<?= $satuan_val ?>"
                                                       class="table-input"
                                                       title="Stok Pembukuan: <?= $stok_buku_satuan ?> satuan"
                                                       <?= $disable_satuan ? 'disabled' : '' ?>>
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

<div class="loading-overlay" id="loading-overlay">
    <div class="loading-spinner"></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/form_fisik_harian.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/form_fisik_harian.js') ?>"></script>
<?= $this->endSection() ?>
