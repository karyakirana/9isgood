<?php namespace App\Haramain\Service\SistemStock\SubStockInventory;

use App\Models\Stock\StockInventory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class GeneralStock
{

    public object $stockInventory;
    public string $sessionActive;

    protected string $active_cash;
    protected string $jenis;
    protected int|null $gudang_id, $produk_id;
    protected int|null $stock_awal, $stock_opname, $stock_masuk, $stock_keluar;
    protected int|null $stock_saldo, $stock_akhir, $stock_lost;

    public object $row_detail;

    public function __construct(StockInventory $stockInventory)
    {
        $this->stockInventory = $stockInventory;
    }

    /**
     * query dasar
     * @return Builder
     */
    protected function stockQuery(): Builder
    {
        return $this->stockInventory::query();
    }

    /**
     * query menggunakan session
     * @return Builder
     */
    public function stockWithPeriode(): Builder
    {
        return $this->stockQuery()->sessionActive();
    }

    /**
     * query berdasarkian gudang
     * @param $gudang_id
     * @return array|Collection
     */
    public function getStockByGudang($gudang_id): array|Collection
    {
        return $this->stockWithPeriode()->where('gudang_id', $gudang_id)->get();
    }

    /**
     * query berdasarkan gudang tanpa menggunakan periode (semua periode)
     * @param $gudang_id
     * @return array|Collection
     */
    public function getStockByGudangWOPeriode($gudang_id): array|Collection
    {
        return $this->stockQuery()->where('gudang_id', $gudang_id)->get();
    }

    /**
     * query berdasarkan gudang dan kondisi pada active cash berjalan
     * @param $gudang_id
     * @param $kondisi
     * @return array|Collection
     */
    public function getStockByGudangAndKondisi($gudang_id, $kondisi): array|Collection
    {
        return $this->stockWithPeriode()
            ->where('gudang_id', $gudang_id)
            ->where('jenis', $kondisi)
            ->get();
    }

    /**
     * query berdasarkan gudang dan kondisi pada active cash semua periode
     * @param $gudang_id
     * @param $kondisi
     * @return array|Collection
     */
    public function getStockByGudangAndKondisiWOPeriode($gudang_id, $kondisi): array|Collection
    {
        return $this->stockQuery()
            ->where('gudang_id', $gudang_id)
            ->where('jenis', $kondisi)
            ->get();
    }

    /**
     * CRUD Stock
     */

}
