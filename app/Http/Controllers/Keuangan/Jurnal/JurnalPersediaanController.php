<?php

namespace App\Http\Controllers\Keuangan\Jurnal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JurnalPersediaanController extends Controller
{
    // daftar jurnal persediaan
    public function index()
    {
        return view('pages.Keuangan.jurnal-persediaan-index');
    }

    // daftar persediaan
    public function indexPersediaan()
    {
        return view('pages.Keuangan.persediaan');
    }
}
