<?php

namespace App\Controllers;

use App\Models\GudangModel;
use App\Models\ProduksiModel;
use App\Models\MesinModel;
use App\Models\PengemasanModel;

class PengemasanController extends BaseController
{
    protected $gudangModel;
    protected $produksiModel;
    protected $mesinModel;
    protected $pengemasanModel;

    public function __construct()
    {
        $this->gudangModel = new GudangModel();
        $this->produksiModel = new ProduksiModel();
        $this->mesinModel = new MesinModel();
        $this->pengemasanModel = new PengemasanModel();
    }

    public function index()
    {
        return view('pengemasan/form');
    }

    public function getGudang()
    {
        $gudangList = $this->gudangModel->getGudangProduksi();
        $options = "<option value=''>-- Pilih Gudang --</option>";
        
        foreach ($gudangList as $gudang) {
            $options .= "<option value='" . esc($gudang['id_gudang']) . "' data-nama-gudang='" . esc($gudang['nama_gudang']) . "'>" . esc($gudang['nama_gudang']) . "</option>";
        }
        
        return $this->response->setContentType('text/html')->setBody($options);
    }

    public function getJenisProduksi()
    {
        $jenisList = $this->produksiModel->getJenisProduksi();
        $options = "<option value=''>-- Pilih Jenis Produksi --</option>";
        
        foreach ($jenisList as $jenis) {
            $options .= "<option value='{$jenis['nom_jenis_produksi']}'>{$jenis['jenis_produksi']}</option>";
        }
        
        return $this->response->setContentType('text/html')->setBody($options);
    }

    public function getMesin()
    {
        $namaGudang = $this->request->getGet('nama_gudang');
        $options = '<option value="">-- Pilih Mesin --</option>';
        
        if (!empty($namaGudang)) {
            $mesinList = $this->mesinModel->getMesinByLokasi($namaGudang);
            foreach ($mesinList as $mesin) {
                $options .= "<option value='" . esc($mesin['kode_supcus']) . "'>" . esc($mesin['nama_supcus']) . "</option>";
            }
        }
        
        return $this->response->setContentType('text/html')->setBody($options);
    }

    public function getInfoProduksi()
    {
        $nomJenisProduksi = $this->request->getGet('nom_jenis_produksi');
        $response = ['bahan_baku' => [], 'unit_label' => 'Dus'];
        
        if ($nomJenisProduksi > 0) {
            $response = $this->produksiModel->getInfoProduksi($nomJenisProduksi);
        }
        
        return $this->response->setJSON($response);
    }

    public function save()
    {
        try {
            $data = [
                'tanggal' => $this->request->getPost('tTanggal'),
                'shift' => $this->request->getPost('tShift'),
                'items' => $this->request->getPost('items')
            ];

            $result = $this->pengemasanModel->savePengemasan($data);
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }
}
