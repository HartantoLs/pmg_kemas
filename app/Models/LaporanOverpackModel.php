<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanOverpackModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Mengambil data laporan overpack
     */
    public function getLaporanOverpack(array $filters)
    {
        try {
            $tipe_laporan = $filters['tipe_laporan'] ?? 'harian';
            $selected_date = $filters['tanggal'] ?? date('Y-m-d');
            $start_date = $filters['tanggal_mulai'] ?? date('Y-m-01');
            $end_date = $filters['tanggal_akhir'] ?? date('Y-m-t');
            $filter_produk = $filters['produk_id'] ?? 'semua';

            $builder = $this->db->table('produk p');
            $builder->select('p.id_produk, p.nama_produk, p.satuan_per_dus');

            if ($tipe_laporan === 'harian') {
                // Kueri untuk laporan status HINGGA tanggal tertentu
                $builder->select('p.id_produk, p.nama_produk, p.satuan_per_dus, 
                    COALESCE(masuk.total_masuk, 0) AS total_masuk,
                    COALESCE(seleksi.total_aman, 0) AS total_aman,
                    COALESCE(seleksi.total_curah, 0) AS total_curah,
                    COALESCE(kemas.total_kemas, 0) AS total_kemas');

                // JOIN untuk total masuk
                $builder->join('(SELECT okd.produk_id, 
                    SUM((okd.jumlah_dus_kembali * prd.satuan_per_dus) + okd.jumlah_satuan_kembali) AS total_masuk 
                    FROM operpack_kerusakan ok 
                    JOIN operpack_kerusakan_detail okd ON ok.id = okd.operpack_id 
                    JOIN produk prd ON okd.produk_id = prd.id_produk
                    WHERE DATE(ok.waktu_diterima) <= "' . $selected_date . '" 
                    GROUP BY okd.produk_id) AS masuk', 'p.id_produk = masuk.produk_id', 'left');

                // JOIN untuk seleksi
                $builder->join('(SELECT produk_id, SUM(pcs_aman) as total_aman, SUM(pcs_curah) as total_curah 
                    FROM operpack_seleksi 
                    WHERE tanggal <= "' . $selected_date . '" 
                    GROUP BY produk_id) AS seleksi', 'p.id_produk = seleksi.produk_id', 'left');

                // JOIN untuk kemas
                $builder->join('(SELECT produk_id, SUM(jumlah_kemas) as total_kemas 
                    FROM operpack_kemas_ulang 
                    WHERE tanggal <= "' . $selected_date . '" 
                    GROUP BY produk_id) AS kemas', 'p.id_produk = kemas.produk_id', 'left');

            } else { // rekap
                // Kueri untuk laporan mutasi DI ANTARA rentang tanggal
                $builder->select('p.id_produk, p.nama_produk, p.satuan_per_dus, 
                    COALESCE(masuk.total_masuk, 0) AS total_masuk,
                    COALESCE(seleksi.total_aman, 0) AS total_aman,
                    COALESCE(seleksi.total_curah, 0) AS total_curah,
                    COALESCE(kemas.total_kemas, 0) AS total_kemas');

                // JOIN untuk total masuk
                $builder->join('(SELECT okd.produk_id, 
                    SUM((okd.jumlah_dus_kembali * prd.satuan_per_dus) + okd.jumlah_satuan_kembali) AS total_masuk
                    FROM operpack_kerusakan ok 
                    JOIN operpack_kerusakan_detail okd ON ok.id = okd.operpack_id 
                    JOIN produk prd ON okd.produk_id = prd.id_produk
                    WHERE DATE(ok.waktu_diterima) BETWEEN "' . $start_date . '" AND "' . $end_date . '" 
                    GROUP BY okd.produk_id) AS masuk', 'p.id_produk = masuk.produk_id', 'left');

                // JOIN untuk seleksi
                $builder->join('(SELECT produk_id, SUM(pcs_aman) as total_aman, SUM(pcs_curah) as total_curah 
                    FROM operpack_seleksi 
                    WHERE tanggal BETWEEN "' . $start_date . '" AND "' . $end_date . '" 
                    GROUP BY produk_id) AS seleksi', 'p.id_produk = seleksi.produk_id', 'left');

                // JOIN untuk kemas
                $builder->join('(SELECT produk_id, SUM(jumlah_kemas) as total_kemas 
                    FROM operpack_kemas_ulang 
                    WHERE tanggal BETWEEN "' . $start_date . '" AND "' . $end_date . '" 
                    GROUP BY produk_id) AS kemas', 'p.id_produk = kemas.produk_id', 'left');
            }

            if ($filter_produk !== 'semua') {
                $builder->where('p.id_produk', $filter_produk);
            }

            $builder->orderBy('p.nama_produk');
            $results = $builder->get()->getResultArray();

            $report_data = [];
            $grand_totals = [];

            foreach ($results as $row) {
                $data_row = [];
                $satuan_per_dus = $row['satuan_per_dus'] ?? 1;

                if ($tipe_laporan === 'harian') {
                    $total_sudah_seleksi = $row['total_aman'] + $row['total_curah'];
                    $belum_seleksi = $row['total_masuk'] - $total_sudah_seleksi;
                    $siap_kemas = $row['total_aman'] - $row['total_kemas'];
                    $sudah_kemas = $row['total_kemas'];
                    $total_curah = $row['total_curah'];
                    $total_keseluruhan = $belum_seleksi + $siap_kemas + $sudah_kemas + $total_curah;

                    if ($total_keseluruhan != 0) {
                        $data_row = [
                            'nama_produk' => $row['nama_produk'],
                            'satuan_per_dus' => $satuan_per_dus,
                            'belum_seleksi' => $belum_seleksi,
                            'siap_kemas' => $siap_kemas,
                            'sudah_kemas' => $sudah_kemas,
                            'total_curah' => $total_curah,
                            'total_keseluruhan' => $total_keseluruhan
                        ];
                    }
                } else { // rekap
                    if ($row['total_masuk'] > 0 || $row['total_aman'] > 0 || $row['total_curah'] > 0 || $row['total_kemas'] > 0) {
                        $data_row = [
                            'nama_produk' => $row['nama_produk'],
                            'satuan_per_dus' => $satuan_per_dus,
                            'total_masuk' => $row['total_masuk'],
                            'total_aman' => $row['total_aman'],
                            'total_curah' => $row['total_curah'],
                            'total_kemas' => $row['total_kemas']
                        ];
                    }
                }

                if (!empty($data_row)) {
                    $report_data[] = $data_row;
                    foreach ($data_row as $key => $value) {
                        if (is_numeric($value) && $key !== 'satuan_per_dus') {
                            $grand_totals[$key] = ($grand_totals[$key] ?? 0) + $value;
                        }
                    }
                }
            }

            return [
                'report_data' => $report_data,
                'grand_totals' => $grand_totals
            ];

        } catch (\Exception $e) {
            log_message('error', 'Error in getLaporanOverpack: ' . $e->getMessage());
            return [
                'report_data' => [],
                'grand_totals' => []
            ];
        }
    }

    /**
     * Format stok untuk display
     */
    public function formatStok($pcs, $satuan_per_dus)
    {
        if ($pcs == 0) return '-';

        // Jika satuan per dus adalah 1, hanya tampilkan Pcs
        if ($satuan_per_dus <= 1) {
            return number_format($pcs) . " Pcs";
        }

        $dus = floor($pcs / $satuan_per_dus);
        $sisa_pcs = $pcs % $satuan_per_dus;

        $hasil = [];
        if ($dus > 0) $hasil[] = number_format($dus) . " Dus";
        if ($sisa_pcs > 0) $hasil[] = number_format($sisa_pcs) . " Pcs";

        return !empty($hasil) ? implode(' ', $hasil) : '-';
    }

    /**
     * Export data ke CSV
     */
    public function exportToCSV(array $data, array $filters)
    {
        $csv_data = [];
        $tipe_laporan = $filters['tipe_laporan'] ?? 'harian';
        
        $csv_data[] = ['Laporan Stok Overpack'];
        
        if ($tipe_laporan === 'harian') {
            $csv_data[] = ['Tanggal: ' . date('d F Y', strtotime($filters['tanggal']))];
        } else {
            $csv_data[] = ['Periode: ' . date('d F Y', strtotime($filters['tanggal_mulai'])) . ' s/d ' . date('d F Y', strtotime($filters['tanggal_akhir']))];
        }
        
        $csv_data[] = []; // Empty row

        if ($tipe_laporan === 'harian') {
            // Header untuk laporan harian
            $csv_data[] = [
                'No', 'Nama Produk', 'Isi/Dus', 
                'Belum Diseleksi', 'Siap Dikemas', 'Sudah Dikemas', 
                'Total Curah', 'Total Overpack'
            ];

            // Data rows
            foreach ($data['report_data'] as $index => $row) {
                $csv_data[] = [
                    $index + 1,
                    $row['nama_produk'],
                    number_format($row['satuan_per_dus']),
                    number_format($row['belum_seleksi']),
                    number_format($row['siap_kemas']),
                    number_format($row['sudah_kemas']),
                    number_format($row['total_curah']),
                    number_format($row['total_keseluruhan'])
                ];
            }
        } else {
            // Header untuk laporan rekap
            $csv_data[] = [
                'No', 'Nama Produk', 'Isi/Dus', 
                'Total Masuk', 'Seleksi Aman', 'Seleksi Curah', 'Kemas Ulang'
            ];

            // Data rows
            foreach ($data['report_data'] as $index => $row) {
                $csv_data[] = [
                    $index + 1,
                    $row['nama_produk'],
                    number_format($row['satuan_per_dus']),
                    number_format($row['total_masuk']),
                    number_format($row['total_aman']),
                    number_format($row['total_curah']),
                    number_format($row['total_kemas'])
                ];
            }
        }

        return $csv_data;
    }
}
