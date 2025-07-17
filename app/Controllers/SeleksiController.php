<?php

namespace App\Controllers;

use App\Models\SeleksiModel;
use App\Models\ProdukModel;

class SeleksiController extends BaseController
{
    protected $seleksiModel;
    protected $produkModel;

    public function __construct()
    {
        $this->seleksiModel = new SeleksiModel();
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $data = [
            'produk_list' => $this->produkModel->select('id_produk, nama_produk')
                                              ->orderBy('nama_produk', 'ASC')
                                              ->findAll()
        ];

        return view('seleksi/form', $data);
    }

    public function getStokSeleksi()
    {
        $idProduk = (int)$this->request->getGet('id_produk');
        $stok = $this->seleksiModel->getStokSeleksi($idProduk);
        
        return $this->response->setJSON($stok);
    }

    public function save()
    {
        try {
            $data = [
                'id_produk' => $this->request->getPost('id_produk'),
                'tanggal' => $this->request->getPost('tanggal'),
                'pcs_aman' => $this->request->getPost('pcs_aman'),
                'pcs_curah' => $this->request->getPost('pcs_curah')
            ];

            $result = $this->seleksiModel->saveSeleksi($data);
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }
    }
}
