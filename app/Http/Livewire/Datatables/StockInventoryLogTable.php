<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Stock\StockInventory;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class StockInventoryLogTable extends DataTableComponent
{
    use DatatablesTraits;

    protected $activeCash;

    public $stockField;

    public function mount($activeCash = true)
    {
        $this->activeCash = $activeCash;
    }

    public function sortByStock($stockField)
    {
        //
    }

    public function resetForm()
    {
        $this->reset(['stockField']);
    }

    public function columns(): array
    {
        return [
            Column::make('Gudang', 'gudang.nama'),
            Column::make('Kondisi', 'jenis'),
            Column::make('Kode', 'produk.kode_lokal'),
            Column::make('Produk', 'produk.nama'),
            Column::make('Saldo', 'stock_saldo'),
        ];
    }

    public function query(): Builder
    {
        $query = StockInventory::query();
        if ($this->activeCash){
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        if ($this->stockField){
            //
        }
        return $query;
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.stock_inventory_log_table';
    }
}
