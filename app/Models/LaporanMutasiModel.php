<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanMutasiModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Mengambil data mutasi stok per produk dengan logika saldo yang benar
     */
    public function getMutasiStok(array $filters)
    {
        try {
            $tipe_laporan = $filters['tipe_laporan'] ?? 'harian';
            $tgl_laporan = $filters['tanggal'] ?? date('Y-m-d');
            $tgl_mulai = $filters['tanggal_mulai'] ?? date('Y-m-01');
            $tgl_akhir = $filters['tanggal_akhir'] ?? date('Y-m-t');
            $filter_gudang = $filters['gudang_id'] ?? 'semua';
            $filter_produk = $filters['produk_id'] ?? 'semua';

            $tanggal_hitung_awal = ($tipe_laporan === 'harian') ? $tgl_laporan : $tgl_mulai;
            $tanggal_akhir_hitung = ($tipe_laporan === 'harian') ? $tgl_laporan : $tgl_akhir;

            // Ambil daftar produk
            $builder = $this->db->table('produk');
            $builder->select('id_produk, nama_produk, satuan_per_dus as isi');
            
            if ($filter_produk !== 'semua') {
                $builder->where('id_produk', $filter_produk);
            }
            
            $builder->orderBy('nama_produk');
            $produk_list = $builder->get()->getResultArray();

            $report_data = [];
            
            foreach ($produk_list as $produk) {
                $id_produk = $produk['id_produk'];
                
                $data = [
                    'id_produk' => $id_produk,
                    'nama_produk' => $produk['nama_produk'],
                    'isi' => $produk['isi'],
                    'saldo_awal_dus' => 0,
                    'saldo_awal_satuan' => 0,
                    'produksi_dus' => 0,
                    'produksi_satuan' => 0,
                    'op_masuk_p1_dus' => 0,
                    'op_masuk_p1_satuan' => 0,
                    'op_masuk_p2_dus' => 0,
                    'op_masuk_p2_satuan' => 0,
                    'op_masuk_p3_dus' => 0,
                    'op_masuk_p3_satuan' => 0,
                    'overpack_masuk_dus' => 0,
                    'overpack_masuk_satuan' => 0,
                    'jual_dus' => 0,
                    'jual_satuan' => 0,
                    'op_keluar_p1_dus' => 0,
                    'op_keluar_p1_satuan' => 0,
                    'op_keluar_p2_dus' => 0,
                    'op_keluar_p2_satuan' => 0,
                    'op_keluar_p3_dus' => 0,
                    'op_keluar_p3_satuan' => 0,
                    'overpack_keluar_dus' => 0,
                    'overpack_keluar_satuan' => 0,
                    'error_message' => null,
                ];

                // Hitung saldo awal dengan logika yang benar
                $saldo_awal = $this->getSaldoAwalBenar($id_produk, $tanggal_hitung_awal, $filter_gudang);
                if ($saldo_awal['error']) {
                    $data['error_message'] = $saldo_awal['message'];
                } else {
                    $data['saldo_awal_dus'] = $saldo_awal['dus'];
                    $data['saldo_awal_satuan'] = $saldo_awal['satuan'];
                }

                // Hitung mutasi periode laporan
                $this->hitungProduksi($data, $id_produk, $tanggal_hitung_awal, $tanggal_akhir_hitung, $filter_gudang);
                $this->hitungOperstock($data, $id_produk, $tanggal_hitung_awal, $tanggal_akhir_hitung, $filter_gudang);
                $this->hitungPenjualan($data, $id_produk, $tanggal_hitung_awal, $tanggal_akhir_hitung, $filter_gudang);
                $this->hitungOverpack($data, $id_produk, $tanggal_hitung_awal, $tanggal_akhir_hitung, $filter_gudang);

                $report_data[$id_produk] = $data;
            }

            return $report_data;

        } catch (\Exception $e) {
            log_message('error', 'Error in getMutasiStok: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Menghitung saldo awal yang benar - mengikuti saldo akhir hari sebelumnya
     */
    private function getSaldoAwalBenar(int $id_produk, string $tanggal_hitung_awal, $filter_gudang)
    {
        try {
            $bulan_tahun_laporan = date('Y-m', strtotime($tanggal_hitung_awal));
            
            // Cari opname bulan ini
            $builder = $this->db->table('stok_awal_bulan');
            $builder->select('SUM(jumlah_dus_opname) as total_dus, SUM(jumlah_satuan_opname) as total_satuan');
            $builder->where('produk_id', $id_produk);
            $builder->where("DATE_FORMAT(tanggal_opname, '%Y-%m')", $bulan_tahun_laporan);
            
            if ($filter_gudang !== 'semua') {
                $builder->where('gudang_id', $filter_gudang);
            }
            
            $opname_data = $builder->get()->getRowArray();
            
            if (!$opname_data || ($opname_data['total_dus'] === null && $opname_data['total_satuan'] === null)) {
                $nama_bulan_tahun = date('F Y', strtotime($tanggal_hitung_awal));
                return [
                    'error' => true,
                    'message' => "Stok awal untuk bulan $nama_bulan_tahun belum diinput."
                ];
            }
            
            $saldo_awal_dus = $opname_data['total_dus'] ?? 0;
            $saldo_awal_satuan = $opname_data['total_satuan'] ?? 0;
            
            // Hitung mutasi dari awal bulan hingga H-1 tanggal laporan
            $tanggal_mulai_hitung_mutasi = date('Y-m-01', strtotime($tanggal_hitung_awal));
            $tanggal_sebelum_laporan = date('Y-m-d', strtotime($tanggal_hitung_awal . ' -1 day'));
            
            // Jika tanggal laporan bukan tanggal 1, hitung mutasi dari awal bulan hingga H-1
            if ($tanggal_hitung_awal > $tanggal_mulai_hitung_mutasi) {
                $mutasi_dus = 0;
                $mutasi_satuan = 0;
                
                // Produksi
                $builder = $this->db->table('pengemasan');
                $builder->select('SUM(jumlah_dus) as dus, SUM(jumlah_satuan) as satuan');
                $builder->where('produk_id', $id_produk);
                $builder->where('DATE(tanggal) >=', $tanggal_mulai_hitung_mutasi);
                $builder->where('DATE(tanggal) <=', $tanggal_sebelum_laporan);
                
                if ($filter_gudang !== 'semua') {
                    $builder->where('gudang_id', $filter_gudang);
                }
                
                $prod_result = $builder->get()->getRowArray();
                if ($prod_result) {
                    $mutasi_dus += $prod_result['dus'] ?? 0;
                    $mutasi_satuan += $prod_result['satuan'] ?? 0;
                }
                
                // Penjualan
                $builder = $this->db->table('penjualan p');
                $builder->select('SUM(pd.jumlah_dus) as dus, SUM(pd.jumlah_satuan) as satuan');
                $builder->join('penjualan_detail pd', 'p.id = pd.penjualan_id');
                $builder->where('pd.produk_id', $id_produk);
                $builder->where('DATE(p.tanggal) >=', $tanggal_mulai_hitung_mutasi);
                $builder->where('DATE(p.tanggal) <=', $tanggal_sebelum_laporan);
                
                if ($filter_gudang !== 'semua') {
                    $builder->where('pd.gudang_id', $filter_gudang);
                }
                
                $jual_result = $builder->get()->getRowArray();
                if ($jual_result) {
                    $mutasi_dus -= $jual_result['dus'] ?? 0;
                    $mutasi_satuan -= $jual_result['satuan'] ?? 0;
                }
                
                // Operstock antar gudang (bukan overpack)
                $builder = $this->db->table('operstock o');
                $builder->select('o.gudang_asal_id, o.gudang_tujuan_id, SUM(od.jumlah_dus_dikirim) as dus, SUM(od.jumlah_satuan_dikirim) as satuan');
                $builder->join('operstock_detail od', 'o.id = od.operstock_id');
                $builder->where('od.produk_id', $id_produk);
                $builder->where('DATE(o.waktu_kirim) >=', $tanggal_mulai_hitung_mutasi);
                $builder->where('DATE(o.waktu_kirim) <=', $tanggal_sebelum_laporan);
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
                
                // Overpack masuk
                $builder = $this->db->table('operstock o');
                $builder->select('SUM(od.jumlah_dus_dikirim) as dus, SUM(od.jumlah_satuan_dikirim) as satuan');
                $builder->join('operstock_detail od', 'o.id = od.operstock_id');
                $builder->where('od.produk_id', $id_produk);
                $builder->where('DATE(o.waktu_kirim) >=', $tanggal_mulai_hitung_mutasi);
                $builder->where('DATE(o.waktu_kirim) <=', $tanggal_sebelum_laporan);
                $builder->where('o.gudang_asal_id', 4);
                
                if ($filter_gudang !== 'semua') {
                    $builder->where('o.gudang_tujuan_id', $filter_gudang);
                }
                
                $overpack_masuk_result = $builder->get()->getRowArray();
                if ($overpack_masuk_result) {
                    $mutasi_dus += $overpack_masuk_result['dus'] ?? 0;
                    $mutasi_satuan += $overpack_masuk_result['satuan'] ?? 0;
                }
                
                // Overpack keluar
                $builder = $this->db->table('operpack_kerusakan ok');
                $builder->select('SUM(okd.jumlah_dus_kembali) as dus, SUM(okd.jumlah_satuan_kembali) as satuan');
                $builder->join('operpack_kerusakan_detail okd', 'ok.id = okd.operpack_id');
                $builder->join('gudang g', "ok.kategori_asal = 'Internal' AND ok.asal = g.nama_gudang");
                $builder->where('okd.produk_id', $id_produk);
                $builder->where('DATE(ok.waktu_diterima) >=', $tanggal_mulai_hitung_mutasi);
                $builder->where('DATE(ok.waktu_diterima) <=', $tanggal_sebelum_laporan);
                
                if ($filter_gudang !== 'semua') {
                    $builder->where('g.id_gudang', $filter_gudang);
                }
                
                $overpack_keluar_result = $builder->get()->getRowArray();
                if ($overpack_keluar_result) {
                    $mutasi_dus -= $overpack_keluar_result['dus'] ?? 0;
                    $mutasi_satuan -= $overpack_keluar_result['satuan'] ?? 0;
                }
                
                // Saldo awal = Opname + Mutasi hingga H-1
                $saldo_awal_dus += $mutasi_dus;
                $saldo_awal_satuan += $mutasi_satuan;
            }
            
            return [
                'error' => false,
                'dus' => $saldo_awal_dus,
                'satuan' => $saldo_awal_satuan
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getSaldoAwalBenar: ' . $e->getMessage());
            return ['error' => true, 'message' => 'Error menghitung saldo awal'];
        }
    }

    /**
     * Menghitung data produksi
     */
    private function hitungProduksi(array &$data, int $id_produk, string $tgl_mulai, string $tgl_akhir, $filter_gudang)
    {
        $builder = $this->db->table('pengemasan');
        $builder->select('SUM(jumlah_dus) as total_dus, SUM(jumlah_satuan) as total_satuan');
        $builder->where('produk_id', $id_produk);
        $builder->where('DATE(tanggal) >=', $tgl_mulai);
        $builder->where('DATE(tanggal) <=', $tgl_akhir);
        
        if ($filter_gudang !== 'semua') {
            $builder->where('gudang_id', $filter_gudang);
        }
        
        $result = $builder->get()->getRowArray();
        if ($result) {
            $data['produksi_dus'] = $result['total_dus'] ?? 0;
            $data['produksi_satuan'] = $result['total_satuan'] ?? 0;
        }
    }

    /**
     * Menghitung data operstock
     */
    private function hitungOperstock(array &$data, int $id_produk, string $tgl_mulai, string $tgl_akhir, $filter_gudang)
    {
        $builder = $this->db->table('operstock o');
        $builder->select('o.gudang_asal_id, o.gudang_tujuan_id, SUM(od.jumlah_dus_dikirim) as total_dus, SUM(od.jumlah_satuan_dikirim) as total_satuan');
        $builder->join('operstock_detail od', 'o.id = od.operstock_id');
        $builder->where('od.produk_id', $id_produk);
        $builder->where('DATE(o.waktu_kirim) >=', $tgl_mulai);
        $builder->where('DATE(o.waktu_kirim) <=', $tgl_akhir);
        $builder->where('o.gudang_asal_id !=', 4);
        $builder->where('o.gudang_tujuan_id !=', 4);
        $builder->groupBy('o.gudang_asal_id, o.gudang_tujuan_id');
        
        $results = $builder->get()->getResultArray();
        
        $gudang_names = [1 => 'p1', 2 => 'p2', 3 => 'p3'];
        
        foreach ($results as $row) {
            if ($filter_gudang === 'semua') {
                if (isset($gudang_names[$row['gudang_asal_id']])) {
                    $data['op_keluar_' . $gudang_names[$row['gudang_asal_id']] . '_dus'] += $row['total_dus'] ?? 0;
                    $data['op_keluar_' . $gudang_names[$row['gudang_asal_id']] . '_satuan'] += $row['total_satuan'] ?? 0;
                }
                if (isset($gudang_names[$row['gudang_tujuan_id']])) {
                    $data['op_masuk_' . $gudang_names[$row['gudang_tujuan_id']] . '_dus'] += $row['total_dus'] ?? 0;
                    $data['op_masuk_' . $gudang_names[$row['gudang_tujuan_id']] . '_satuan'] += $row['total_satuan'] ?? 0;
                }
            } else {
                if ($row['gudang_asal_id'] == $filter_gudang && isset($gudang_names[$row['gudang_tujuan_id']])) {
                    $data['op_keluar_' . $gudang_names[$row['gudang_tujuan_id']] . '_dus'] += $row['total_dus'] ?? 0;
                    $data['op_keluar_' . $gudang_names[$row['gudang_tujuan_id']] . '_satuan'] += $row['total_satuan'] ?? 0;
                }
                if ($row['gudang_tujuan_id'] == $filter_gudang && isset($gudang_names[$row['gudang_asal_id']])) {
                    $data['op_masuk_' . $gudang_names[$row['gudang_asal_id']] . '_dus'] += $row['total_dus'] ?? 0;
                    $data['op_masuk_' . $gudang_names[$row['gudang_asal_id']] . '_satuan'] += $row['total_satuan'] ?? 0;
                }
            }
        }
    }

    /**
     * Menghitung data penjualan
     */
    private function hitungPenjualan(array &$data, int $id_produk, string $tgl_mulai, string $tgl_akhir, $filter_gudang)
    {
        $builder = $this->db->table('penjualan p');
        $builder->select('SUM(pd.jumlah_dus) as total_dus, SUM(pd.jumlah_satuan) as total_satuan');
        $builder->join('penjualan_detail pd', 'p.id = pd.penjualan_id');
        $builder->where('pd.produk_id', $id_produk);
        $builder->where('DATE(p.tanggal) >=', $tgl_mulai);
        $builder->where('DATE(p.tanggal) <=', $tgl_akhir);
        
        if ($filter_gudang !== 'semua') {
            $builder->where('pd.gudang_id', $filter_gudang);
        }
        
        $result = $builder->get()->getRowArray();
        if ($result) {
            $data['jual_dus'] = $result['total_dus'] ?? 0;
            $data['jual_satuan'] = $result['total_satuan'] ?? 0;
        }
    }

    /**
     * Menghitung data overpack
     */
    private function hitungOverpack(array &$data, int $id_produk, string $tgl_mulai, string $tgl_akhir, $filter_gudang)
    {
        // Overpack masuk
        $builder = $this->db->table('operstock o');
        $builder->select('SUM(od.jumlah_dus_dikirim) as total_dus, SUM(od.jumlah_satuan_dikirim) as total_satuan');
        $builder->join('operstock_detail od', 'o.id = od.operstock_id');
        $builder->where('od.produk_id', $id_produk);
        $builder->where('DATE(o.waktu_kirim) >=', $tgl_mulai);
        $builder->where('DATE(o.waktu_kirim) <=', $tgl_akhir);
        $builder->where('o.gudang_asal_id', 4);
        
        if ($filter_gudang !== 'semua') {
            $builder->where('o.gudang_tujuan_id', $filter_gudang);
        }
        
        $result = $builder->get()->getRowArray();
        if ($result) {
            $data['overpack_masuk_dus'] = $result['total_dus'] ?? 0;
            $data['overpack_masuk_satuan'] = $result['total_satuan'] ?? 0;
        }

        // Overpack keluar
        $builder = $this->db->table('operpack_kerusakan ok');
        $builder->select('SUM(okd.jumlah_dus_kembali) as total_dus, SUM(okd.jumlah_satuan_kembali) as total_satuan');
        $builder->join('operpack_kerusakan_detail okd', 'ok.id = okd.operpack_id');
        $builder->join('gudang g', "ok.kategori_asal = 'Internal' AND ok.asal = g.nama_gudang");
        $builder->where('okd.produk_id', $id_produk);
        $builder->where('DATE(ok.waktu_diterima) >=', $tgl_mulai);
        $builder->where('DATE(ok.waktu_diterima) <=', $tgl_akhir);
        
        if ($filter_gudang !== 'semua') {
            $builder->where('g.id_gudang', $filter_gudang);
        }
        
        $result = $builder->get()->getRowArray();
        if ($result) {
            $data['overpack_keluar_dus'] = $result['total_dus'] ?? 0;
            $data['overpack_keluar_satuan'] = $result['total_satuan'] ?? 0;
        }
    }

    /**
     * Menghitung total keseluruhan
     */
    public function calculateTotals(array $report_data)
    {
        $totals = [
            'saldo_awal_dus' => 0,
            'saldo_awal_satuan' => 0,
            'produksi_dus' => 0,
            'produksi_satuan' => 0,
            'penerimaan_dus' => 0,
            'penerimaan_satuan' => 0,
            'pengeluaran_dus' => 0,
            'pengeluaran_satuan' => 0,
            'saldo_akhir_dus' => 0,
            'saldo_akhir_satuan' => 0
        ];

        foreach ($report_data as $data) {
            if ($data['error_message']) continue;

            $penerimaan_dus = $data['produksi_dus'] + $data['op_masuk_p1_dus'] + $data['op_masuk_p2_dus'] + $data['op_masuk_p3_dus'] + $data['overpack_masuk_dus'];
            $penerimaan_satuan = $data['produksi_satuan'] + $data['op_masuk_p1_satuan'] + $data['op_masuk_p2_satuan'] + $data['op_masuk_p3_satuan'] + $data['overpack_masuk_satuan'];
            $pengeluaran_dus = $data['jual_dus'] + $data['op_keluar_p1_dus'] + $data['op_keluar_p2_dus'] + $data['op_keluar_p3_dus'] + $data['overpack_keluar_dus'];
            $pengeluaran_satuan = $data['jual_satuan'] + $data['op_keluar_p1_satuan'] + $data['op_keluar_p2_satuan'] + $data['op_keluar_p3_satuan'] + $data['overpack_keluar_satuan'];

            $totals['saldo_awal_dus'] += $data['saldo_awal_dus'];
            $totals['saldo_awal_satuan'] += $data['saldo_awal_satuan'];
            $totals['produksi_dus'] += $data['produksi_dus'];
            $totals['produksi_satuan'] += $data['produksi_satuan'];
            $totals['penerimaan_dus'] += $penerimaan_dus;
            $totals['penerimaan_satuan'] += $penerimaan_satuan;
            $totals['pengeluaran_dus'] += $pengeluaran_dus;
            $totals['pengeluaran_satuan'] += $pengeluaran_satuan;
            $totals['saldo_akhir_dus'] += ($data['saldo_awal_dus'] + $penerimaan_dus - $pengeluaran_dus);
            $totals['saldo_akhir_satuan'] += ($data['saldo_awal_satuan'] + $penerimaan_satuan - $pengeluaran_satuan);
        }

        return $totals;
    }

    /**
     * Mendapatkan kolom gudang berdasarkan filter
     */
    public function getWarehouseColumns($filter_gudang)
    {
        if ($filter_gudang === 'semua') {
            return ['P1', 'P2', 'P3'];
        }

        $builder = $this->db->table('gudang');
        $builder->select('nama_gudang');
        $builder->where('id_gudang', $filter_gudang);
        $gudang = $builder->get()->getRowArray();

        if (!$gudang) return ['P1', 'P2', 'P3'];

        switch ($gudang['nama_gudang']) {
            case 'P1': return ['P2', 'P3'];
            case 'P2': return ['P1', 'P3'];
            case 'P3': return ['P1', 'P2'];
            default: return ['P1', 'P2', 'P3'];
        }
    }
}
