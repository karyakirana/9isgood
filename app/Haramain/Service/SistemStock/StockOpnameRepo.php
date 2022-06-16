<?php namespace App\Haramain\Service\SistemStock;

use App\Models\Stock\StockOpname;
use App\Models\Stock\StockOpnameDetail;
use Illuminate\Database\Eloquent\Collection;

class StockOpnameRepo
{
    public function __construct()
    {
    }

    public function getStockOpnameDetail($session): Collection|array
    {
        return StockOpnameDetail::query()
            ->leftJoin('stock_opname', 'stock_opname.id', '=', 'stock_opname_detail.stock_opname_id')
            ->where('stock_opname.active_cash', $session)
            ->get();
    }
}
