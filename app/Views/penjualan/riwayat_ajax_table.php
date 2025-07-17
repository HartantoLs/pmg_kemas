<?php if (empty($report_data)): ?>
    <tr><td colspan="8"><div class="empty-state"><i class="fas fa-inbox"></i><h3>Tidak Ada Data</h3><p>Tidak ada riwayat penjualan sesuai filter.</p></div></td></tr>
<?php else: ?>
    <?php foreach ($report_data as $row): ?>
        <tr id="row-<?= $row['id'] ?>">
            <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
            <td><?= esc($row['no_surat_jalan']) ?></td>
            <td class="text-left"><?= esc($row['customer']) ?></td>
            <td class="text-left"><?= esc($row['nama_produk']) ?></td>
            <td><?= esc($row['nama_gudang']) ?></td>
            <td><?= number_format($row['jumlah_dus']) ?></td>
            <td><?= number_format($row['jumlah_satuan']) ?></td>
            <td>
                <button class="btn btn-edit" data-id="<?= $row['id'] ?>"><i class="fas fa-edit"></i> Edit</button> 
                <button class="btn btn-delete" data-id="<?= $row['id'] ?>"><i class="fas fa-trash"></i> Hapus</button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>