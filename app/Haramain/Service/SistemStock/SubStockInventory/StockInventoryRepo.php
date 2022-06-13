<?php namespace App\Haramain\Service\SistemStock\SubStockInventory;

use App\Models\Stock\StockInventory;

class StockInventoryRepo
{
    public object $stockInventory;

    public function __construct(StockInventory $stockInventory)
    {
        $this->stockInventory = $stockInventory;
    }

    // create stock
    public function create($data)
    {
        //
    }
    // update stock
    // clean stock
    // destroy stock
}
