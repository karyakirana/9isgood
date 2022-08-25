<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockMutasiController extends Controller
{
    public function index()
    {
        return view('pages.stock.stock-mutasi-index');
    }

    public function jenisMutasi($id)
    {
        return view('pages.stock.stock-mutasi-index', ['id'=>$id]);
    }
}
