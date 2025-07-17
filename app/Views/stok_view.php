<!DOCTYPE html>
<html>
<head>
    <title>Stok Produk</title>
    <style>
        table, th, td { border: 1px solid black; border-collapse: collapse; padding: 6px; }
    </style>
</head>
<body>
    <h2>Daftar Stok Produk per Gudang</h2>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Gudang</th>
                <th>Jumlah Dus</th>
                <th>Jumlah Satuan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stok as $item): ?>
                <tr>
                    <td><?= esc($item['nama_produk']) ?></td>
                    <td><?= esc($item['nama_gudang']) ?></td>
                    <td><?= $item['sistem_dus'] ?></td>
                    <td><?= $item['sistem_satuan'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
