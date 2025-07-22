<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanKartuStokModel extends Model
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Menghitung saldo awal produk berdasarkan opname dan mutasi
     */
    public function getSaldoAwal(int $produk_id, string $tgl_mulai, $gudang_id = null)
    {
        try {
            // Cari opname terakhir sebelum tanggal mulai
            $builder = $this->db->table('stok_awal_bulan');
            $builder->select('tanggal_opname, SUM(jumlah_dus_opname) as total_dus, SUM(jumlah_satuan_opname) as total_satuan');
            $builder->where('produk_id', $produk_id);
            $builder->where('tanggal_opname <', $tgl_mulai);
            
            if ($gudang_id && $gudang_id !== 'semua') {
                $builder->where('gudang_id', $gudang_id);
            }
            
            $builder->groupBy('tanggal_opname');
            $builder->orderBy('tanggal_opname', 'DESC');
            $builder->limit(1);
            
            $opname = $builder->get()->getRowArray();
            
            $saldo_dasar_dus = 0;
            $saldo_dasar_satuan = 0;
            $tgl_mulai_hitung = '1970-01-01';
            
            if ($opname) {
                $saldo_dasar_dus = (int)$opname['total_dus'];
                $saldo_dasar_satuan = (int)$opname['total_satuan'];
                $tgl_mulai_hitung = $opname['tanggal_opname'];
            }
            
            // Hitung mutasi dari tanggal opname hingga H-1 laporan
            $builder = $this->db->table('v_semua_transaksi');
            $builder->select('SUM(perubahan_dus) as total_dus, SUM(perubahan_satuan) as total_satuan');
            $builder->where('produk_id', $produk_id);
            $builder->where('tanggal_transaksi >=', $tgl_mulai_hitung);
            $builder->where('tanggal_transaksi <', $tgl_mulai);
            
            if ($gudang_id && $gudang_id !== 'semua') {
                $builder->where('gudang_id', $gudang_id);
            }
            
            $mutasi = $builder->get()->getRowArray();
            
            $saldo_awal_dus = $saldo_dasar_dus + (int)($mutasi['total_dus'] ?? 0);
            $saldo_awal_satuan = $saldo_dasar_satuan + (int)($mutasi['total_satuan'] ?? 0);
            
            return [
                'dus' => $saldo_awal_dus,
                'satuan' => $saldo_awal_satuan
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getSaldoAwal: ' . $e->getMessage());
            return ['dus' => 0, 'satuan' => 0];
        }
    }

    /**
     * Mengambil detail transaksi untuk kartu stok
     */
    public function getDetailTransaksi(int $produk_id, string $tgl_mulai, string $tgl_akhir, $gudang_id = null)
    {
        try {
            $builder = $this->db->table('v_semua_transaksi');
            $builder->select('*');
            $builder->where('produk_id', $produk_id);
            $builder->where('tanggal_transaksi >=', $tgl_mulai);
            $builder->where('tanggal_transaksi <=', $tgl_akhir);
            
            if ($gudang_id && $gudang_id !== 'semua') {
                $builder->where('gudang_id', $gudang_id);
            }
            
            $builder->orderBy('tanggal_transaksi', 'ASC');
            $builder->orderBy('tipe_transaksi', 'ASC');
            
            return $builder->get()->getResultArray();
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getDetailTransaksi: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Export data ke CSV
     */
    public function exportToCSV(array $data, array $filters)
    {
        $csv_data = [];
        $csv_data[] = ['Laporan Kartu Stok - ' . $filters['nama_produk']];
        $csv_data[] = ['Periode: ' . $filters['periode']];
        $csv_data[] = ['Gudang: ' . $filters['nama_gudang']];
        $csv_data[] = []; // Empty row
        
        // Header
        $csv_data[] = [
            'Tanggal', 'ID Gudang', 'Keterangan', 
            'Masuk Dus', 'Masuk Pcs', 
            'Keluar Dus', 'Keluar Pcs', 
            'Saldo Dus', 'Saldo Pcs'
        ];
        
        // Saldo awal
        $csv_data[] = [
            'SALDO AWAL', '', '', '', '', '', '', 
            $filters['saldo_awal_dus'], $filters['saldo_awal_satuan']
        ];
        
        // Data transaksi
        $running_dus = $filters['saldo_awal_dus'];
        $running_satuan = $filters['saldo_awal_satuan'];
        
        foreach ($data as $row) {
            $masuk_dus = ($row['perubahan_dus'] > 0) ? $row['perubahan_dus'] : 0;
            $masuk_satuan = ($row['perubahan_satuan'] > 0) ? $row['perubahan_satuan'] : 0;
            $keluar_dus = ($row['perubahan_dus'] < 0) ? abs($row['perubahan_dus']) : 0;
            $keluar_satuan = ($row['perubahan_satuan'] < 0) ? abs($row['perubahan_satuan']) : 0;
            
            $running_dus += $row['perubahan_dus'];
            $running_satuan += $row['perubahan_satuan'];
            
            $csv_data[] = [
                date('d-m-Y', strtotime($row['tanggal_transaksi'])),
                $row['gudang_id'],
                $row['tipe_transaksi'],
                $masuk_dus > 0 ? $masuk_dus : '',
                $masuk_satuan > 0 ? $masuk_satuan : '',
                $keluar_dus > 0 ? $keluar_dus : '',
                $keluar_satuan > 0 ? $keluar_satuan : '',
                $running_dus,
                $running_satuan
            ];
        }
        
        // Saldo akhir
        $csv_data[] = [
            'SALDO AKHIR', '', '', '', '', '', '', 
            $running_dus, $running_satuan
        ];
        
        return $csv_data;
    }
}
