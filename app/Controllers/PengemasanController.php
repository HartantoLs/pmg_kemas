<?php

namespace App\Controllers;

use App\Models\PengemasanModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;

class PengemasanController extends BaseController
{
    protected $pengemasanModel;
    protected $gudangModel;
    protected $produkModel;

    /**
     * Konstruktor untuk menginisialisasi model.
     */
    public function __construct()
    {
        $this->pengemasanModel = new PengemasanModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
    }

    /**
     * Menampilkan halaman form input pengemasan.
     */
    public function index()
    {
        $data = [
            'page_title' => 'Input Hasil Pengemasan'
        ];
        return view('pengemasan/form', $data);
    }

    /**
     * Menampilkan halaman riwayat pengemasan.
     */
    public function riwayat()
    {
        $data = [
            'page_title'    => 'Riwayat Pengemasan',
            'tgl_mulai'     => $this->request->getGet('tanggal_mulai') ?? date('Y-m-01'),
            'tgl_akhir'     => $this->request->getGet('tanggal_akhir') ?? date('Y-m-t'),
            'gudang_list'   => $this->gudangModel->getGudangList(),
            'produk_list'   => $this->produkModel->getProdukList()
        ];
        return view('pengemasan/riwayat', $data);
    }

    // --- Kumpulan Metode untuk AJAX ---

    public function getGudang()
    {
        $gudang = $this->gudangModel->getGudangProduksi();
        $options = "<option value=''>-- Pilih Gudang --</option>";
        foreach ($gudang as $row) {
            $options .= "<option value='" . esc($row['id_gudang']) . "' data-nama-gudang='" . esc($row['nama_gudang']) . "'>" . esc($row['nama_gudang']) . "</option>";
        }
        return $this->response->setBody($options);
    }

    public function getJenisProduksi()
    {
        $jenis = $this->produkModel->getJenisProduksi();
        $options = "<option value=''>-- Pilih Jenis Produksi --</option>";
        foreach ($jenis as $d) {
            $options .= "<option value='{$d['nom_jenis_produksi']}'>{$d['jenis_produksi']}</option>";
        }
        return $this->response->setBody($options);
    }

    public function getMesin()
    {
        $nama_gudang = $this->request->getGet('nama_gudang') ?? '';
        $mesin = $this->produkModel->getMesinByGudang($nama_gudang);
        $options = '<option value="">-- Pilih Mesin --</option>';
        foreach ($mesin as $row) {
            $options .= "<option value='" . esc($row['kode_supcus']) . "'>" . esc($row['nama_supcus']) . "</option>";
        }
        return $this->response->setBody($options);
    }
    
    public function getInfoProduksi()
    {
        $nom_jenis = $this->request->getGet('nom_jenis_produksi') ?? 0;
        $data = $this->produkModel->getInfoProduksi($nom_jenis);
        return $this->response->setJSON($data);
    }
    
    public function simpan()
    {
        if (!$this->request->isAJAX()) return redirect()->to('/pengemasan');
        $data = $this->request->getPost();
        $result = $this->pengemasanModel->simpanPengemasan($data);
        return $this->response->setJSON($result);
    }

    public function filterRiwayat()
    {
        $data = [
            'tgl_mulai' => $this->request->getPost('tanggal_mulai'),
            'tgl_akhir' => $this->request->getPost('tanggal_akhir'),
            'gudang_id' => $this->request->getPost('gudang_id'),
            'produk_id' => $this->request->getPost('produk_id')
        ];
        // Memanggil fungsi getRiwayat di model
        $riwayatData = $this->pengemasanModel->getRiwayat($data); 
        // Mengirim data ke view partial dengan nama variabel 'report_data'
        return view('pengemasan/riwayat_ajax_table', ['report_data' => $riwayatData]);
    }

    public function getDetailRiwayat()
    {
        $id = $this->request->getPost('id');
        $result = $this->pengemasanModel->getDetailRiwayat($id);
        if ($result) return $this->response->setJSON(['success' => true, 'data' => $result]);
        return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Data tidak ditemukan.']);
    }

    public function updateRiwayat()
    {
        $data = $this->request->getPost();
        $result = $this->pengemasanModel->updatePengemasan($data);
        return $this->response->setJSON($result);
    }
    
    public function hapusRiwayat()
    {
        $id = $this->request->getPost('id');
        $result = $this->pengemasanModel->hapusPengemasan($id);
        return $this->response->setJSON($result);
    }
}