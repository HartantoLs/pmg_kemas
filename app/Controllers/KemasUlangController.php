<?php

namespace App\Controllers;

use App\Models\KemasUlangModel;
use App\Models\ProdukModel;

class KemasUlangController extends BaseController
{
    protected $kemasUlangModel;
    protected $produkModel;

    public function __construct()
    {
        $this->kemasUlangModel = new KemasUlangModel();
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $data = [
            'produk_list' => $this->produkModel->select('id_produk, nama_produk')
                                              ->orderBy('nama_produk', 'ASC')
                                              ->findAll()
        ];

        return view('kemas_ulang/form', $data);
    }

    public function getStokRepack()
    {
        $idProduk = (int)$this->request->getGet('id_produk');
        $stok = $this->kemasUlangModel->getStokRepack($idProduk);
        
        return $this->response->setJSON($stok);
    }

    public function save()
    {
        try {
            $data = [
                'id_produk' => $this->request->getPost('id_produk'),
                'tanggal' => $this->request->getPost('tanggal'),
                'jumlah_kemas_unit' => $this->request->getPost('jumlah_kemas_unit')
            ];

            $result = $this->kemasUlangModel->saveRepack($data);
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }
    }
}
