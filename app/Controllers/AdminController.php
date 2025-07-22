<?php

namespace App\Controllers;

use App\Models\AdminModel;
use CodeIgniter\Controller;

class AdminController extends Controller
{
    protected $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    public function index()
    {
        return view('admin/admin_panel');
    }

    // Gudang Methods
    public function getGudangList()
    {
        try {
            $data = $this->adminModel->getGudangList();
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data gudang: ' . $e->getMessage()
            ]);
        }
    }

    public function saveGudang()
    {
        try {
            $data = [
                'nama_gudang' => $this->request->getPost('nama_gudang'),
                'tipe_gudang' => $this->request->getPost('tipe_gudang')
            ];

            $id = $this->request->getPost('id_gudang');
            
            if ($id) {
                $result = $this->adminModel->updateGudang($id, $data);
                $message = 'Data gudang berhasil diupdate';
            } else {
                $result = $this->adminModel->insertGudang($data);
                $message = 'Data gudang berhasil disimpan';
            }

            return $this->response->setJSON([
                'success' => $result,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data gudang: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteGudang()
    {
        try {
            $id = $this->request->getPost('id');
            $result = $this->adminModel->deleteGudang($id);
            
            return $this->response->setJSON([
                'success' => $result,
                'message' => 'Data gudang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data gudang: ' . $e->getMessage()
            ]);
        }
    }

    // Produk Methods
    public function getProdukList()
    {
        try {
            $data = $this->adminModel->getProdukList();
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data produk: ' . $e->getMessage()
            ]);
        }
    }

    public function saveProduk()
    {
        try {
            $data = [
                'nama_produk' => $this->request->getPost('nama_produk'),
                'satuan_per_dus' => $this->request->getPost('satuan_per_dus')
            ];

            $id = $this->request->getPost('id_produk');
            
            if ($id) {
                $result = $this->adminModel->updateProduk($id, $data);
                $message = 'Data produk berhasil diupdate';
            } else {
                $result = $this->adminModel->insertProduk($data);
                $message = 'Data produk berhasil disimpan';
            }

            return $this->response->setJSON([
                'success' => $result,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data produk: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteProduk()
    {
        try {
            $id = $this->request->getPost('id');
            $result = $this->adminModel->deleteProduk($id);
            
            return $this->response->setJSON([
                'success' => $result,
                'message' => 'Data produk berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data produk: ' . $e->getMessage()
            ]);
        }
    }

    // Jenis Produksi Methods
    public function getJenisProduksiList()
    {
        try {
            $data = $this->adminModel->getJenisProduksiList();
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data jenis produksi: ' . $e->getMessage()
            ]);
        }
    }

    public function getJenisProduksiDetail()
    {
        try {
            $id = $this->request->getGet('id');
            $jenisProduksi = $this->adminModel->getJenisProduksiById($id);
            $bahanBaku = $this->adminModel->getBahanBakuByJenisProduksi($id);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'jenis_produksi' => $jenisProduksi,
                    'bahan_baku' => $bahanBaku
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil detail jenis produksi: ' . $e->getMessage()
            ]);
        }
    }

    public function saveJenisProduksi()
    {
        try {
            // Data untuk tbl_jenis_produksi
            $jenisProduksiData = [
                'jenis_produksi' => $this->request->getPost('jenis_produksi'),
                'group_jenis_produksi' => $this->request->getPost('group_jenis_produksi'),
                'keterangan' => $this->request->getPost('keterangan')
            ];

            // Data bahan baku
            $bahanBaku = json_decode($this->request->getPost('bahan_baku'), true) ?: [];
            
            $id = $this->request->getPost('nom_jenis_produksi');
            $isEdit = $this->request->getPost('is_edit') == '1';
            
            if ($id && $isEdit) {
                // Update - tidak perlu produk data karena tidak diubah
                $result = $this->adminModel->updateJenisProduksi($id, $jenisProduksiData, $bahanBaku);
                $message = 'Data jenis produksi berhasil diupdate';
            } else {
                // Insert - perlu produk data untuk produk baru
                $produkData = [
                    'nama_produk' => $this->request->getPost('group_jenis_produksi'),
                    'satuan_per_dus' => $this->request->getPost('satuan_per_dus')
                ];
                $result = $this->adminModel->insertJenisProduksi($jenisProduksiData, $produkData, $bahanBaku);
                $message = 'Data jenis produksi berhasil disimpan';
            }

            return $this->response->setJSON([
                'success' => $result,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data jenis produksi: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteJenisProduksi()
    {
        try {
            $id = $this->request->getPost('id');

            if (!$id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID tidak boleh kosong'
                ]);
            }

            $result = $this->adminModel->deleteJenisProduksi($id);
            
            return $this->response->setJSON([
                'success' => $result,
                'message' => 'Data jenis produksi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            log_message('error', '[CONTROLLER_DELETE] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data jenis produksi: ' . $e->getMessage()
            ]);
        }
    }


    // Barang Methods
    public function getBarangList()
    {
        try {
            $data = $this->adminModel->getBarangList();
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data barang: ' . $e->getMessage()
            ]);
        }
    }
}
