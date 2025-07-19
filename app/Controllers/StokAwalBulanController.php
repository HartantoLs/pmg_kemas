<?php

namespace App\Controllers;

use App\Models\StokAwalBulanModel;
use App\Models\StokModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;
use CodeIgniter\Controller;

class StokAwalBulanController extends Controller
{
    protected $stokAwalBulanModel;
    protected $stokModel;
    protected $gudangModel;
    protected $produkModel;

    public function __construct()
    {
        $this->stokAwalBulanModel = new StokAwalBulanModel();
        $this->stokModel = new StokModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
    }

    public function form()
    {
        $data = [
            'title' => 'Form Stock Opname',
            'current_month_year' => date('Y-m'),
            'selected_month_year' => $this->request->getGet('tanggal_opname_month') ?? date('Y-m'),
            'gudang_list' => $this->gudangModel->findAll(),
            'produk_list' => $this->produkModel->orderBy('nama_produk', 'ASC')->findAll(),
            'existing_data' => [],
            'mode' => 'create'
        ];

        $selected_tanggal = $data['selected_month_year'] . '-01';
        $selected_year = date('Y', strtotime($selected_tanggal));
        $selected_month = date('m', strtotime($selected_tanggal));

        // Cek data existing untuk menentukan mode
        $existing = $this->stokAwalBulanModel->getDataByMonth($selected_year, $selected_month);
        if (!empty($existing)) {
            $data['mode'] = 'edit';
            foreach ($existing as $row) {
                $data['existing_data'][$row['produk_id']][$row['gudang_id']] = [
                    'dus' => $row['jumlah_dus_opname'],
                    'satuan' => $row['jumlah_satuan_opname']
                ];
            }
        }

        return view('stok_awal_bulan/form', $data);
    }

    public function calculateBeginningStock()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $target_month_year = $this->request->getPost('tanggal_opname_month') ?? date('Y-m');
        $previous_month_date = new \DateTime($target_month_year . '-01');
        $previous_month_date->modify('-1 month');
        $prev_year = $previous_month_date->format('Y');
        $prev_month = $previous_month_date->format('m');

        $start_of_prev_month = $prev_year . '-' . $prev_month . '-01';
        $end_of_prev_month = $previous_month_date->format('Y-m-t');

        // Ambil data opname bulan sebelumnya
        $opname_data = $this->stokAwalBulanModel->getDataByMonth($prev_year, $prev_month);
        $calculated_stock = [];
        $opname_found = !empty($opname_data);

        foreach ($opname_data as $row) {
            $calculated_stock[$row['produk_id']][$row['gudang_id']] = [
                'dus' => (int)$row['jumlah_dus_opname'],
                'satuan' => (int)$row['jumlah_satuan_opname']
            ];
        }

        // Ambil mutasi bulan sebelumnya
        $mutasi_data = $this->stokAwalBulanModel->getMutasiByPeriod($start_of_prev_month, $end_of_prev_month);
        foreach ($mutasi_data as $row) {
            $p_id = $row['produk_id'];
            $g_id = $row['gudang_id'];
            if (!isset($calculated_stock[$p_id][$g_id])) {
                $calculated_stock[$p_id][$g_id] = ['dus' => 0, 'satuan' => 0];
            }
            $calculated_stock[$p_id][$g_id]['dus'] += (int)$row['total_mutasi_dus'];
            $calculated_stock[$p_id][$g_id]['satuan'] += (int)$row['total_mutasi_satuan'];
        }

        return $this->response->setJSON([
            'success' => true,
            'opname_found' => $opname_found,
            'calculated_stock' => $calculated_stock
        ]);
    }

    public function saveOpname()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $tanggal_opname = $this->request->getPost('tanggal_opname');
        $items = $this->request->getPost('items');

        if (empty($tanggal_opname) || empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $saved_count = 0;
            foreach ($items as $produk_id => $gudang_data) {
                foreach ($gudang_data as $gudang_id => $jumlah) {
                    $dus = intval($jumlah['dus'] ?? 0);
                    $satuan = intval($jumlah['satuan'] ?? 0);
                    
                    $data = [
                        'tanggal_opname' => $tanggal_opname,
                        'produk_id' => $produk_id,
                        'gudang_id' => $gudang_id,
                        'jumlah_dus_opname' => $dus,
                        'jumlah_satuan_opname' => $satuan
                    ];

                    if ($this->stokAwalBulanModel->saveOpname($data)) {
                        $saved_count++;
                    }
                }
            }

            // Rekalkulasi bulan-bulan berikutnya
            $recalculated_months = $this->stokAwalBulanModel->recalculateFollowingMonths($tanggal_opname);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data']);
            }

            $message = "Data stock opname berhasil diperbarui! ($saved_count records)";
            if (!empty($recalculated_months)) {
                $message .= "<br><b>Rekalkulasi otomatis berhasil!</b> Stok awal untuk bulan " . implode(', ', $recalculated_months) . " telah diperbarui.";
            } else {
                $message .= "<br>Tidak ada perubahan stok di bulan-bulan berikutnya.";
            }

            return $this->response->setJSON(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }
}
