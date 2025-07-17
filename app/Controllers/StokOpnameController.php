<?php

namespace App\Controllers;

use App\Models\StokOpnameModel;
use App\Models\GudangModel;
use App\Models\ProdukModel;

class StokOpnameController extends BaseController
{
    protected $stokOpnameModel;
    protected $gudangModel;
    protected $produkModel;

    public function __construct()
    {
        $this->stokOpnameModel = new StokOpnameModel();
        $this->gudangModel = new GudangModel();
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $currentMonthYear = date('Y-m');
        $selectedMonthYear = $this->request->getGet('tanggal_opname_month') ?? $currentMonthYear;
        $selectedTanggal = $selectedMonthYear . '-01';
        $selectedYear = date('Y', strtotime($selectedTanggal));
        $selectedMonth = date('m', strtotime($selectedTanggal));

        // Get data
        $gudangList = $this->gudangModel->orderBy('id_gudang')->findAll();
        $produkList = $this->produkModel->orderBy('nama_produk', 'ASC')->findAll();
        $stokAktualMap = $this->stokOpnameModel->getStokAktual();
        $existingData = $this->stokOpnameModel->getExistingData($selectedYear, $selectedMonth);

        // Determine mode
        $mode = 'create';
        if (!empty($existingData)) {
            $mode = 'edit';
        } elseif ($selectedMonthYear != $currentMonthYear) {
            $mode = 'closed';
        }

        $data = [
            'current_month_year' => $currentMonthYear,
            'selected_month_year' => $selectedMonthYear,
            'selected_tanggal' => $selectedTanggal,
            'gudang_list' => $gudangList,
            'produk_list' => $produkList,
            'stok_aktual_map' => $stokAktualMap,
            'existing_data' => $existingData,
            'mode' => $mode,
            'total_stok_records' => count($stokAktualMap)
        ];

        return view('stok_opname/form', $data);
    }

    public function save()
    {
        try {
            $data = [
                'tanggal_opname' => $this->request->getPost('tanggal_opname'),
                'items' => $this->request->getPost('items')
            ];

            $result = $this->stokOpnameModel->saveStokOpname($data);
            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }
    }
}
