<?php if (empty($report_data)): ?>
    <tr>
        <td colspan="8">
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Tidak Ada Data</h3>
                <p>Tidak ada riwayat kerusakan sesuai filter yang dipilih</p>
            </div>
        </td>
    </tr>
<?php else: ?>
    <?php foreach ($report_data as $row): ?>
        <tr id="row-<?= $row['id'] ?>" class="anim-fade-in">
            <td><?= date('d-m-Y H:i', strtotime($row['waktu_diterima'])) ?></td>
            <td><?= esc($row['no_surat_jalan']) ?></td>
            <td>
                <span class="category-badge <?= strtolower($row['kategori_asal']) === 'internal' ? 'category-internal' : 'category-eksternal' ?>">
                    <i class="fas fa-<?= strtolower($row['kategori_asal']) === 'internal' ? 'building' : 'external-link-alt' ?>"></i>
                    <?= esc($row['kategori_asal']) ?>
                </span>
            </td>
            <td class="text-left"><?= esc($row['asal']) ?></td>
            <td class="text-left"><?= esc($row['nama_produk']) ?></td>
            <td class="jumlah-dus"><?= number_format($row['jumlah_dus_kembali']) ?></td>
            <td class="jumlah-satuan"><?= number_format($row['jumlah_satuan_kembali']) ?></td>
            <td>
                <button class="btn btn-edit" data-id="<?= $row['id'] ?>">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-delete" data-id="<?= $row['id'] ?>">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
