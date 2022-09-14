<?php

namespace App\Http\Controllers\Testing;

use App\Haramain\Repository\Persediaan\PersediaanRepository;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanKeluar;
use App\Http\Controllers\Controller;
use App\Models\Stock\StockMutasi;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        try {
            $persediaankeluar = PersediaanKeluar::create('baik', '1', 2, 90000)->getData();
            var_dump($persediaankeluar);
        } catch (RecordsNotFoundException $e){
            echo $e->getMessage();
        }
    }
}
