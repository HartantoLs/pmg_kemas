<?php

namespace App\Controllers;

use App\Models\OperpackKemasUlangModel;
use App\Models\ProdukModel;
use App\Models\StokModel;
use App\Models\GudangModel;

class OperpackKemasUlangController extends BaseController
{
    protected $operpackKemasUlangModel;
    protected $produkModel;
    protected $stokModel;
    protected $gudangModel;

    public function __construct()
    {
        $this->operpackKemasUlangModel = new OperpackKemasUlangModel();
        $this->produkModel = new ProdukModel();
        $this->stokModel = new StokModel();
        $this->gudangModel = new GudangModel();
    }

    public function input()
    {
        $data = [
            'title' => 'Form Input Hasil Kemas Ulang',
            'produk_list' => $this->produkModel->findAll()
        ];

        return view('operpack_kemas_ulang/form', $data);
    }

    public function riwayat()
    {
        $data = [
            'title' => 'Riwayat Kemas Ulang',
            'produk_list' => $this->produkModel->findAll()
        ];

        return view('operpack_kemas_ulang/riwayat', $data);
    }

    public function getStokRepack()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $idProduk = $this->request->getGet('id_produk');
        
        if (!$idProduk) {
            return $this->response->setJSON([
                'hasil_seleksi_aman' => 0,
                'hasil_kemas_ulang' => 0,
                'stok_aman_siap_repack_pcs' => 0,
                'satuan_per_dus' => 1,
                'max_unit' => 0,
                'sisa_pcs' => 0,
                'unit_type' => 'satuan',
                'unit_label' => 'Satuan/Pcs',
                'nama_produk' => ''
            ]);
        }

        try {
            $stokData = $this->operpackKemasUlangModel->getStokRepack($idProduk);
            return $this->response->setJSON($stokData);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'hasil_seleksi_aman' => 0,
                'hasil_kemas_ulang' => 0,
                'stok_aman_siap_repack_pcs' => 0,
                'satuan_per_dus' => 1,
                'max_unit' => 0,
                'sisa_pcs' => 0,
                'unit_type' => 'satuan',
                'unit_label' => 'Satuan/Pcs',
                'nama_produk' => ''
            ]);
        }
    }

    public function simpanRepack()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $data = [
                'id_produk' => (int)$this->request->getPost('id_produk'),
                'tanggal' => $this->request->getPost('tanggal'),
                'jumlah_kemas_unit' => (int)$this->request->getPost('jumlah_kemas_unit')
            ];

            $result = $this->operpackKemasUlangModel->simpanRepack($data);
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }
    }


    public function filterData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $tglMulai = $this->request->getPost('tanggal_mulai') ?? date('Y-m-01');
        $tglAkhir = $this->request->getPost('tanggal_akhir') ?? date('Y-m-t');
        $filterProduk = $this->request->getPost('produk_id') ?? 'semua';

        $data = [
            'report_data' => $this->operpackKemasUlangModel->getRiwayat([
                'tanggal_mulai' => $tglMulai,
                'tanggal_akhir' => $tglAkhir,
                'produk_id' => $filterProduk
            ]),
        ];

        return view('operpack_kemas_ulang/riwayat_ajax_table', $data);
    }

    public function getDetail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $id = (int)$this->request->getPost('id');

            if ($id <= 0) {
                throw new \Exception('ID tidak valid');
            }

            $data = $this->operpackKemasUlangModel->getDetailWithStok($id);

            if (!$data) {
                throw new \Exception('Data tidak ditemukan');
            }

            return $this->response->setJSON(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateKemasUlang()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $id = (int)$this->request->getPost('id');
            $newKemas = (int)$this->request->getPost('jumlah_kemas');

            if ($id <= 0 || $newKemas < 0) {
                throw new \Exception("Nilai input tidak valid");
            }

            $result = $this->operpackKemasUlangModel->updateKemasUlang($id, $newKemas);
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteKemasUlang()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $id = (int)$this->request->getPost('id');

            if ($id <= 0) {
                throw new \Exception("ID tidak valid");
            }

            $result = $this->operpackKemasUlangModel->hapusKemasUlang($id);
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }
}
