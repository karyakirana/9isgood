<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersediaanController extends Controller
{
    public function persediaanOut()
    {
        return view('pages.Testing.testing-persediaan');
    }
}
