<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Stock\StockMutasiDetail;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class StockMutasiLogTable extends DataTableComponent
{
    use DatatablesTraits;

    public $activeCash;

    public function mount($activeCash = true)
    {
        $this->activeCash = $activeCash;
    }

    public function columns(): array
    {
        return [
            Column::make('ID'),
            Column::make('Asal'),
            Column::make('Tujuan'),
            Column::make('Tanggal'),
            Column::make('Kode'),
            Column::make('Produk'),
            Column::make('Jumlah'),
        ];
    }

    public function query(): Builder
    {
        $query = StockMutasiDetail::query();
        if ($this->activeCash){
            $query = $query->whereRelation('stockMutasi', 'active_cash', session('ClosedCash'));
        }
        return $query->latest();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.stock_mutasi_log_table';
    }
}
