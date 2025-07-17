<?php

namespace App\Controllers;

use App\Models\LaporanModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;

class LaporanController extends BaseController
{
    protected $laporanModel;
    protected $gudangModel;
    protected $produkModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
    }

    public function kartuStok()
    {
        $data = [
            'title' => 'Laporan Kartu Stok',
            'gudang_list' => $this->gudangModel->findAll(),
            'produk_list' => $this->produkModel->findAll(),
            'report_data' => [],
            'saldo_awal_dus' => 0,
            'saldo_awal_satuan' => 0,
            'selected_produk_name' => '',
            'selected_gudang_name' => 'Semua Gudang'
        ];

        // Get filters
        $tgl_mulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $tgl_akhir = $this->request->getGet('tanggal_akhir') ?? date('Y-m-t');
        $filter_gudang = $this->request->getGet('gudang_id') ?? 'semua';
        $filter_produk = $this->request->getGet('produk_id') ?? null;

        $data['tgl_mulai'] = $tgl_mulai;
        $data['tgl_akhir'] = $tgl_akhir;
        $data['filter_gudang'] = $filter_gudang;
        $data['filter_produk'] = $filter_produk;

        if ($filter_produk) {
            $kartuStokData = $this->laporanModel->getKartuStok($filter_produk, $filter_gudang, $tgl_mulai, $tgl_akhir);
            $data = array_merge($data, $kartuStokData);
        }

        return view('laporan/kartu_stok', $data);
    }

    public function mutasi()
    {
        $data = [
            'title' => 'Laporan Mutasi Stok',
            'gudang_list' => $this->gudangModel->where('nama_gudang !=', 'Overpack')->findAll(),
            'produk_list' => $this->produkModel->findAll()
        ];

        // Get filters
        $tipe_laporan = $this->request->getGet('tipe_laporan') ?? 'harian';
        $tgl_laporan = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $tgl_mulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $tgl_akhir = $this->request->getGet('tanggal_akhir') ?? date('Y-m-t');
        $filter_gudang = $this->request->getGet('gudang_id') ?? 'semua';
        $filter_produk = $this->request->getGet('produk_id') ?? 'semua';

        $data['tipe_laporan'] = $tipe_laporan;
        $data['tgl_laporan'] = $tgl_laporan;
        $data['tgl_mulai'] = $tgl_mulai;
        $data['tgl_akhir'] = $tgl_akhir;
        $data['filter_gudang'] = $filter_gudang;
        $data['filter_produk'] = $filter_produk;

        $mutasiData = $this->laporanModel->getMutasiStok($tipe_laporan, $tgl_laporan, $tgl_mulai, $tgl_akhir, $filter_gudang, $filter_produk);
        $data = array_merge($data, $mutasiData);

        return view('laporan/mutasi', $data);
    }

    public function overpack()
    {
        $data = [
            'title' => 'Laporan Overpack',
            'produk_list' => $this->produkModel->findAll()
        ];

        // Get filters
        $tipe_laporan = $this->request->getGet('tipe_laporan') ?? 'harian';
        $selected_date = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $start_date = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $end_date = $this->request->getGet('tanggal_akhir') ?? date('Y-m-t');
        $filter_produk = $this->request->getGet('produk_id') ?? 'semua';

        $data['tipe_laporan'] = $tipe_laporan;
        $data['selected_date'] = $selected_date;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['filter_produk'] = $filter_produk;

        $overpackData = $this->laporanModel->getOverpackData($tipe_laporan, $selected_date, $start_date, $end_date, $filter_produk);
        $data = array_merge($data, $overpackData);

        return view('laporan/overpack', $data);
    }

    public function perbandingan()
    {
        $data = [
            'title' => 'Laporan Perbandingan Stok',
            'gudang_list' => $this->gudangModel->findAll(),
            'produk_list' => $this->produkModel->findAll()
        ];

        // Get filters
        $selected_date = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $filter_gudang = $this->request->getGet('id_gudang') ?? 'semua';
        $filter_produk = $this->request->getGet('produk_id') ?? 'semua';

        $data['selected_date'] = $selected_date;
        $data['filter_gudang'] = $filter_gudang;
        $data['filter_produk'] = $filter_produk;

        $perbandinganData = $this->laporanModel->getPerbandinganStok($selected_date, $filter_gudang, $filter_produk);
        $data = array_merge($data, $perbandinganData);

        return view('laporan/perbandingan', $data);
    }

    public function lihatStok()
    {
        $data = [
            'title' => 'Lihat Stok Produk',
            'gudang_list' => $this->gudangModel->findAll()
        ];

        // Get filters
        $filter_gudang = $this->request->getGet('id_gudang') ?? 'semua';
        $search_produk = $this->request->getGet('search') ?? '';

        $data['filter_gudang'] = $filter_gudang;
        $data['search_produk'] = $search_produk;

        $stokData = $this->laporanModel->getLihatStok($filter_gudang, $search_produk);
        $data = array_merge($data, $stokData);

        return view('laporan/lihat_stok', $data);
    }
}
