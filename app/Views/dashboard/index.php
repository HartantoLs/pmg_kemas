<?= $this->extend('layout/main') ?>

<?= $this->section('page_css') ?>
    <link href="/css/dashboard.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('main_content') ?>
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-5 anim-fade-in">
            <h1 class="fw-bold"><?= esc($sapaan) ?>, Admin!</h1>
            <p class="text-secondary fs-5">Ringkasan aktivitas dan data penting dari sistem gudang Anda.</p>
        </div>

        <section class="mb-5 anim-slide-up">
            <h2 class="section-title"><i class="fa-solid fa-rocket text-secondary"></i> Akses Cepat</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4"><div class="action-card" data-bs-toggle="modal" data-bs-target="#formInputModal"><div class="icon"><i class="fa-solid fa-file-pen"></i></div><h3>Form Input</h3><p>Masukan data transaksi seperti penjualan, pengemasan, dan lainnya.</p></div></div>
                <div class="col-lg-4 col-md-6 mb-4"><div class="action-card" data-bs-toggle="modal" data-bs-target="#laporanModal"><div class="icon"><i class="fa-solid fa-chart-pie"></i></div><h3>Laporan</h3><p>Lihat laporan penting seperti kartu stok, mutasi produk, dan lainnya.</p></div></div>
                <div class="col-lg-4 col-md-6 mb-4"><div class="action-card" data-bs-toggle="modal" data-bs-target="#riwayatModal"><div class="icon"><i class="fa-solid fa-clock-rotate-left"></i></div><h3>Riwayat</h3><p>Telusuri kembali semua catatan transaksi yang telah tersimpan di sistem.</p></div></div>
            </div>
        </section>

        <section class="mb-5 anim-slide-up" style="animation-delay: 0.1s;">
            <h2 class="section-title"><i class="fa-solid fa-chart-simple text-secondary"></i> Statistik Utama</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4"><div class="stat-card"><div class="icon-wrapper bg-primary"><i class="fa-solid fa-boxes-stacked"></i></div><div class="stat-info"><div class="stat-label">Total Jenis Produk</div><div class="stat-value"><?= esc($total_produk) ?></div></div></div></div>
                <div class="col-lg-4 col-md-6 mb-4"><div class="stat-card"><div class="icon-wrapper bg-success"><i class="fa-solid fa-cart-shopping"></i></div><div class="stat-info"><div class="stat-label">Penjualan Bulan Ini</div><div class="stat-value"><?= esc($penjualan_bulan_ini_formatted) ?></div></div></div></div>
                <div class="col-lg-4 col-md-6 mb-4"><div class="stat-card"><div class="icon-wrapper bg-warning"><i class="fa-solid fa-triangle-exclamation"></i></div><div class="stat-info"><div class="stat-label">Produk Stok Menipis</div><div class="stat-value"><?= esc($stok_menipis) ?></div></div></div></div>
            </div>
        </section>

        <section class="anim-slide-up" style="animation-delay: 0.2s;">
            <h2 class="section-title"><i class="fa-solid fa-bolt text-secondary"></i> Aktivitas Terbaru</h2>
            <div class="table-container"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Jenis Aktivitas</th><th>Detail</th><th class="text-end">Waktu</th></tr></thead><tbody>
                <?php if (count($aktivitas_terbaru) > 0): foreach ($aktivitas_terbaru as $aktivitas): ?>
                <tr><td><div class="d-flex align-items-center"><i class="fa-solid <?= esc($aktivitas['icon']) ?> <?= esc($aktivitas['color']) ?> me-3 fs-5"></i><span class="fw-bold"><?= esc($aktivitas['jenis']) ?></span></div></td><td class="text-secondary"><?= esc($aktivitas['detail']) ?></td><td class="text-end text-secondary"><?= date('d M Y, H:i', strtotime($aktivitas['waktu'])) ?></td></tr>
                <?php endforeach; else: ?>
                <tr><td colspan="3" class="text-center text-secondary py-4">Belum ada aktivitas terbaru.</td></tr>
                <?php endif; ?>
            </tbody></table></div></div>
        </section>
    </div>
</main>

<div class="modal fade" id="formInputModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Pilih Form Input</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4"><div class="row">
                <div class="col-md-6 mb-3"><a href="<?= site_url('pengemasan/input') ?>" class="modal-link-card"><div class="icon-wrapper"><i class="fa-solid fa-box"></i></div><div class="link-text"><strong>Input Pengemasan</strong><small>Catat hasil produksi.</small></div></a></div>
                <div class="col-md-6 mb-3"><a href="<?= site_url('penjualan/input') ?>" class="modal-link-card"><div class="icon-wrapper"><i class="fa-solid fa-cart-arrow-down"></i></div><div class="link-text"><strong>Input Penjualan</strong><small>Catat barang keluar ke customer.</small></div></a></div>
                </div></div>
        </div>
    </div>
</div>

<div class="modal fade" id="laporanModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Pilih Laporan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4"><div class="row">
                <div class="col-md-6 mb-3"><a href="<?= site_url('laporan/stok') ?>" class="modal-link-card"><div class="icon-wrapper"><i class="fa-solid fa-boxes-stacked"></i></div><div class="link-text"><strong>Lihat Stok</strong><small>Tampilan stok terkini di semua gudang.</small></div></a></div>
                </div></div>
        </div>
    </div>
</div>

<div class="modal fade" id="riwayatModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Pilih Riwayat Transaksi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4"><div class="row">
                <div class="col-md-6 mb-3"><a href="<?= site_url('pengemasan/riwayat') ?>" class="modal-link-card"><div class="icon-wrapper"><i class="fa-solid fa-box"></i></div><div class="link-text"><strong>Riwayat Pengemasan</strong><small>Semua data hasil produksi.</small></div></a></div>
                <div class="col-md-6 mb-3"><a href="<?= site_url('penjualan/riwayat') ?>" class="modal-link-card"><div class="icon-wrapper"><i class="fa-solid fa-receipt"></i></div><div class="link-text"><strong>Riwayat Penjualan</strong><small>Semua data penjualan yang tercatat.</small></div></a></div>
                </div></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<?= $this->endSection() ?>