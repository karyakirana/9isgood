<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use App\Models\Stock\StockMutasi;
use Illuminate\Http\Request;

class TestingStockMutasi extends Controller
{
    public function testingstockMutasi (StockMutasi $stockMutasi) {
        return view('pages.Testing.testing-stock-mutasi',
        [
           'stockmutasi'=>$stockMutasi
        ]);

    }
}
