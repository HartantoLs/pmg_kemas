<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getKartuStok($filter_produk, $filter_gudang, $tgl_mulai, $tgl_akhir)
    {
        $data = [
            'report_data' => [],
            'saldo_awal_dus' => 0,
            'saldo_awal_satuan' => 0,
            'selected_produk_name' => '',
            'selected_gudang_name' => 'Semua Gudang'
        ];

        if (!$filter_produk) {
            return $data;
        }

        // Get product name
        $produk = $this->db->table('produk')->where('id_produk', $filter_produk)->get()->getRowArray();
        if ($produk) {
            $data['selected_produk_name'] = $produk['nama_produk'];
        }

        // Get warehouse name
        if ($filter_gudang !== 'semua') {
            $gudang = $this->db->table('gudang')->where('id_gudang', $filter_gudang)->get()->getRowArray();
            if ($gudang) {
                $data['selected_gudang_name'] = $gudang['nama_gudang'];
            }
        }

        // Calculate saldo awal from stok_opname
        $builder = $this->db->table('stok_opname');
        $builder->select('tanggal_opname, SUM(jumlah_dus_opname) as total_dus, SUM(jumlah_satuan_opname) as total_satuan');
        $builder->where('produk_id', $filter_produk);
        $builder->where('tanggal_opname <', $tgl_mulai);
        
        if ($filter_gudang !== 'semua') {
            $builder->where('gudang_id', $filter_gudang);
        }
        
        $builder->groupBy('tanggal_opname');
        $builder->orderBy('tanggal_opname', 'DESC');
        $builder->limit(1);
        
        $opname_result = $builder->get();
        
        $saldo_dasar_dus = 0;
        $saldo_dasar_satuan = 0;
        $tgl_mulai_hitung = '1970-01-01';
        
        if ($opname_result->getNumRows() > 0) {
            $opname = $opname_result->getRowArray();
            $saldo_dasar_dus = (int)$opname['total_dus'];
            $saldo_dasar_satuan = (int)$opname['total_satuan'];
            $tgl_mulai_hitung = $opname['tanggal_opname'];
        }

        // Calculate mutations before report period using individual tables
        $mutasi_dus = 0;
        $mutasi_satuan = 0;

        // 1. Produksi (POSITIF)
        $builder = $this->db->table('pengemasan');
        $builder->select('SUM(jumlah_dus) as dus, SUM(jumlah_satuan) as satuan');
        $builder->where('produk_id', $filter_produk);
        $builder->where('DATE(tanggal) >=', $tgl_mulai_hitung);
        $builder->where('DATE(tanggal) <', $tgl_mulai);
        
        if ($filter_gudang !== 'semua') {
            $builder->where('gudang_id', $filter_gudang);
        }
        
        $prod_result = $builder->get()->getRowArray();
        if ($prod_result) {
            $mutasi_dus += $prod_result['dus'] ?? 0;
            $mutasi_satuan += $prod_result['satuan'] ?? 0;
        }

        // 2. Penjualan (NEGATIF)
        $builder = $this->db->table('penjualan p');
        $builder->select('SUM(pd.jumlah_dus) as dus, SUM(pd.jumlah_satuan) as satuan');
        $builder->join('penjualan_detail pd', 'p.id = pd.penjualan_id');
        $builder->where('pd.produk_id', $filter_produk);
        $builder->where('DATE(p.tanggal) >=', $tgl_mulai_hitung);
        $builder->where('DATE(p.tanggal) <', $tgl_mulai);
        
        if ($filter_gudang !== 'semua') {
            $builder->where('pd.gudang_id', $filter_gudang);
        }
        
        $jual_result = $builder->get()->getRowArray();
        if ($jual_result) {
            $mutasi_dus -= $jual_result['dus'] ?? 0;
            $mutasi_satuan -= $jual_result['satuan'] ?? 0;
        }

        $data['saldo_awal_dus'] = $saldo_dasar_dus + $mutasi_dus;
        $data['saldo_awal_satuan'] = $saldo_dasar_satuan + $mutasi_satuan;

        // Get transaction details for the report period
        $report_data = [];
        
        // Get pengemasan data
        $builder = $this->db->table('pengemasan p');
        $builder->select("DATE(p.tanggal) as tanggal_transaksi, p.gudang_id, 'Produksi' as tipe_transaksi, p.jumlah_dus as perubahan_dus, p.jumlah_satuan as perubahan_satuan");
        $builder->where('p.produk_id', $filter_produk);
        $builder->where('DATE(p.tanggal) >=', $tgl_mulai);
        $builder->where('DATE(p.tanggal) <=', $tgl_akhir);
        
        if ($filter_gudang !== 'semua') {
            $builder->where('p.gudang_id', $filter_gudang);
        }
        
        $pengemasan_data = $builder->get()->getResultArray();
        $report_data = array_merge($report_data, $pengemasan_data);

        // Get penjualan data
        $builder = $this->db->table('penjualan p');
        $builder->select("DATE(p.tanggal) as tanggal_transaksi, pd.gudang_id, 'Penjualan' as tipe_transaksi, -pd.jumlah_dus as perubahan_dus, -pd.jumlah_satuan as perubahan_satuan");
        $builder->join('penjualan_detail pd', 'p.id = pd.penjualan_id');
        $builder->where('pd.produk_id', $filter_produk);
        $builder->where('DATE(p.tanggal) >=', $tgl_mulai);
        $builder->where('DATE(p.tanggal) <=', $tgl_akhir);
        
        if ($filter_gudang !== 'semua') {
            $builder->where('pd.gudang_id', $filter_gudang);
        }
        
        $penjualan_data = $builder->get()->getResultArray();
        $report_data = array_merge($report_data, $penjualan_data);

        // Sort by date
        usort($report_data, function($a, $b) {
            return strcmp($a['tanggal_transaksi'], $b['tanggal_transaksi']);
        });

        $data['report_data'] = $report_data;

        return $data;
    }

    public function getMutasiStok($tipe_laporan, $tgl_laporan, $tgl_mulai, $tgl_akhir, $filter_gudang, $filter_produk)
    {
        $data = [
            'report_data' => [],
            'warehouse_columns' => ['P1', 'P2', 'P3'],
            'totals' => [
                'saldo_awal_dus' => 0, 'saldo_awal_satuan' => 0,
                'produksi_dus' => 0, 'produksi_satuan' => 0,
                'penerimaan_dus' => 0, 'penerimaan_satuan' => 0,
                'pengeluaran_dus' => 0, 'pengeluaran_satuan' => 0,
                'saldo_akhir_dus' => 0, 'saldo_akhir_satuan' => 0
            ]
        ];

        // Get warehouse columns based on filter
        if ($filter_gudang !== 'semua') {
            $gudang = $this->db->table('gudang')->where('id_gudang', $filter_gudang)->get()->getRowArray();
            if ($gudang) {
                switch ($gudang['nama_gudang']) {
                    case 'P1': $data['warehouse_columns'] = ['P2', 'P3']; break;
                    case 'P2': $data['warehouse_columns'] = ['P1', 'P3']; break;
                    case 'P3': $data['warehouse_columns'] = ['P1', 'P2']; break;
                }
            }
        }

        // Determine date range
        $tanggal_hitung_awal = ($tipe_laporan === 'harian') ? $tgl_laporan : $tgl_mulai;
        $tanggal_akhir_hitung = ($tipe_laporan === 'harian') ? $tgl_laporan : $tgl_akhir;

        // Get products
        $builder = $this->db->table('produk');
        $builder->select('id_produk, nama_produk, satuan_per_dus');
        
        if ($filter_produk !== 'semua') {
            $builder->where('id_produk', $filter_produk);
        }
        
        $builder->orderBy('nama_produk');
        $produk_list = $builder->get()->getResultArray();
        
        // Process each product
        foreach ($produk_list as $produk) {
            $report_row = [
                'id_produk' => $produk['id_produk'],
                'nama_produk' => $produk['nama_produk'],
                'isi' => $produk['satuan_per_dus'],
                'saldo_awal_dus' => 0,
                'saldo_awal_satuan' => 0,
                'produksi_dus' => 0,
                'produksi_satuan' => 0,
                'op_masuk_p1_dus' => 0, 'op_masuk_p1_satuan' => 0,
                'op_masuk_p2_dus' => 0, 'op_masuk_p2_satuan' => 0,
                'op_masuk_p3_dus' => 0, 'op_masuk_p3_satuan' => 0,
                'overpack_masuk_dus' => 0, 'overpack_masuk_satuan' => 0,
                'jual_dus' => 0, 'jual_satuan' => 0,
                'op_keluar_p1_dus' => 0, 'op_keluar_p1_satuan' => 0,
                'op_keluar_p2_dus' => 0, 'op_keluar_p2_satuan' => 0,
                'op_keluar_p3_dus' => 0, 'op_keluar_p3_satuan' => 0,
                'overpack_keluar_dus' => 0, 'overpack_keluar_satuan' => 0,
            ];

            // Calculate saldo awal (simplified)
            $builder = $this->db->table('stok_opname');
            $builder->select('SUM(jumlah_dus_opname) as total_dus, SUM(jumlah_satuan_opname) as total_satuan');
            $builder->where('produk_id', $produk['id_produk']);
            $builder->where('DATE_FORMAT(tanggal_opname, "%Y-%m") <=', date('Y-m', strtotime($tanggal_hitung_awal)));
            
            if ($filter_gudang !== 'semua') {
                $builder->where('gudang_id', $filter_gudang);
            }
            
            $opname_result = $builder->get()->getRowArray();
            if ($opname_result) {
                $report_row['saldo_awal_dus'] = $opname_result['total_dus'] ?? 0;
                $report_row['saldo_awal_satuan'] = $opname_result['total_satuan'] ?? 0;
            }

            // Get produksi data
            $builder = $this->db->table('pengemasan');
            $builder->select('SUM(jumlah_dus) as total_dus, SUM(jumlah_satuan) as total_satuan');
            $builder->where('produk_id', $produk['id_produk']);
            $builder->where('DATE(tanggal) >=', $tanggal_hitung_awal);
            $builder->where('DATE(tanggal) <=', $tanggal_akhir_hitung);
            
            if ($filter_gudang !== 'semua') {
                $builder->where('gudang_id', $filter_gudang);
            }
            
            $prod_result = $builder->get()->getRowArray();
            if ($prod_result) {
                $report_row['produksi_dus'] = $prod_result['total_dus'] ?? 0;
                $report_row['produksi_satuan'] = $prod_result['total_satuan'] ?? 0;
            }

            // Get penjualan data
            $builder = $this->db->table('penjualan p');
            $builder->select('SUM(pd.jumlah_dus) as total_dus, SUM(pd.jumlah_satuan) as total_satuan');
            $builder->join('penjualan_detail pd', 'p.id = pd.penjualan_id');
            $builder->where('pd.produk_id', $produk['id_produk']);
            $builder->where('DATE(p.tanggal) >=', $tanggal_hitung_awal);
            $builder->where('DATE(p.tanggal) <=', $tanggal_akhir_hitung);
            
            if ($filter_gudang !== 'semua') {
                $builder->where('pd.gudang_id', $filter_gudang);
            }
            
            $jual_result = $builder->get()->getRowArray();
            if ($jual_result) {
                $report_row['jual_dus'] = $jual_result['total_dus'] ?? 0;
                $report_row['jual_satuan'] = $jual_result['total_satuan'] ?? 0;
            }
            
            $data['report_data'][] = $report_row;
        }

        // Calculate totals
        foreach ($data['report_data'] as $row) {
            $penerimaan_dus = $row['produksi_dus'] + $row['op_masuk_p1_dus'] + $row['op_masuk_p2_dus'] + $row['op_masuk_p3_dus'] + $row['overpack_masuk_dus'];
            $penerimaan_satuan = $row['produksi_satuan'] + $row['op_masuk_p1_satuan'] + $row['op_masuk_p2_satuan'] + $row['op_masuk_p3_satuan'] + $row['overpack_masuk_satuan'];
            $pengeluaran_dus = $row['jual_dus'] + $row['op_keluar_p1_dus'] + $row['op_keluar_p2_dus'] + $row['op_keluar_p3_dus'] + $row['overpack_keluar_dus'];
            $pengeluaran_satuan = $row['jual_satuan'] + $row['op_keluar_p1_satuan'] + $row['op_keluar_p2_satuan'] + $row['op_keluar_p3_satuan'] + $row['overpack_keluar_satuan'];

            $data['totals']['saldo_awal_dus'] += $row['saldo_awal_dus'];
            $data['totals']['saldo_awal_satuan'] += $row['saldo_awal_satuan'];
            $data['totals']['produksi_dus'] += $row['produksi_dus'];
            $data['totals']['produksi_satuan'] += $row['produksi_satuan'];
            $data['totals']['penerimaan_dus'] += $penerimaan_dus;
            $data['totals']['penerimaan_satuan'] += $penerimaan_satuan;
            $data['totals']['pengeluaran_dus'] += $pengeluaran_dus;
            $data['totals']['pengeluaran_satuan'] += $pengeluaran_satuan;
            $data['totals']['saldo_akhir_dus'] += ($row['saldo_awal_dus'] + $penerimaan_dus - $pengeluaran_dus);
            $data['totals']['saldo_akhir_satuan'] += ($row['saldo_awal_satuan'] + $penerimaan_satuan - $pengeluaran_satuan);
        }

        return $data;
    }

    public function getOverpackData($tipe_laporan, $selected_date, $start_date, $end_date, $filter_produk)
    {
        $data = [
            'report_data' => [],
            'grand_totals' => []
        ];

        $builder = $this->db->table('produk p');
        
        if ($tipe_laporan === 'harian') {
            $builder->select('p.id_produk, p.nama_produk, p.satuan_per_dus,
                             COALESCE(masuk.total_masuk, 0) AS total_masuk,
                             COALESCE(seleksi.total_aman, 0) AS total_aman,
                             COALESCE(seleksi.total_curah, 0) AS total_curah,
                             COALESCE(kemas.total_kemas, 0) AS total_kemas');
            
            // Join for masuk data
            $builder->join('(SELECT okd.produk_id, SUM(okd.total_pcs) AS total_masuk 
                            FROM operpack_kerusakan ok 
                            JOIN operpack_kerusakan_detail okd ON ok.id = okd.operpack_id 
                            WHERE DATE(ok.waktu_diterima) <= "' . $selected_date . '"
                            GROUP BY okd.produk_id) masuk', 'p.id_produk = masuk.produk_id', 'left');
            
            // Join for seleksi data
            $builder->join('(SELECT produk_id, SUM(pcs_aman) as total_aman, SUM(pcs_curah) as total_curah 
                            FROM operpack_seleksi 
                            WHERE tanggal <= "' . $selected_date . '"
                            GROUP BY produk_id) seleksi', 'p.id_produk = seleksi.produk_id', 'left');
            
            // Join for kemas data
            $builder->join('(SELECT produk_id, SUM(jumlah_kemas) as total_kemas 
                            FROM operpack_kemas_ulang 
                            WHERE tanggal <= "' . $selected_date . '"
                            GROUP BY produk_id) kemas', 'p.id_produk = kemas.produk_id', 'left');
        } else {
            $builder->select('p.id_produk, p.nama_produk, p.satuan_per_dus,
                             COALESCE(masuk.total_masuk, 0) AS total_masuk,
                             COALESCE(seleksi.total_aman, 0) AS total_aman,
                             COALESCE(seleksi.total_curah, 0) AS total_curah,
                             COALESCE(kemas.total_kemas, 0) AS total_kemas');
            
            // Join for masuk data
            $builder->join('(SELECT okd.produk_id, SUM(okd.total_pcs) AS total_masuk 
                            FROM operpack_kerusakan ok 
                            JOIN operpack_kerusakan_detail okd ON ok.id = okd.operpack_id 
                            WHERE DATE(ok.waktu_diterima) BETWEEN "' . $start_date . '" AND "' . $end_date . '"
                            GROUP BY okd.produk_id) masuk', 'p.id_produk = masuk.produk_id', 'left');
            
            // Join for seleksi data
            $builder->join('(SELECT produk_id, SUM(pcs_aman) as total_aman, SUM(pcs_curah) as total_curah 
                            FROM operpack_seleksi 
                            WHERE tanggal BETWEEN "' . $start_date . '" AND "' . $end_date . '"
                            GROUP BY produk_id) seleksi', 'p.id_produk = seleksi.produk_id', 'left');
            
            // Join for kemas data
            $builder->join('(SELECT produk_id, SUM(jumlah_kemas) as total_kemas 
                            FROM operpack_kemas_ulang 
                            WHERE tanggal BETWEEN "' . $start_date . '" AND "' . $end_date . '"
                            GROUP BY produk_id) kemas', 'p.id_produk = kemas.produk_id', 'left');
        }

        if ($filter_produk !== 'semua') {
            $builder->where('p.id_produk', $filter_produk);
        }

        $builder->orderBy('p.nama_produk');
        $raw_data = $builder->get()->getResultArray();

        foreach ($raw_data as $row) {
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
                    
                    $data['report_data'][] = $data_row;
                    
                    // Add to grand totals
                    foreach ($data_row as $key => $value) {
                        if (is_numeric($value) && $key !== 'satuan_per_dus') {
                            $data['grand_totals'][$key] = ($data['grand_totals'][$key] ?? 0) + $value;
                        }
                    }
                }
            } else {
                if ($row['total_masuk'] > 0 || $row['total_aman'] > 0 || $row['total_curah'] > 0 || $row['total_kemas'] > 0) {
                    $data_row = [
                        'nama_produk' => $row['nama_produk'],
                        'satuan_per_dus' => $satuan_per_dus,
                        'total_masuk' => $row['total_masuk'],
                        'total_aman' => $row['total_aman'],
                        'total_curah' => $row['total_curah'],
                        'total_kemas' => $row['total_kemas']
                    ];
                    
                    $data['report_data'][] = $data_row;
                    
                    // Add to grand totals
                    foreach ($data_row as $key => $value) {
                        if (is_numeric($value) && $key !== 'satuan_per_dus') {
                            $data['grand_totals'][$key] = ($data['grand_totals'][$key] ?? 0) + $value;
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function getPerbandinganStok($selected_date, $filter_gudang, $filter_produk)
    {
        $data = [
            'report_data' => [],
            'total_records' => 0,
            'records_with_difference' => 0,
            'total_selisih_dus' => 0,
            'total_selisih_satuan' => 0,
            'selected_gudang_name' => 'Semua Gudang',
            'selected_produk_name' => 'Semua Produk'
        ];

        // Get filter names
        if ($filter_gudang !== 'semua') {
            $gudang = $this->db->table('gudang')->where('id_gudang', $filter_gudang)->get()->getRowArray();
            if ($gudang) {
                $data['selected_gudang_name'] = $gudang['nama_gudang'];
            }
        }

        if ($filter_produk !== 'semua') {
            $produk = $this->db->table('produk')->where('id_produk', $filter_produk)->get()->getRowArray();
            if ($produk) {
                $data['selected_produk_name'] = $produk['nama_produk'];
            }
        }

        $builder = $this->db->table('log_perbandingan_stok l');
        $builder->join('produk p', 'l.id_produk = p.id_produk');
        $builder->join('gudang g', 'l.id_gudang = g.id_gudang');
        $builder->where('l.tanggal_cek', $selected_date);

        if ($filter_gudang === 'semua') {
            $builder->select('l.tanggal_cek, p.nama_produk, "Total Semua Gudang" as nama_gudang,
                             SUM(l.sistem_dus) as sistem_dus, SUM(l.sistem_satuan) as sistem_satuan,
                             SUM(l.fisik_dus) as fisik_dus, SUM(l.fisik_satuan) as fisik_satuan,
                             SUM(l.selisih_dus) as selisih_dus, SUM(l.selisih_satuan) as selisih_satuan');
            
            if ($filter_produk !== 'semua') {
                $builder->where('l.id_produk', $filter_produk);
            }
            
            $builder->groupBy('p.id_produk, p.nama_produk, l.tanggal_cek');
            $builder->orderBy('p.nama_produk');
        } else {
            $builder->select('l.tanggal_cek, p.nama_produk, g.nama_gudang,
                             l.sistem_dus, l.sistem_satuan, l.fisik_dus, l.fisik_satuan,
                             l.selisih_dus, l.selisih_satuan');
            $builder->where('l.id_gudang', $filter_gudang);
            
            if ($filter_produk !== 'semua') {
                $builder->where('l.id_produk', $filter_produk);
            }
            
            $builder->orderBy('p.nama_produk');
            $builder->orderBy('g.nama_gudang');
        }

        $data['report_data'] = $builder->get()->getResultArray();

        // Calculate statistics
        $data['total_records'] = count($data['report_data']);
        
        foreach ($data['report_data'] as $row) {
            if ($row['selisih_dus'] != 0 || $row['selisih_satuan'] != 0) {
                $data['records_with_difference']++;
            }
            $data['total_selisih_dus'] += abs($row['selisih_dus']);
            $data['total_selisih_satuan'] += abs($row['selisih_satuan']);
        }

        return $data;
    }

    public function getLihatStok($filter_gudang, $search_produk)
    {
        $data = [
            'report_data' => [],
            'selected_gudang_name' => 'Semua Gudang',
            'total_products' => 0,
            'total_dus' => 0,
            'total_satuan' => 0,
            'products_with_stock' => 0
        ];

        // Get warehouse name
        if ($filter_gudang !== 'semua') {
            $gudang = $this->db->table('gudang')->where('id_gudang', $filter_gudang)->get()->getRowArray();
            if ($gudang) {
                $data['selected_gudang_name'] = $gudang['nama_gudang'] . ' (' . $gudang['tipe_gudang'] . ')';
            }
        }

        $builder = $this->db->table('produk p');
        $builder->join('stok_produk s', 'p.id_produk = s.id_produk', 'left');

        if ($filter_gudang === 'semua') {
            $builder->select('p.id_produk, p.nama_produk, p.satuan_per_dus,
                             SUM(COALESCE(s.jumlah_dus, 0)) as final_dus,
                             SUM(COALESCE(s.jumlah_satuan, 0)) as final_satuan');
            
            if (!empty($search_produk)) {
                $builder->like('p.nama_produk', $search_produk);
            }
            
            $builder->groupBy('p.id_produk, p.nama_produk, p.satuan_per_dus');
        } else {
            $builder->select('p.id_produk, p.nama_produk, p.satuan_per_dus,
                             COALESCE(s.jumlah_dus, 0) as final_dus,
                             COALESCE(s.jumlah_satuan, 0) as final_satuan');
            $builder->where('s.id_gudang', $filter_gudang);
            
            if (!empty($search_produk)) {
                $builder->like('p.nama_produk', $search_produk);
            }
        }

        $builder->orderBy('p.nama_produk');
        $data['report_data'] = $builder->get()->getResultArray();

        // Calculate statistics
        $data['total_products'] = count($data['report_data']);
        $data['total_dus'] = array_sum(array_column($data['report_data'], 'final_dus'));
        $data['total_satuan'] = array_sum(array_column($data['report_data'], 'final_satuan'));
        $data['products_with_stock'] = count(array_filter($data['report_data'], function($item) {
            return $item['final_dus'] > 0 || $item['final_satuan'] > 0;
        }));

        return $data;
    }
}
