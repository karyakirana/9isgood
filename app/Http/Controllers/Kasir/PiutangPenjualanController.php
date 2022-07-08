<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Master\Customer;
use Illuminate\Http\Request;

class PiutangPenjualanController extends Controller
{
    public function showDetailPenjualan($customer)
    {
        return view('pages.kasir.detail-piutang-penjualan', [
            'customer'=>Customer::find($customer)
        ]);
    }
}
