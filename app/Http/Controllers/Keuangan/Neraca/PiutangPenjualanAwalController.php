<?php

namespace App\Http\Controllers\Keuangan\Neraca;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PiutangPenjualanAwalController extends Controller
{
    public function index()
    {
        return view('pages.Keuangan.piutang-penjualan-awal-index');
    }
}
