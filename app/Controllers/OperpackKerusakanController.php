<?php

namespace App\Controllers;

use App\Models\OperpackKerusakanModel;
use App\Models\ProdukModel;
use App\Models\GudangModel;
use App\Models\StokModel;
use App\Models\PenjualanModel;

class OperpackKerusakanController extends BaseController
{
    protected $operpackKerusakanModel;
    protected $produkModel;
    protected $gudangModel;
    protected $stokModel;
    protected $penjualanModel;

    public function __construct()
    {
        $this->operpackKerusakanModel = new OperpackKerusakanModel();
        $this->produkModel = new ProdukModel();
        $this->gudangModel = new GudangModel();
        $this->stokModel = new StokModel();
        $this->penjualanModel = new PenjualanModel();
    }

    /**
     * Menampilkan halaman form input kerusakan.
     */
    public function form()
    {
        $data = [
            'title' => 'Form Penerimaan Barang Rusak',
            'produk_list' => $this->produkModel->orderBy('nama_produk', 'ASC')->findAll()
        ];

        return view('operpack_kerusakan/form', $data);
    }

    /**
     * Menampilkan halaman riwayat kerusakan.
     */
    public function riwayat()
    {
        $data = [
            'title' => 'Riwayat Barang Rusak',
            'tgl_mulai' => $this->request->getGet('tanggal_mulai') ?? date('Y-m-01'),
            'tgl_akhir' => $this->request->getGet('tanggal_akhir') ?? date('Y-m-t'),
            'produk_list' => $this->produkModel->orderBy('nama_produk', 'ASC')->findAll()
        ];

        return view('operpack_kerusakan/riwayat', $data);
    }

    /**
     * AJAX: Mengambil daftar gudang internal (tipe produksi).
     */
    public function getGudangInternal()
    {
        $gudangs = $this->gudangModel->where('tipe_gudang', 'Produksi')
                                    ->orderBy('nama_gudang', 'ASC')
                                    ->findAll();

        return $this->response->setJSON($gudangs);
    }

    /**
     * AJAX: Validasi nomor surat jalan penjualan.
     */
    public function validatePenjualan()
    {
        $no_surat_jalan = $this->request->getGet('no_surat_jalan');
        
        if (empty($no_surat_jalan)) {
            return $this->response->setJSON(['exists' => false]);
        }

        $penjualan = $this->penjualanModel->getPenjualanByNoSuratJalan($no_surat_jalan);
        
        if ($penjualan) {
            return $this->response->setJSON([
                'exists' => true,
                'data' => $penjualan
            ]);
        }

        return $this->response->setJSON(['exists' => false]);
    }

    /**
     * AJAX: Mengambil data produk dari penjualan.
     */
    public function getPenjualanProduk()
    {
        $no_surat_jalan = $this->request->getGet('no_surat_jalan');
        $id_produk = (int)$this->request->getGet('id_produk');

        if (empty($no_surat_jalan) || $id_produk <= 0) {
            return $this->response->setJSON(['exists' => false]);
        }

        $produk_data = $this->penjualanModel->getProdukFromPenjualan($no_surat_jalan, $id_produk);
        
        if ($produk_data) {
            return $this->response->setJSON([
                'exists' => true,
                'data' => $produk_data
            ]);
        }

        return $this->response->setJSON(['exists' => false]);
    }

