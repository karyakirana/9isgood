<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersediaanController extends Controller
{
    Public function log()
    {
        return view('pages.Keuangan.persediaan-log-index');
    }

    public function logTransaksi()
    {
        //
    }

    public function logOpnamePrice()
    {
        return view('pages.Keuangan.persediaan-log-opname-price');
    }
}
