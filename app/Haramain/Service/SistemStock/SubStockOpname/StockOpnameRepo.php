<?php namespace App\Haramain\Service\SistemStock\SubStockOpname;

use App\Models\Stock\StockOpname;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class StockOpnameRepo
{
    protected object $stockOpname;

    public function __construct(StockOpname $stockOpname)
    {
        $this->stockOpname = $stockOpname;
    }

    public function getDataAllByActiveSession($jenis = 'baik'): Collection|array
    {
        return $this->stockOpname->newQuery()
            ->with('stockOpnameDetail')
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $jenis)
            ->get();
    }

    public function getDataById($stockOpanameId): Model|Collection|Builder|array|null
    {
        return $this->stockOpname->newQuery()->find($stockOpanameId);
    }
}