    /**
     * AJAX: Mengambil stok produk berdasarkan gudang dan tanggal (untuk Internal).
     */
    public function getStokProduk()
    {
        $id_gudang = (int)$this->request->getGet('id_gudang');
        $id_produk = (int)$this->request->getGet('id_produk');
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        if ($id_gudang <= 0 || $id_produk <= 0) {
            return $this->response->setJSON(['exists' => false]);
        }

        // Menggunakan getHistoricalStock untuk konsistensi
        $stok_historis = $this->stokModel->getHistoricalStock($id_produk, $id_gudang, $tanggal);
        
        if ($stok_historis['dus'] > 0 || $stok_historis['satuan'] > 0) {
            // Ambil data produk untuk satuan_per_dus
            $produk = $this->produkModel->find($id_produk);
            
            return $this->response->setJSON([
                'exists' => true,
                'jumlah_dus' => $stok_historis['dus'],
                'jumlah_satuan' => $stok_historis['satuan'],
                'satuan_per_dus' => $produk['satuan_per_dus'] ?? 1,
                'nama_produk' => $produk['nama_produk'] ?? ''
            ]);
        }

        return $this->response->setJSON(['exists' => false]);
    }

    /**
     * AJAX: Mengambil riwayat kerusakan berdasarkan kategori dan asal.
     */
    public function getDamageHistory()
    {
        $kategori_asal = $this->request->getGet('kategori_asal');
        $asal = $this->request->getGet('asal');

        if (empty($kategori_asal) || empty($asal)) {
            return $this->response->setJSON([]);
        }

        $history = $this->operpackKerusakanModel->getDamageHistory($kategori_asal, $asal);
        return $this->response->setJSON($history);
    }

    /**
     * AJAX: Menyimpan data kerusakan.
     */
    public function simpan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $data = $this->request->getPost();
        $result = $this->operpackKerusakanModel->simpanKerusakan($data);
        
        return $this->response->setJSON($result);
    }

    /**
     * AJAX: Filter riwayat kerusakan.
     */
    public function filterRiwayat()
    {
        $filters = [
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?? date('Y-m-01'),
            'tanggal_akhir' => $this->request->getPost('tanggal_akhir') ?? date('Y-m-t'),
            'produk_id' => $this->request->getPost('produk_id') ?? 'semua',
            'kategori_asal' => $this->request->getPost('kategori_asal') ?? 'semua'
        ];

        $report_data = $this->operpackKerusakanModel->getRiwayat($filters);
        
        return view('operpack_kerusakan/riwayat_ajax_table', ['report_data' => $report_data]);
    }

    /**
     * AJAX: Mengambil detail riwayat untuk edit.
     */
    public function getDetailRiwayat()
    {
        $id = (int)$this->request->getPost('id');
        
        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID tidak valid']);
        }

        $data = $this->operpackKerusakanModel->getDetailRiwayat($id);
        
        if (!$data) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        // Jika kategori eksternal, ambil data penjualan
        if ($data['kategori_asal'] === 'Eksternal') {
            $penjualan_data = $this->penjualanModel->getProdukFromPenjualan($data['asal'], $data['produk_id']);
            $data['penjualan_data'] = $penjualan_data;
        }

        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * AJAX: Mengambil stok untuk validasi edit (Internal).
     */
    public function getStock()
    {
        $produk_id = (int)$this->request->getPost('produk_id');
        $gudang_id = (int)$this->request->getPost('gudang_id');
        $tanggal = $this->request->getPost('tanggal') ?? date('Y-m-d');

        if ($produk_id <= 0 || $gudang_id <= 0) {
            return $this->response->setJSON(['dus' => 0, 'satuan' => 0]);
        }

        $stok_historis = $this->stokModel->getHistoricalStock($produk_id, $gudang_id, $tanggal);
        
        return $this->response->setJSON([
            'dus' => $stok_historis['dus'],
            'satuan' => $stok_historis['satuan']
        ]);
    }

    /**
     * AJAX: Update riwayat kerusakan.
     */
    public function updateRiwayat()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $data = $this->request->getPost();
        $result = $this->operpackKerusakanModel->updateKerusakan($data);
        
        return $this->response->setJSON($result);
    }

    /**
     * AJAX: Hapus riwayat kerusakan.
     */
    public function hapusRiwayat()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $id = (int)$this->request->getPost('id');
        
        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID tidak valid']);
        }

        $result = $this->operpackKerusakanModel->hapusKerusakan($id);
        
        return $this->response->setJSON($result);
    }
}
