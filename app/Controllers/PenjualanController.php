<?php

namespace App\Controllers;

use App\Models\PenjualanModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;

class PenjualanController extends BaseController
{
    protected $penjualanModel;
    protected $gudangModel;
    protected $produkModel;

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $data = [
            'gudang_list' => $this->gudangModel->orderBy('nama_gudang', 'ASC')->findAll(),
            'produk_list' => $this->produkModel->orderBy('nama_produk', 'ASC')->findAll()
        ];

        return view('penjualan/form', $data);
    }

    public function getProdukInfo()
    {
        $idProduk = (int)$this->request->getGet('id_produk');
        
        $produk = $this->produkModel->find($idProduk);
        $info = $produk ? [
            'satuan_per_dus' => $produk['satuan_per_dus'],
            'nama_produk' => $produk['nama_produk']
        ] : [
            'satuan_per_dus' => 1,
            'nama_produk' => ''
        ];

        return $this->response->setJSON($info);
    }

    public function getCurrentStock()
    {
        $idGudang = (int)$this->request->getGet('id_gudang');
        $idProduk = (int)$this->request->getGet('id_produk');

        $db = \Config\Database::connect();
        $query = "SELECT jumlah_dus, jumlah_satuan FROM stok_produk WHERE id_gudang = ? AND id_produk = ?";
        $result = $db->query($query, [$idGudang, $idProduk]);
        $stok = $result->getRowArray() ?? ['jumlah_dus' => 0, 'jumlah_satuan' => 0];

        return $this->response->setJSON($stok);
    }

    public function getCustomerHistory()
    {
        $customer = $this->request->getGet('customer') ?? '';
        $history = $this->penjualanModel->getCustomerHistory($customer);
        
        return $this->response->setJSON($history);
    }

    public function save()
    {
        try {
            $data = [
                'no_surat_jalan' => $this->request->getPost('no_surat_jalan'),
                'customer' => $this->request->getPost('customer'),
                'tanggal' => $this->request->getPost('tanggal'),
                'pelat_mobil' => $this->request->getPost('pelat_mobil'),
                'items' => $this->request->getPost('items')
            ];

            if (empty($data['items'])) {
                throw new \Exception("Harap tambahkan minimal satu item produk.");
            }

            $result = $this->penjualanModel->savePenjualan($data);
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Transaksi Gagal: ' . $e->getMessage()
            ]);
        }
    }
}
