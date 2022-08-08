<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JurnalPiutangPenjualanAwalController extends Controller
{
    public function createForPenjualan()
    {
        return view('pages.Keuangan.jurnal-piutang-penjualan-awal', ['mode'=>'penjualan']);
    }

    public function createForRetur()
    {
        return view('pages.Keuangan.jurnal-piutang-penjualan-awal', ['mode'=>'retur']);
    }

    public function edit($id)
    {
        return view('pages.Keuangan.jurnal-piutang-penjualan-awal', ['id'=>$id]);
    }
}
