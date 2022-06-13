<?php namespace App\Haramain\Service\SistemStock\SubStockInventory;

use App\Haramain\Service\SistemStock\SubStockOpname\StockOpnameRepo;
use App\Models\Stock\StockInventory;

class RefreshStock
{
    public object $stockInventory;
    public object $generalStock;
    public object $stockBehaviorRepo;
    public object $stockOpnameRepo;

    protected string $field;
    protected string $kondisi;

    public function __construct(
        StockInventory $stockInventory,
        GeneralStock $generalStock,
        StockBehaviorRepo $stockBehaviorRepo,
        StockOpnameRepo $stockOpnameRepo
    )
    {
        $this->stockInventory = $stockInventory;
        $this->generalStock = $generalStock;
        $this->stockBehaviorRepo = $stockBehaviorRepo;
        $this->stockOpnameRepo = $stockOpnameRepo;
    }

    protected function base()
    {
        return $this->stockInventory->newQuery()->sessionActive();
    }

    protected function clean()
    {
        return $this->base()->kondisi($this->kondisi)->clean($this->field);
    }

    public function generateStockOpname(string $kondisi = 'baik'): array
    {
        $this->kondisi = $kondisi;
        // clean all field stock opname
        $this->clean();
        // get all stock opname by session active
        $stockOpname = $this->getStockOpname();
        // insert by detail and return by status
        return $this->handleBulkStore($stockOpname);
    }

    protected function getStockOpname():array
    {
        $stockOpname = $this->stockOpnameRepo->getDataAllByActiveSession($this->kondisi);
        $stockOpnameDetail = [];
        // get all stock opname detail on array
        foreach ($stockOpname->stockOpnameDetail as $item) {
            $stockOpnameDetail[] = $item;
        }
        return $stockOpnameDetail;
    }

    protected function handleBulkStore(array $data): array
    {
        $dataReturn = [];
        foreach ($data as $item) {
            $dataReturn[] = $this->stockBehaviorRepo->createOrUpdate($item, $this->field);
        }
        return $dataReturn;
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
