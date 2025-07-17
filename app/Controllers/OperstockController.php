<?php

namespace App\Controllers;

use App\Models\OperstockModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;

class OperstockController extends BaseController
{
    protected $operstockModel;
    protected $gudangModel;
    protected $produkModel;

    public function __construct()
    {
        $this->operstockModel = new OperstockModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $data = [
            'gudang_list' => $this->gudangModel->orderBy('nama_gudang', 'ASC')->findAll(),
            'produk_list' => $this->produkModel->orderBy('nama_produk', 'ASC')->findAll()
        ];

        return view('operstock/form', $data);
    }

    public function getBothStocks()
    {
        $idGudangAsal = (int)$this->request->getGet('id_gudang_asal');
        $idGudangTujuan = (int)$this->request->getGet('id_gudang_tujuan');
        $idProduk = (int)$this->request->getGet('id_produk');

        $response = [
            'asal' => ['jumlah_dus' => 0, 'jumlah_satuan' => 0],
            'tujuan' => ['jumlah_dus' => 0, 'jumlah_satuan' => 0]
        ];

        $db = \Config\Database::connect();

        // Get source warehouse stock
        $queryAsal = "SELECT jumlah_dus, jumlah_satuan FROM stok_produk WHERE id_gudang = ? AND id_produk = ?";
        $resultAsal = $db->query($queryAsal, [$idGudangAsal, $idProduk]);
        $stokAsal = $resultAsal->getRowArray();
        if ($stokAsal) {
            $response['asal'] = $stokAsal;
        }

        // Get destination warehouse stock
        $queryTujuan = "SELECT jumlah_dus, jumlah_satuan FROM stok_produk WHERE id_gudang = ? AND id_produk = ?";
        $resultTujuan = $db->query($queryTujuan, [$idGudangTujuan, $idProduk]);
        $stokTujuan = $resultTujuan->getRowArray();
        if ($stokTujuan) {
            $response['tujuan'] = $stokTujuan;
        }

        return $this->response->setJSON($response);
    }

    public function getTransferHistory()
    {
        $idGudangAsal = (int)$this->request->getGet('id_gudang_asal');
        $idGudangTujuan = (int)$this->request->getGet('id_gudang_tujuan');

        $history = $this->operstockModel->getTransferHistory($idGudangAsal, $idGudangTujuan);
        return $this->response->setJSON($history);
    }

    public function save()
    {
        try {
            $data = [
                'no_surat_jalan' => $this->request->getPost('no_surat_jalan'),
                'gudang_asal' => $this->request->getPost('gudang_asal'),
                'gudang_tujuan' => $this->request->getPost('gudang_tujuan'),
                'tanggal' => $this->request->getPost('tanggal'),
                'items' => $this->request->getPost('items')
            ];

            $result = $this->operstockModel->saveOperstock($data);
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Transaksi Gagal: ' . $e->getMessage()
            ]);
        }
    }
}
