<?php

namespace App\Http\Controllers\Testing;

use App\Haramain\Repository\Persediaan\PersediaanRepository;
use App\Http\Controllers\Controller;
use App\Models\Stock\StockMutasi;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $mutasi = StockMutasi::query()->find(200);
        $mutasiDetail = $mutasi->stockMutasiDetail;
        //dd($mutasiDetail);
        foreach ($mutasiDetail as $item) {
            $getPersediaan = (new PersediaanRepository())->getStockOut($mutasi->gudang_asal_id, 'baik', $item);
            $collect = collect($getPersediaan);
            //dd($collect);
            foreach ($collect as $row){
                dd($row['persediaan_id']);
            }
        }
    }
}
