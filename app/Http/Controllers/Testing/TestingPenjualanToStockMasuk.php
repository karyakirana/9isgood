<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use App\Models\Penjualan\Penjualan;
use Illuminate\Http\Request;

class TestingPenjualanToStockMasuk extends Controller
{
    public function testinggeneratePenjualan (Penjualan $penjualan)
    {
        return view('pages.Testing.testing-penjualan-to-stock-masuk', [
            'penjualan'=>$penjualan
        ]);
    }
}
