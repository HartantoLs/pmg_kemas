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
            'gudang_list' => $this->gudangModel->getGudangList(),
            'produk_list' => $this->produkModel->getProdukList(),
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
            $stok_pembukuan = $this->fisikHarianModel->getCurrentStock();
            foreach ($stok_pembukuan as $stok) {
                $data['stok_pembukuan_map'][$stok['id_produk']][$stok['id_gudang']] = $stok;
            }
        }

        return view('fisik_harian/form', $data);
    }

    public function saveFisikHarian()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $tanggal_fisik = $this->request->getPost('tanggal_fisik');
        $items = $this->request->getPost('items');

        // Validasi input detail
        if (empty($tanggal_fisik)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tanggal fisik tidak boleh kosong']);
        }

        if (empty($items) || !is_array($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data items kosong atau format tidak valid']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $saved_count = 0;
            $error_details = [];

            foreach ($items as $produk_id => $gudang_data) {
                if (!is_array($gudang_data)) {
                    $error_details[] = "Data gudang untuk produk ID {$produk_id} tidak valid";
                    continue;
                }

                foreach ($gudang_data as $id_gudang => $jumlah) {
                    try {
                        $fisik_dus = intval($jumlah['dus'] ?? 0);
                        $fisik_satuan = intval($jumlah['satuan'] ?? 0);

                        // Skip jika kedua nilai 0 (opsional, sesuaikan kebutuhan)
                        // if ($fisik_dus == 0 && $fisik_satuan == 0) {
                        //     continue;
                        // }

                        // Cek apakah method getHistoricalStock ada
                        if (method_exists($this->stokModel, 'getHistoricalStock')) {
                            $stok_pembukuan = $this->stokModel->getHistoricalStock($produk_id, $id_gudang, $tanggal_fisik);
                        } else {
                            // Fallback: ambil stok saat ini
                            $stok_current = $this->stokModel->where(['id_produk' => $produk_id, 'id_gudang' => $id_gudang])->first();
                            $stok_pembukuan = [
                                'dus' => $stok_current['jumlah_dus'] ?? 0,
                                'satuan' => $stok_current['jumlah_satuan'] ?? 0
                            ];
                        }

                        $sistem_dus = $stok_pembukuan['dus'] ?? 0;
                        $sistem_satuan = $stok_pembukuan['satuan'] ?? 0;

                        $data = [
                            'tanggal_cek' => $tanggal_fisik,
                            'id_produk' => (int)$produk_id,
                            'id_gudang' => (int)$id_gudang,
                            'fisik_dus' => $fisik_dus,
                            'fisik_satuan' => $fisik_satuan,
                            'sistem_dus' => $sistem_dus,
                            'sistem_satuan' => $sistem_satuan
                        ];

                        // Validasi data sebelum simpan
                        if (!is_numeric($produk_id) || !is_numeric($id_gudang)) {
                            $error_details[] = "ID Produk ({$produk_id}) atau ID Gudang ({$id_gudang}) bukan angka";
                            continue;
                        }

                        $result = $this->fisikHarianModel->savePerbandingan($data);
                        
                        if (!$result) {
                            // Ambil error database yang detail
                            $db_error = $db->error();
                            $error_details[] = "Gagal simpan Produk ID {$produk_id}, Gudang ID {$id_gudang}: " . $db_error['message'];
                        } else {
                            $saved_count++;
                        }

                    } catch (\Exception $e) {
                        $error_details[] = "Error pada Produk ID {$produk_id}, Gudang ID {$id_gudang}: " . $e->getMessage();
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $db_error = $db->error();
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Transaksi database gagal',
                    'db_error' => $db_error['message'] ?? 'Unknown database error',
                    'error_details' => $error_details
                ]);
            }

            if (!empty($error_details)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ada error saat menyimpan beberapa data',
                    'saved_count' => $saved_count,
                    'error_details' => $error_details
                ]);
            }

            return $this->response->setJSON([
                'success' => true, 
                'message' => "Berhasil menyimpan {$saved_count} data perbandingan stok fisik!",
                'saved_count' => $saved_count
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Exception error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    public function riwayat()
    {
        $data = [
            'title' => 'Riwayat Stok Fisik Harian',
            'produk_list' => $this->produkModel->getProdukList(),
            'gudang_list' => $this->gudangModel->getGudangList()
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
