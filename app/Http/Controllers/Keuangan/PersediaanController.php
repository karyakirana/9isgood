<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersediaanController extends Controller
{
    public function logTransaksi()
    {
        //
    }

    Public function log()
    {
        return view('pages.Keuangan.persediaan-log-index');
    }
}
