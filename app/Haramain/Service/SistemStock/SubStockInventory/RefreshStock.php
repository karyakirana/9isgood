<?php namespace App\Haramain\Service\SistemStock\SubStockInventory;

use App\Models\Stock\StockInventory;

class RefreshStock
{
    public object $stockInventory;

    public function __construct(StockInventory $stockInventory)
    {
        $this->stockInventory = $stockInventory;
    }

    protected function base(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->stockInventory->newQuery()->where('active_cash', session('ClosedCash'));
    }

    protected function clean($field, $kondisi = 'baik')
    {
        return $this->base()->whereNotNull($field)->where('kondisi', $kondisi)->delete();
    }

    public function generateStockOpname()
    {
        // clean all field stock opname
        // get all stock opname by session active
        // insert by detail and return by status
    }

    public function generateStockMasuk()
    {
        // clean all field stock masuk
        // get all stock masuk by session active
        // insert by detail and return by status
    }

    public function generateStockKeluar()
    {
        // clean all field stock keluar
        // get all stock keluar by session active
        // insert by detail and return by status
    }

    public function handleGenerate()
    {
        // get from
    }
}
