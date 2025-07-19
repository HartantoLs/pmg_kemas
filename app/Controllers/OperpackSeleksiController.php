<?php

namespace App\Controllers;

use App\Models\OperpackSeleksiModel;
use App\Models\ProdukModel;
use App\Models\StokModel;

class OperpackSeleksiController extends BaseController
{
    protected $operpackSeleksiModel;
    protected $produkModel;
    protected $stokModel;

    public function __construct()
    {
        $this->operpackSeleksiModel = new OperpackSeleksiModel();
        $this->produkModel = new ProdukModel();
        $this->stokModel = new StokModel();
    }

    public function form()
    {
        $data = [
            'title' => 'Form Input Hasil Seleksi',
            'produk_list' => $this->produkModel->findAll()
        ];

        return view('operpack_seleksi/form', $data);
    }

    public function riwayat()
    {
        $data = [
            'title' => 'Riwayat Seleksi Overpack',
            'produk_list' => $this->produkModel->findAll()
        ];

        return view('operpack_seleksi/riwayat', $data);
    }

    public function getStokSeleksi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $idProduk = $this->request->getGet('id_produk');
        
        if (!$idProduk) {
            return $this->response->setJSON(['belum_seleksi' => 0]);
        }

        try {
            $stokData = $this->operpackSeleksiModel->getStokBelumSeleksi($idProduk);
            $belumSeleksi = $stokData['belum_seleksi'] ?? 0;

            return $this->response->setJSON(['belum_seleksi' => max(0, $belumSeleksi)]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['belum_seleksi' => 0]);
        }
    }

    public function simpanSeleksi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $idProduk = (int)$this->request->getPost('id_produk');
            $tanggal = $this->request->getPost('tanggal');
            $pcsAman = (int)$this->request->getPost('pcs_aman');
            $pcsCurah = (int)$this->request->getPost('pcs_curah');
            $totalInput = $pcsAman + $pcsCurah;

            // Validasi dasar
            if ($pcsAman < 0 || $pcsCurah < 0 || $totalInput == 0) {
                throw new \Exception("Jumlah pcs aman atau curah harus diisi dengan benar.");
            }

            // Cek stok tersedia
            $stokData = $this->stokModel->getStokOverpack($idProduk);
            $stokTersedia = $stokData['belum_seleksi'] ?? 0;

            // Validasi stok
            if ($totalInput > $stokTersedia) {
                throw new \Exception("Jumlah yang diinput ($totalInput pcs) melebihi stok yang tersedia ($stokTersedia pcs).");
            }

            // Simpan data
            $data = [
                'produk_id' => $idProduk,
                'tanggal' => $tanggal,
                'pcs_aman' => $pcsAman,
                'pcs_curah' => $pcsCurah
            ];

            $this->operpackSeleksiModel->insert($data);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Gagal menyimpan data ke database.");
            }

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Data seleksi berhasil disimpan!'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
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
            'report_data' => $this->operpackSeleksiModel->getRiwayat([
            'tanggal_mulai' => $tglMulai,
            'tanggal_akhir' => $tglAkhir,
            'produk_id'     => $filterProduk
            ]),
        ];

        return view('operpack_seleksi/riwayat_ajax_table', $data);
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

            $data = $this->operpackSeleksiModel->getDetailWithStok($id);
            
            if (!$data) {
                throw new \Exception('Data tidak ditemukan');
            }

            return $this->response->setJSON(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateSeleksi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $id = (int)$this->request->getPost('id');
            $newAman = (int)$this->request->getPost('pcs_aman');
            $newCurah = (int)$this->request->getPost('pcs_curah');

            if ($id <= 0 || $newAman < 0 || $newCurah < 0) {
                throw new \Exception("Nilai input tidak valid");
            }

            // Get old data
            $oldData = $this->operpackSeleksiModel->find($id);
            if (!$oldData) {
                throw new \Exception("Data seleksi tidak ditemukan.");
            }

            // Hitung selisih
            $totalSelisih = ($newAman - $oldData['pcs_aman']) + ($newCurah - $oldData['pcs_curah']);

            // Validasi jika ada penambahan
            if ($totalSelisih > 0) {
                $stokData = $this->stokModel->getStokOverpack($oldData['produk_id']);
                $stokTersedia = $stokData['belum_seleksi'] ?? 0;

                if ($totalSelisih > $stokTersedia) {
                    throw new \Exception("Stok rusak yang belum diseleksi hanya: " . $stokTersedia . " pcs. Anda mencoba menambah: " . $totalSelisih . " pcs.");
                }
            }

            // Validasi jika ada pengurangan pcs_aman
            if (($newAman - $oldData['pcs_aman']) < 0) {
                $stokOverpackData = $this->operpackSeleksiModel->getStokSiapRepack($oldData['produk_id']);
                $stokSiapRepack = (int)($stokOverpackData['stok_siap_repack'] ?? 0);
                
                if ($stokSiapRepack < abs($newAman - $oldData['pcs_aman'])) {
                    throw new \Exception("Gagal mengurangi Pcs Aman. Sebagian produk kemungkinan sudah dikemas ulang. Sisa stok siap kemas: " . $stokSiapRepack);
                }
            }

            // Update data menggunakan method dari model
            $updateData = [
                'id' => $id,
                'pcs_aman' => $newAman,
                'pcs_curah' => $newCurah
            ];

            $result = $this->operpackSeleksiModel->updateSeleksi($updateData);
            
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteSeleksi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $id = (int)$this->request->getPost('id');
            
            if ($id <= 0) {
                throw new \Exception("ID tidak valid");
            }

            // Get data to delete
            $dataToDelete = $this->operpackSeleksiModel->find($id);
            if (!$dataToDelete) {
                throw new \Exception("Data seleksi tidak ditemukan.");
            }

            // Validasi apakah masih bisa dihapus
            $stokOverpackData = $this->operpackSeleksiModel->getStokSiapRepack($dataToDelete['produk_id']);
            $stokSiapRepack = (int)($stokOverpackData['stok_siap_repack'] ?? 0);
            
            if ($stokSiapRepack < $dataToDelete['pcs_aman']) {
                throw new \Exception("Gagal menghapus. Sebagian Pcs Aman dari log ini kemungkinan sudah dikemas ulang.");
            }

            // Delete data menggunakan method dari model
            $result = $this->operpackSeleksiModel->hapusSeleksi($id);
            
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }
    public function simpanRepack()
    {
        $data = $this->request->getPost([
            'produk_id', 'tanggal', 'jumlah_kemas'
        ]);

        $model = new \App\Models\OperpackKemasUlangModel();
        $result = $model->simpanRepack($data);

        return $this->response->setJSON($result);
    }

}