<?php

namespace App\Controllers;

use App\Models\OperpackKerusakanModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;

class OperpackKerusakanController extends BaseController
{
    protected $operpackKerusakanModel;
    protected $gudangModel;
    protected $produkModel;

    public function __construct()
    {
        $this->operpackKerusakanModel = new OperpackKerusakanModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $data = [
            'produk_list' => $this->produkModel->select('id_produk, nama_produk, satuan_per_dus')
                                              ->orderBy('nama_produk', 'ASC')
                                              ->findAll()
        ];

        return view('operpack_kerusakan/form', $data);
    }

    public function getGudangInternal()
    {
        $gudangList = $this->gudangModel->where('tipe_gudang', 'Produksi')
                                       ->orderBy('nama_gudang')
                                       ->findAll();
        
        return $this->response->setJSON($gudangList);
    }

    public function getStokProduk()
    {
        $idGudang = (int)$this->request->getGet('id_gudang');
        $idProduk = (int)$this->request->getGet('id_produk');

        $db = \Config\Database::connect();
        $query = "SELECT sp.jumlah_dus, sp.jumlah_satuan, p.satuan_per_dus, p.nama_produk
                  FROM stok_produk sp
                  JOIN produk p ON sp.id_produk = p.id_produk
                  WHERE sp.id_gudang = ? AND sp.id_produk = ?";
        
        $result = $db->query($query, [$idGudang, $idProduk]);
        $row = $result->getRowArray();

        if ($row) {
            $stokData = [
                'exists' => true,
                'jumlah_dus' => (int)$row['jumlah_dus'],
                'jumlah_satuan' => (int)$row['jumlah_satuan'],
                'satuan_per_dus' => (int)$row['satuan_per_dus'],
                'nama_produk' => $row['nama_produk']
            ];
        } else {
            $stokData = ['exists' => false];
        }

        return $this->response->setJSON($stokData);
    }

    public function getDamageHistory()
    {
        $kategoriAsal = $this->request->getGet('kategori_asal') ?? '';
        $asal = $this->request->getGet('asal') ?? '';

        $history = $this->operpackKerusakanModel->getDamageHistory($kategoriAsal, $asal);
        return $this->response->setJSON($history);
    }

    public function save()
    {
        try {
            $data = [
                'no_surat_jalan' => $this->request->getPost('no_surat_jalan'),
                'tanggal' => $this->request->getPost('tanggal'),
                'kategori_asal' => $this->request->getPost('kategori_asal'),
                'asal' => $this->request->getPost('asal'),
                'items' => $this->request->getPost('items')
            ];

            $result = $this->operpackKerusakanModel->saveKerusakan($data);
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Transaksi Gagal: ' . $e->getMessage()
            ]);
        }
    }
}
