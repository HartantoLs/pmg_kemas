<?php if (empty($report_data)): ?>
    <tr>
        <td colspan="5">
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>Tidak Ada Data</h3>
                <p>Tidak ada riwayat seleksi sesuai filter.</p>
            </div>
        </td>
    </tr>
<?php else: ?>
    <?php foreach ($report_data as $row): ?>
        <tr id="row-<?= $row['id'] ?>">
            <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
            <td class="text-left"><?= esc($row['nama_produk']) ?></td>
            <td class="pcs-aman"><?= number_format($row['pcs_aman']) ?></td>
            <td class="pcs-curah"><?= number_format($row['pcs_curah']) ?></td>
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
