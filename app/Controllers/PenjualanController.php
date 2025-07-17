<?php

namespace App\Controllers;

use App\Models\PenjualanModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;
use App\Models\StokModel;

class PenjualanController extends BaseController
{
    protected $penjualanModel;
    protected $gudangModel;
    protected $produkModel;
    protected $stokModel;

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
        $this->stokModel = new StokModel();
    }

    public function input()
    {
        $data = [
            'page_title' => 'Form Input Penjualan',
            'gudang_list' => $this->gudangModel->getGudangList(),
            'produk_list' => $this->produkModel->getProdukList(),
        ];
        return view('penjualan/form', $data);
    }
    
    public function riwayat()
    {
        $data = [
            'page_title'    => 'Riwayat Penjualan',
            'gudang_list'   => $this->gudangModel->getGudangList(),
            'produk_list'   => $this->produkModel->getProdukList(),
            'tgl_mulai'     => $this->request->getGet('tanggal_mulai') ?? date('Y-m-01'),
            'tgl_akhir'     => $this->request->getGet('tanggal_akhir') ?? date('Y-m-t'),
        ];
        return view('penjualan/riwayat', $data);
    }

    // --- Kumpulan Metode untuk AJAX ---

    public function getProdukInfo()
    {
        $id_produk = $this->request->getGet('id_produk');
        $info = $this->produkModel->find($id_produk);
        return $this->response->setJSON($info ?? ['satuan_per_dus' => 1]);
    }
    
    public function getStokPadaTanggal()
    {
        $id_produk = (int)$this->request->getGet('id_produk');
        $id_gudang = (int)$this->request->getGet('id_gudang');
        $tanggal = $this->request->getGet('tanggal');
        if (empty($id_produk) || empty($id_gudang) || empty($tanggal)) {
            return $this->response->setJSON(['dus' => 0, 'satuan' => 0]);
        }
        $stok = $this->stokModel->getHistoricalStock($id_produk, $id_gudang, $tanggal);
        return $this->response->setJSON($stok);
    }
    
    public function simpan()
    {
        $data = $this->request->getPost();
        $result = $this->penjualanModel->simpanPenjualan($data);
        return $this->response->setJSON($result);
    }

    public function filterRiwayat()
    {
        $filters = $this->request->getPost();
        $data['report_data'] = $this->penjualanModel->getRiwayat($filters);
        return view('penjualan/riwayat_ajax_table', $data);
    }
    
    public function getDetailRiwayat()
    {
        $id = $this->request->getPost('id');
        $detail = $this->penjualanModel->getDetail($id);
        return $this->response->setJSON(['success' => true, 'data' => $detail]);
    }

    public function updateRiwayat()
    {
        $data = $this->request->getPost();
        $result = $this->penjualanModel->updatePenjualan($data);
        return $this->response->setJSON($result);
    }
    
    public function hapusRiwayat()
    {
        $id = $this->request->getPost('id');
        $result = $this->penjualanModel->hapusPenjualan($id);
        return $this->response->setJSON($result);
    }
}