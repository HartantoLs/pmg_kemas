<?php

namespace App\Controllers;

use App\Models\FisikHarianModel;
use App\Models\StokModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;
use CodeIgniter\Controller;

class FisikHarianController extends Controller
{
    protected $fisikHarianModel;
    protected $stokModel;
    protected $gudangModel;
    protected $produkModel;

    public function __construct()
    {
        $this->fisikHarianModel = new FisikHarianModel();
        $this->stokModel = new StokModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
    }

    public function form()
    {
        $selected_tanggal = $this->request->getGet('tanggal_fisik');
        
        $data = [
            'title' => 'Input Stok Fisik Harian',
            'selected_tanggal' => $selected_tanggal,
            'gudang_list' => $this->gudangModel->findAll(),
            'produk_list' => $this->produkModel->orderBy('nama_produk', 'ASC')->findAll(),
            'existing_data' => [],
            'stok_pembukuan_map' => []
        ];

        if ($selected_tanggal) {
            // Ambil data existing dari log_perbandingan_stok
            $existing = $this->fisikHarianModel->getDataByDate($selected_tanggal);
            foreach ($existing as $row) {
                $data['existing_data'][$row['id_produk']][$row['id_gudang']] = [
                    'dus' => $row['fisik_dus'],
                    'satuan' => $row['fisik_satuan']
                ];
            }

            // Ambil data stok pembukuan untuk tombol import
            $stok_pembukuan = $this->stokModel->findAll();
            foreach ($stok_pembukuan as $stok) {
                $data['stok_pembukuan_map'][$stok['id_produk']][$stok['id_gudang']] = $stok;
            }
        }

        return view('fisik_harian/form', $data);
    }

    public function saveFisikHarian()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $tanggal_fisik = $this->request->getPost('tanggal_fisik');
        $items = $this->request->getPost('items');

        if (empty($tanggal_fisik) || empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tanggal dan item tidak boleh kosong.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($items as $produk_id => $gudang_data) {
                foreach ($gudang_data as $id_gudang => $jumlah) {
                    $fisik_dus = intval($jumlah['dus'] ?? 0);
                    $fisik_satuan = intval($jumlah['satuan'] ?? 0);

                    // Ambil stok pembukuan saat ini untuk snapshot
                    $stok_pembukuan = $this->stokModel->where([
                        'id_produk' => $produk_id,
                        'id_gudang' => $id_gudang
                    ])->first();

                    $sistem_dus = $stok_pembukuan['jumlah_dus'] ?? 0;
                    $sistem_satuan = $stok_pembukuan['jumlah_satuan'] ?? 0;

                    // Simpan ke log_perbandingan_stok
                    $data = [
                        'tanggal_cek' => $tanggal_fisik,
                        'id_produk' => $produk_id,
                        'id_gudang' => $id_gudang,
                        'fisik_dus' => $fisik_dus,
                        'fisik_satuan' => $fisik_satuan,
                        'sistem_dus' => $sistem_dus,
                        'sistem_satuan' => $sistem_satuan
                    ];

                    $this->fisikHarianModel->savePerbandingan($data);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Data perbandingan stok fisik berhasil disimpan!']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function riwayat()
    {
        $data = [
            'title' => 'Riwayat Stok Fisik Harian'
        ];

        return view('fisik_harian/riwayat', $data);
    }

    public function filterData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $tanggal_dari = $this->request->getPost('tanggal_dari');
        $tanggal_sampai = $this->request->getPost('tanggal_sampai');
        $produk_id = $this->request->getPost('produk_id');
        $gudang_id = $this->request->getPost('gudang_id');

        $data = $this->fisikHarianModel->getFilteredData($tanggal_dari, $tanggal_sampai, $produk_id, $gudang_id);

        return view('fisik_harian/riwayat_ajax_table', ['data' => $data]);
    }
}
