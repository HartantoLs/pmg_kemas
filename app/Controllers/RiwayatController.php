<?php

namespace App\Controllers;

class RiwayatController extends BaseController
{
    public function pengemasan()
    {
        return view('riwayat/pengemasan');
    }

    public function penjualan()
    {
        return view('riwayat/penjualan');
    }

    public function operstok()
    {
        return view('riwayat/operstok');
    }

    public function operpack()
    {
        return view('riwayat/operpack');
    }

    public function seleksi()
    {
        return view('riwayat/seleksi');
    }

    public function kemasUlang()
    {
        return view('riwayat/kemas_ulang');
    }
}
