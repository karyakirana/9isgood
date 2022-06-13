<?php namespace App\Haramain\Service\SistemStock\SubStockOpname;

use App\Models\Stock\StockOpname;

class StockOpnameService
{
    protected object $stockOpname;

    public function __construct(StockOpname $stockOpname)
    {
        $this->stockOpname = $stockOpname;
    }

    public function handleCreate($data)
    {
        // store
    }

    public function handleUpdate($data)
    {
        // update
    }

    public function handleDestroy($data)
    {
        //
    }
}
