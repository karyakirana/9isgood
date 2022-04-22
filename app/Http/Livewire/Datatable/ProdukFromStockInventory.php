<?php

namespace App\Http\Livewire\Datatable;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Master\Produk;
use App\Models\Stock\StockInventory;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProdukFromStockInventory extends DataTableComponent
{
    use DatatablesTraits;

    protected $listeners = [
        'refreshDatatable'=>'$refresh',
        'setGudang',
    ];

    public bool $singleColumnSorting = true;
    public int|null $gudang_id = null;

    public function columns(): array
    {
        return [
            Column::make('Kode'),
            Column::make('Gudang', 'gudang.nama')
                ->searchable(),
            Column::make('Produk', 'produk.nama')
                ->sortable(function(Builder $query, $direction){
                    return $query->orderBy(Produk::query()->select('nama')->whereColumn('produk.id', 'stock_inventory.produk_id'), $direction);
                })
                ->searchable(),
            Column::make('Jumlah', 'stock_saldo'),
            Column::make(''),
        ];
    }

    public function setGudang($gudang_id)
    {
        $this->gudang_id = $gudang_id;
    }

    public function query(): Builder
    {
        return StockInventory::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('gudang_id', $this->gudang_id);
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.produk_from_stock_inventory';
    }
}
