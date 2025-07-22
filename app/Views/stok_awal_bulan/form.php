<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
<link href="<?= base_url('css/form_stok_awal_bulan.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-warehouse"></i> Form Stock Opname</h1>
        <p>Sistem pencatatan stok fisik bulanan yang terintegrasi dan akurat</p>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-calendar-alt"></i> Pilih Periode Opname</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= base_url('/stok_awal_bulan/form') ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tanggal_opname_month" class="form-label">
                            <i class="fas fa-calendar"></i> Pilih Bulan & Tahun
                        </label>
                        <input type="month" name="tanggal_opname_month" id="tanggal_opname_month" 
                               class="form-input" value="<?= $selected_month_year ?>" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan Form
                        </button>
                    </div>
                    <div class="form-group">
                        <button type="button" id="btnTarikStok" class="btn btn-info">
                            <i class="fas fa-calculator"></i> Hitung & Import Stok
                        </button>
                    </div>
                    <div class="form-group">
                        <button type="button" id="btnClearForm" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Kosongkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php
    $mode_config = [
        'create' => [
            'title' => 'Input Stok Fisik Baru',
            'description' => 'Buat data stock opname baru untuk periode ini',
            'badge_class' => 'badge-success',
            'badge_text' => 'Baru',
            'icon_class' => 'create',
            'icon' => 'fas fa-plus-circle'
        ],
        'edit' => [
            'title' => 'Edit Stok Fisik',
            'description' => 'Perubahan akan menimpa data sebelumnya',
            'badge_class' => 'badge-warning',
            'badge_text' => 'Edit',
            'icon_class' => 'edit',
            'icon' => 'fas fa-edit'
        ]
    ];
    $current_mode = $mode_config[$mode] ?? $mode_config['create'];
    ?>

    <div class="status-card">
        <div class="status-info">
            <div class="status-icon <?= $current_mode['icon_class'] ?>">
                <i class="<?= $current_mode['icon'] ?>"></i>
            </div>
            <div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">
                    <?= $current_mode['title'] ?>
                </h3>
                <p style="color: var(--text-gray); margin: 0;">
                    <?= $current_mode['description'] ?>
                </p>
            </div>
        </div>
        <div class="badge <?= $current_mode['badge_class'] ?>">
            <?= $current_mode['badge_text'] ?>
        </div>
    </div>

    <div id="form-messages" class="alert" style="display: none;"></div>

    <form id="formOpnameData">
        <input type="hidden" name="tanggal_opname" value="<?= date('Y-m-01', strtotime($selected_month_year . '-01')) ?>">
        
        <div class="table-container">
            <div class="card-header">
                <h2>
                    <i class="<?= $current_mode['icon'] ?>"></i> 
                    Form Opname - Periode: <?= date('F Y', strtotime($selected_month_year . '-01')) ?>
                </h2>
                <div class="badge" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <?= count($produk_list) ?> Produk × <?= count($gudang_list) ?> Gudang
                </div>
            </div>

            <?php if ($mode === 'edit'): ?>
            <div class="alert alert-warning" style="display: flex; margin: 0; border-radius: 0;">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Mode edit aktif. Menyimpan akan menimpa data opname sebelumnya untuk periode ini.</span>
            </div>
            <?php endif; ?>

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
                                    <div style="font-size: 0.875rem; margin-bottom: 0.5rem; color: #f7931e;">
                                        <?= ($produk['satuan_per_dus'] > 1) ? $produk['satuan_per_dus'] . ' satuan/dus' : 'Satuan only' ?>
                                    </div>
                                </td>
                                <?php foreach ($gudang_list as $gudang): ?>
                                    <?php
                                        $is_satuan_only_product = (!isset($produk['satuan_per_dus']) || $produk['satuan_per_dus'] <= 1);
                                        $is_gudang_produksi = in_array($gudang['nama_gudang'], ['P1', 'P2', 'P3']);
                                        $disable_dus = $is_satuan_only_product;
                                        $disable_satuan = (!$is_satuan_only_product && $is_gudang_produksi);
                                        $dus_val = $existing_data[$produk['id_produk']][$gudang['id_gudang']]['dus'] ?? 0;
                                        $satuan_val = $existing_data[$produk['id_produk']][$gudang['id_gudang']]['satuan'] ?? 0;
                                    ?>
                                    <td>
                                        <input type="number" 
                                               name="items[<?= $produk['id_produk'] ?>][<?= $gudang['id_gudang'] ?>][dus]" 
                                               value="<?= $dus_val ?>" 
                                               min="0" 
                                               data-original-value="<?= $dus_val ?>" 
                                               class="table-input" 
                                               <?= $disable_dus ? 'disabled' : '' ?>>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="items[<?= $produk['id_produk'] ?>][<?= $gudang['id_gudang'] ?>][satuan]" 
                                               value="<?= $satuan_val ?>" 
                                               min="0" 
                                               data-original-value="<?= $satuan_val ?>" 
                                               class="table-input" 
                                               <?= $disable_satuan ? 'disabled' : '' ?>>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="submit-section">
                <button type="submit" class="submit-btn <?= $mode === 'edit' ? 'btn-warning' : 'btn-success' ?>">
                    <i class="fas fa-<?= $mode === 'edit' ? 'edit' : 'save' ?>"></i>
                    <?= $mode === 'edit' ? 'Update Data Opname' : 'Simpan Opname Baru' ?>
                </button>
            </div>
        </div>
    </form>
</div>

<div class="loading-overlay" id="loading-overlay">
    <div class="loading-spinner"></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script src="<?= base_url('js/form_stok_awal_bulan.js') ?>"></script>
<?= $this->endSection() ?>