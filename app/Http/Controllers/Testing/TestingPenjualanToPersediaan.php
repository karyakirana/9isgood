<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use App\Models\Penjualan\Penjualan;
use Illuminate\Http\Request;

class TestingPenjualanToPersediaan extends Controller
{
    public function testingPenjualanToPersediaan (Penjualan $penjualan)
    {
        return view('pages.Testing.testing-penjualan-to-persediaan', [
            'penjualan'=>$penjualan
        ]);
    }
}
