<?php if (empty($report_data)): ?>
    <tr>
        <td colspan="8">
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Tidak Ada Data</h3>
                <p>Tidak ada riwayat operstock sesuai filter.</p>
            </div>
        </td>
    </tr>
<?php else: ?>
    <?php foreach ($report_data as $row): ?>
        <tr id="row-<?= $row['id'] ?>">
            <td><?= date('d-m-Y H:i', strtotime($row['waktu_kirim'])) ?></td>
            <td><?= esc($row['no_surat_jalan']) ?></td>
            <td class="text-left"><?= esc($row['nama_produk']) ?></td>
            <td><?= esc($row['gudang_asal']) ?></td>
            <td>
                <span class="transfer-badge">
                    <i class="fas fa-arrow-right"></i> 
                    <?= esc($row['gudang_tujuan']) ?>
                </span>
            </td>
            <td class="jumlah-dus"><?= number_format($row['jumlah_dus_dikirim']) ?></td>
            <td class="jumlah-satuan"><?= number_format($row['jumlah_satuan_dikirim']) ?></td>
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
