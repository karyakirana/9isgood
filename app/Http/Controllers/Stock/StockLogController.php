<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockLogController extends Controller
{
    public function index()
    {
        return view('pages.stock.stock-index');
    }

    public function inventory()
    {
        return view('pages.stock.stock-inventory-index');
    }
}
