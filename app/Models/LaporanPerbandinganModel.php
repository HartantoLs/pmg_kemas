<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanPerbandinganModel extends Model
{
    protected $table = 'log_perbandingan_stok';
    protected $primaryKey = 'id_log';
    
    protected $gudangModel;
    protected $produkModel;

    public function __construct()
    {
        parent::__construct();
        $this->gudangModel = new \App\Models\GudangModel();
        $this->produkModel = new \App\Models\ProdukModel();
    }

    /**
     * Get perbandingan stok data with filters
     */
    public function getPerbandinganStok($filters)
    {
        $tanggal = $filters['tanggal'] ?? date('Y-m-d');
        $id_gudang = $filters['id_gudang'] ?? 'semua';
        $produk_id = $filters['produk_id'] ?? 'semua';

        // Base query builder
        $builder = $this->db->table('log_perbandingan_stok l');
        $builder->select('
            l.tanggal_cek, 
            p.nama_produk, 
            g.nama_gudang,
            l.sistem_dus, 
            l.sistem_satuan, 
            l.fisik_dus, 
            l.fisik_satuan, 
            l.selisih_dus, 
            l.selisih_satuan
        ');
        $builder->join('produk p', 'l.id_produk = p.id_produk');
        $builder->join('gudang g', 'l.id_gudang = g.id_gudang');
        $builder->where('l.tanggal_cek', $tanggal);

        // Apply filters
        if ($id_gudang !== 'semua') {
            $builder->where('l.id_gudang', $id_gudang);
        }

        if ($produk_id !== 'semua') {
            $builder->where('l.id_produk', $produk_id);
        }

        // Special case: if gudang is 'semua', group by product
        if ($id_gudang === 'semua') {
            $builder->select('
                l.tanggal_cek, 
                p.nama_produk, 
                "Total Semua Gudang" as nama_gudang,
                SUM(l.sistem_dus) as sistem_dus, 
                SUM(l.sistem_satuan) as sistem_satuan, 
                SUM(l.fisik_dus) as fisik_dus, 
                SUM(l.fisik_satuan) as fisik_satuan, 
                SUM(l.selisih_dus) as selisih_dus, 
                SUM(l.selisih_satuan) as selisih_satuan
            ', false); // false = don't escape
            $builder->groupBy(['p.id_produk', 'p.nama_produk', 'l.tanggal_cek']);
        }

        $builder->orderBy('p.nama_produk');

        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
     * Calculate statistics from report data
     */
    public function calculateStatistics($report_data)
    {
        $stats = [
            'total_records' => count($report_data),
            'records_with_difference' => 0,
            'total_selisih_dus' => 0,
            'total_selisih_satuan' => 0
        ];

        foreach ($report_data as $row) {
            if ($row['selisih_dus'] != 0 || $row['selisih_satuan'] != 0) {
                $stats['records_with_difference']++;
            }
            $stats['total_selisih_dus'] += (int)$row['selisih_dus'];
            $stats['total_selisih_satuan'] += (int)$row['selisih_satuan'];
        }

        return $stats;
    }

    /**
     * Get filter names for display
     */
    public function getFilterNames($filters)
    {
        $result = [
            'gudang_name' => 'Semua Gudang',
            'produk_name' => 'Semua Produk'
        ];
        
        // Get gudang name
        if ($filters['id_gudang'] !== 'semua') {
            $gudang = $this->gudangModel->find($filters['id_gudang']);
            if ($gudang) {
                $result['gudang_name'] = $gudang['nama_gudang'];
            }
        }
        
        // Get produk name  
        if ($filters['produk_id'] !== 'semua') {
            $produk = $this->produkModel->find($filters['produk_id']);
            if ($produk) {
                $result['produk_name'] = $produk['nama_produk'];
            }
        }
        
        return $result;
    }

    /**
     * Export data to CSV format
     */
    public function exportToCSV($report_data, $filters)
    {
        $csv_data = [];
        
        // Header
        $csv_data[] = [
            'No',
            'Produk',
            'Gudang',
            'Sistem Dus',
            'Sistem Pcs',
            'Fisik Dus', 
            'Fisik Pcs',
            'Selisih Dus',
            'Selisih Pcs'
        ];

        // Data rows
        foreach ($report_data as $index => $row) {
            $csv_data[] = [
                $index + 1,
                $row['nama_produk'],
                $row['nama_gudang'],
                $row['sistem_dus'],
                $row['sistem_satuan'],
                $row['fisik_dus'],
                $row['fisik_satuan'],
                $row['selisih_dus'],
                $row['selisih_satuan']
            ];
        }

        return $csv_data;
    }
}