<?php namespace App\Haramain\Service\SistemStock\SubStockInventory;

use App\Models\Stock\StockInventory;
use App\Models\Stock\StockOpname;

class RefreshStock
{
    public object $stockInventory;
    public object $generalStock;
    public object $stockBehaviorRepo;

    protected string $field;
    protected string $kondisi;

    public function __construct(
        StockInventory $stockInventory,
        GeneralStock $generalStock,
        StockBehaviorRepo $stockBehaviorRepo
    )
    {
        $this->stockInventory = $stockInventory;
        $this->generalStock = $generalStock;
        $this->stockBehaviorRepo = $stockBehaviorRepo;
    }

    protected function base(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->stockInventory->newQuery()->where('active_cash', session('ClosedCash'));
    }

    protected function clean()
    {
        return $this->base()->whereNotNull($this->field)->where('kondisi', $this->kondisi)->delete();
    }

    public function generateStockOpname(string $kondisi = 'baik')
    {
        $this->kondisi = $kondisi;
        // clean all field stock opname
        $clean = $this->clean('stock_opname');
        // get all stock opname by session active
        $stockOpname = $this->getStockOpname();
        // insert by detail and return by status
        return $this->handleBulkStore($stockOpname);
    }

    protected function getStockOpname():array
    {
        $stockOpname = StockOpname::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $this->kondisi)
            ->get();
        $stockOpnameDetail = [];
        // get all stock opname detail on array
        foreach ($stockOpname->stockOpnameDetail as $item) {
            $stockOpnameDetail[] = $item;
        }
        return $stockOpnameDetail;
    }

    protected function handleBulkStore(array $data)
    {
        foreach ($data as $item) {
            $this->stockBehaviorRepo->createOrUpdate($item, $this->field);
        }
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
