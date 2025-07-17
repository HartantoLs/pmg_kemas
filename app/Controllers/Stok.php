<?php

namespace App\Controllers;

use App\Models\StokModel;

class Stok extends BaseController
{
    public function index()
    {
        $model = new StokModel();
        $data['stok'] = $model->findAll();

        return view('stok_view', $data);
    }
}
