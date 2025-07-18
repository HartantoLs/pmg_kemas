<?php

namespace App\Controllers;

use App\Models\OperstockModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;
use App\Models\StokModel;

class OperstockController extends BaseController
{
    protected $operstockModel;
    protected $gudangModel;
    protected $produkModel;
    protected $stokModel;

    public function __construct()
    {
        $this->operstockModel = new OperstockModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
        $this->stokModel = new StokModel();
    }

    public function input()
    {
        $data = [
            'page_title' => 'Form Pindah Stok',
            'gudang_list' => $this->gudangModel->getGudangList(),
            'produk_list' => $this->produkModel->getProdukList(),
        ];
        return view('operstock/form', $data);
    }
        
    public function riwayat()
    {
        $data = [
            'page_title' => 'Riwayat Pindah Stok',
            'gudang_list' => $this->gudangModel->getGudangList(),
            'produk_list' => $this->produkModel->getProdukList(),
            'tgl_mulai'   => $this->request->getGet('tanggal_mulai') ?? date('Y-m-01'),
            'tgl_akhir'   => $this->request->getGet('tanggal_akhir') ?? date('Y-m-t'),
            'report_data' => [], // Initial empty, akan diload via AJAX
        ];
        return view('operstock/riwayat', $data);
    }

    // AJAX Methods
    public function getBothStocks()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_produk = (int)$this->request->getGet('id_produk');
        $id_gudang_asal = (int)$this->request->getGet('id_gudang_asal');
        $id_gudang_tujuan = (int)$this->request->getGet('id_gudang_tujuan');
        $tanggal = $this->request->getGet('tanggal');

        if(empty($id_produk) || empty($id_gudang_asal) || empty($id_gudang_tujuan) || empty($tanggal)) {
            return $this->response->setJSON([
                'asal' => ['dus' => 0, 'satuan' => 0], 
                'tujuan' => ['dus' => 0, 'satuan' => 0]
            ]);
        }

        $response = [
            'asal' => $this->stokModel->getHistoricalStock($id_produk, $id_gudang_asal, $tanggal),
            'tujuan' => $this->stokModel->getHistoricalStock($id_produk, $id_gudang_tujuan, $tanggal)
        ];

        // Get satuan_per_dus info
        $produk_info = $this->produkModel->getProdukInfo($id_produk);
        $response['satuan_per_dus'] = $produk_info['satuan_per_dus'] ?? 1;

        return $this->response->setJSON($response);
    }
        
    public function simpan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $data = $this->request->getPost();
        $result = $this->operstockModel->simpanOperstock($data);
        return $this->response->setJSON($result);
    }

    public function filterRiwayat()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $filters = [
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?? date('Y-m-01'),
            'tanggal_akhir' => $this->request->getPost('tanggal_akhir') ?? date('Y-m-t'),
            'gudang_id' => $this->request->getPost('gudang_id') ?? 'semua',
            'produk_id' => $this->request->getPost('produk_id') ?? 'semua',
        ];

        $data['report_data'] = $this->operstockModel->getRiwayat($filters);
        return view('operstock/riwayat_ajax_table', $data);
    }

    public function getDetailRiwayat()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id = (int)$this->request->getPost('id');
        $detail = $this->operstockModel->getDetailRiwayat($id);
        
        if ($detail) {
            return $this->response->setJSON(['success' => true, 'data' => $detail]);
        }
        return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Data tidak ditemukan.']);
    }

    public function updateRiwayat()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $data = $this->request->getPost();
        $result = $this->operstockModel->updateOperstock($data);
        return $this->response->setJSON($result);
    }
        
    public function hapusRiwayat()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id = (int)$this->request->getPost('id');
        $result = $this->operstockModel->hapusOperstock($id);
        return $this->response->setJSON($result);
    }

    public function getTransferHistory()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_gudang_asal = (int)$this->request->getGet('id_gudang_asal');
        $id_gudang_tujuan = (int)$this->request->getGet('id_gudang_tujuan');
                
        $history = $this->operstockModel->getTransferHistory($id_gudang_asal, $id_gudang_tujuan);
        return $this->response->setJSON($history);
    }

    public function getStock()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $produk_id = (int)$this->request->getPost('produk_id');
        $gudang_id = (int)$this->request->getPost('gudang_id');
        $tanggal = $this->request->getPost('tanggal') ?? date('Y-m-d');
        
        $stok = $this->stokModel->getHistoricalStock($produk_id, $gudang_id, $tanggal);
        return $this->response->setJSON($stok);
    }
}
