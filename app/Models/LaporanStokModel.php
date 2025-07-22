<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanStokModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Mengambil data stok produk berdasarkan filter dan tanggal
     */
    public function getStokProduk(array $filters)
    {
        try {
            $tanggal_laporan = $filters['tanggal_laporan'] ?? date('Y-m-d');
            $filter_gudang = $filters['id_gudang'] ?? 'semua';
            $search_produk = $filters['search'] ?? '';

            // Jika tanggal adalah hari ini, gunakan stok real-time
            if ($tanggal_laporan === date('Y-m-d')) {
                return $this->getCurrentStok($filter_gudang, $search_produk);
            } else {
                // Jika tanggal masa lalu, hitung stok historis
                return $this->getHistoricalData($tanggal_laporan, $filter_gudang, $search_produk);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error in getStokProduk: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Mengambil stok saat ini (real-time)
     */
    private function getCurrentStok($filter_gudang, $search_produk)
    {
        if ($filter_gudang === 'semua') {
            $builder = $this->db->table('produk p');
            $builder->select('p.id_produk, p.nama_produk, p.satuan_per_dus, SUM(COALESCE(s.jumlah_dus, 0)) as final_dus, SUM(COALESCE(s.jumlah_satuan, 0)) as final_satuan');
            $builder->join('stok_produk s', 'p.id_produk = s.id_produk', 'left');
            
            if (!empty($search_produk)) {
                $builder->like('p.nama_produk', $search_produk);
            }
            
            $builder->groupBy('p.id_produk, p.nama_produk, p.satuan_per_dus');
            $builder->orderBy('p.nama_produk', 'ASC');
        } else {
            $builder = $this->db->table('produk p');
            $builder->select('p.id_produk, p.nama_produk, p.satuan_per_dus, COALESCE(s.jumlah_dus, 0) as final_dus, COALESCE(s.jumlah_satuan, 0) as final_satuan');
            $builder->join('stok_produk s', 'p.id_produk = s.id_produk AND s.id_gudang = ' . (int)$filter_gudang, 'left');
            
            if (!empty($search_produk)) {
                $builder->like('p.nama_produk', $search_produk);
            }
            
            $builder->orderBy('p.nama_produk', 'ASC');
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mengambil data stok historis berdasarkan tanggal
     */
    public function getHistoricalData($tanggal_laporan, $filter_gudang, $search_produk)
    {
        try {
            // Ambil daftar produk
            $builder = $this->db->table('produk');
            $builder->select('id_produk, nama_produk, satuan_per_dus');
            
            if (!empty($search_produk)) {
                $builder->like('nama_produk', $search_produk);
            }
            
            $builder->orderBy('nama_produk');
            $produk_list = $builder->get()->getResultArray();

            $result = [];
            
            foreach ($produk_list as $produk) {
                $id_produk = $produk['id_produk'];
                
                // Hitung stok historis untuk produk ini
                $stok_historis = $this->calculateHistoricalStock($id_produk, $tanggal_laporan, $filter_gudang);
                
                $result[] = [
                    'id_produk' => $id_produk,
                    'nama_produk' => $produk['nama_produk'],
                    'satuan_per_dus' => $produk['satuan_per_dus'],
                    'final_dus' => $stok_historis['dus'],
                    'final_satuan' => $stok_historis['satuan']
                ];
            }

            return $result;

        } catch (\Exception $e) {
            log_message('error', 'Error in getHistoricalData: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Menghitung stok historis untuk produk tertentu pada tanggal tertentu
     */
    private function calculateHistoricalStock($id_produk, $tanggal_laporan, $filter_gudang)
    {
        try {
            $bulan_tahun_laporan = date('Y-m', strtotime($tanggal_laporan));
            
            // 1. Ambil saldo awal bulan dari opname
            $builder = $this->db->table('stok_awal_bulan');
            $builder->select('SUM(jumlah_dus_opname) as total_dus, SUM(jumlah_satuan_opname) as total_satuan');
            $builder->where('produk_id', $id_produk);
            $builder->where("DATE_FORMAT(tanggal_opname, '%Y-%m')", $bulan_tahun_laporan);
            
            if ($filter_gudang !== 'semua') {
                $builder->where('gudang_id', $filter_gudang);
            }
            
            $opname_data = $builder->get()->getRowArray();
            
            $saldo_awal_dus = $opname_data['total_dus'] ?? 0;
            $saldo_awal_satuan = $opname_data['total_satuan'] ?? 0;
            
            // 2. Hitung mutasi dari awal bulan hingga tanggal laporan
            $tanggal_mulai_bulan = date('Y-m-01', strtotime($tanggal_laporan));
            
            $mutasi_dus = 0;
            $mutasi_satuan = 0;
            
            // Produksi (+)
            $builder = $this->db->table('pengemasan');
            $builder->select('SUM(jumlah_dus) as dus, SUM(jumlah_satuan) as satuan');
            $builder->where('produk_id', $id_produk);
            $builder->where('DATE(tanggal) >=', $tanggal_mulai_bulan);
            $builder->where('DATE(tanggal) <=', $tanggal_laporan);
            
            if ($filter_gudang !== 'semua') {
                $builder->where('gudang_id', $filter_gudang);
            }
            
            $prod_result = $builder->get()->getRowArray();
            if ($prod_result) {
                $mutasi_dus += $prod_result['dus'] ?? 0;
                $mutasi_satuan += $prod_result['satuan'] ?? 0;
            }
            
            // Penjualan (-)
            $builder = $this->db->table('penjualan p');
            $builder->select('SUM(pd.jumlah_dus) as dus, SUM(pd.jumlah_satuan) as satuan');
            $builder->join('penjualan_detail pd', 'p.id = pd.penjualan_id');
            $builder->where('pd.produk_id', $id_produk);
            $builder->where('DATE(p.tanggal) >=', $tanggal_mulai_bulan);
            $builder->where('DATE(p.tanggal) <=', $tanggal_laporan);
            
            if ($filter_gudang !== 'semua') {
                $builder->where('pd.gudang_id', $filter_gudang);
            }
            
            $jual_result = $builder->get()->getRowArray();
            if ($jual_result) {
                $mutasi_dus -= $jual_result['dus'] ?? 0;
                $mutasi_satuan -= $jual_result['satuan'] ?? 0;
            }
            
            // Operstock antar gudang
            $builder = $this->db->table('operstock o');
            $builder->select('o.gudang_asal_id, o.gudang_tujuan_id, SUM(od.jumlah_dus_dikirim) as dus, SUM(od.jumlah_satuan_dikirim) as satuan');
            $builder->join('operstock_detail od', 'o.id = od.operstock_id');
            $builder->where('od.produk_id', $id_produk);
            $builder->where('DATE(o.waktu_kirim) >=', $tanggal_mulai_bulan);
            $builder->where('DATE(o.waktu_kirim) <=', $tanggal_laporan);
            $builder->where('o.gudang_asal_id !=', 4);
            $builder->where('o.gudang_tujuan_id !=', 4);
            
            if ($filter_gudang !== 'semua') {
                $builder->groupStart();
                $builder->where('o.gudang_asal_id', $filter_gudang);
                $builder->orWhere('o.gudang_tujuan_id', $filter_gudang);
                $builder->groupEnd();
            }
            
            $builder->groupBy('o.gudang_asal_id, o.gudang_tujuan_id');
            $operstock_results = $builder->get()->getResultArray();
            
            foreach ($operstock_results as $row) {
                if ($filter_gudang === 'semua') {
                    // Untuk semua gudang, tidak ada perubahan karena internal transfer
                    continue;
                } else {
                    if ($row['gudang_asal_id'] == $filter_gudang) {
                        $mutasi_dus -= $row['dus'] ?? 0;
                        $mutasi_satuan -= $row['satuan'] ?? 0;
                    }
                    if ($row['gudang_tujuan_id'] == $filter_gudang) {
                        $mutasi_dus += $row['dus'] ?? 0;
                        $mutasi_satuan += $row['satuan'] ?? 0;
                    }
                }
            }
            
            // Overpack masuk (+)
            $builder = $this->db->table('operstock o');
            $builder->select('SUM(od.jumlah_dus_dikirim) as dus, SUM(od.jumlah_satuan_dikirim) as satuan');
            $builder->join('operstock_detail od', 'o.id = od.operstock_id');
            $builder->where('od.produk_id', $id_produk);
            $builder->where('DATE(o.waktu_kirim) >=', $tanggal_mulai_bulan);
            $builder->where('DATE(o.waktu_kirim) <=', $tanggal_laporan);
            $builder->where('o.gudang_asal_id', 4);
            
            if ($filter_gudang !== 'semua') {
                $builder->where('o.gudang_tujuan_id', $filter_gudang);
            }
            
            $overpack_masuk_result = $builder->get()->getRowArray();
            if ($overpack_masuk_result) {
                $mutasi_dus += $overpack_masuk_result['dus'] ?? 0;
                $mutasi_satuan += $overpack_masuk_result['satuan'] ?? 0;
            }
            
            // Overpack keluar (-)
            $builder = $this->db->table('operpack_kerusakan ok');
            $builder->select('SUM(okd.jumlah_dus_kembali) as dus, SUM(okd.jumlah_satuan_kembali) as satuan');
            $builder->join('operpack_kerusakan_detail okd', 'ok.id = okd.operpack_id');
            $builder->join('gudang g', "ok.kategori_asal = 'Internal' AND ok.asal = g.nama_gudang");
            $builder->where('okd.produk_id', $id_produk);
            $builder->where('DATE(ok.waktu_diterima) >=', $tanggal_mulai_bulan);
            $builder->where('DATE(ok.waktu_diterima) <=', $tanggal_laporan);
            
            if ($filter_gudang !== 'semua') {
                $builder->where('g.id_gudang', $filter_gudang);
            }
            
            $overpack_keluar_result = $builder->get()->getRowArray();
            if ($overpack_keluar_result) {
                $mutasi_dus -= $overpack_keluar_result['dus'] ?? 0;
                $mutasi_satuan -= $overpack_keluar_result['satuan'] ?? 0;
            }
            
            // 3. Hitung stok akhir = Saldo awal + Mutasi
            $final_dus = $saldo_awal_dus + $mutasi_dus;
            $final_satuan = $saldo_awal_satuan + $mutasi_satuan;
            
            return [
                'dus' => max(0, $final_dus), // Pastikan tidak negatif
                'satuan' => max(0, $final_satuan)
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error in calculateHistoricalStock: ' . $e->getMessage());
            return ['dus' => 0, 'satuan' => 0];
        }
    }

    /**
     * Menghitung statistik stok
     */
    public function calculateStatistics(array $data)
    {
        return [
            'total_products' => count($data),
            'total_dus' => array_sum(array_column($data, 'final_dus')),
            'total_satuan' => array_sum(array_column($data, 'final_satuan')),
            'products_with_stock' => count(array_filter($data, fn($item) => $item['final_dus'] > 0 || $item['final_satuan'] > 0))
        ];
    }

    /**
     * Export data ke CSV
     */
    public function exportToCSV(array $data, array $filters)
    {
        $csv_data = [];
        $csv_data[] = ['Laporan Stok Produk'];
        $csv_data[] = ['Generated: ' . date('d F Y H:i:s')];
        $csv_data[] = ['Tanggal Laporan: ' . date('d F Y', strtotime($filters['tanggal_laporan']))];
        $csv_data[] = ['Filter: ' . $filters['gudang_name']];
        $csv_data[] = []; // Empty row
        
        // Header
        $csv_data[] = [
            'No', 'Nama Produk', 'Satuan per Dus', 
            'Jumlah Dus', 'Jumlah Satuan', 'Status'
        ];
        
        // Data rows
        foreach ($data as $index => $row) {
            $total_stock = $row['final_dus'] + $row['final_satuan'];
            $status = $total_stock > 10 ? 'Tersedia' : ($total_stock > 0 ? 'Sedikit' : 'Kosong');
            
            $csv_data[] = [
                $index + 1,
                $row['nama_produk'],
                ($row['satuan_per_dus'] > 1) ? $row['satuan_per_dus'] . ' satuan/dus' : 'Satuan only',
                number_format((int)($row['final_dus'] ?? 0)),
                number_format((int)($row['final_satuan'] ?? 0)),
                $status
            ];
        }
        
        return $csv_data;
    }
}
