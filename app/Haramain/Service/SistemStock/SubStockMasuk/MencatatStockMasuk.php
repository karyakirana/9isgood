<?php namespace App\Haramain\Service\SistemStock\SubStockMasuk;

use App\Models\Stock\StockMasuk;

class MencatatStockMasuk
{
    protected object $stockMasuk;

    public function __construct(StockMasuk $stockMasuk)
    {
        $this->stockMasuk = $stockMasuk;
    }

    public function handle()
    {
        $this->stockMasuk->newQuery()->create();
    }

    public function generateKode()
    {
        //
    }
}
