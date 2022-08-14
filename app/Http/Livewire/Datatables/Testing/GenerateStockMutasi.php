<?php

namespace App\Http\Livewire\Datatables\Testing;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Penjualan\Penjualan;
use App\Models\Stock\StockMutasi;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class GenerateStockMutasi extends DataTableComponent
{
//    use DatatablesTraits;
//    public $jenisMutasi;
//
//    public function mount($jenisMutasi=null)
//    {
//        $this->jenisMutasi = $jenisMutasi;
//    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'kode')
                ->sortable()
                ->addClass('hidden md:table-cell')
                ->selected(),
            Column::make('Gudang Asal', 'gudang_asal_id')
                ->sortable()
                ->searchable(),
            Column::make('Gudang Tujuan', 'gudang_tujuan_id')
                ->sortable()
                ->searchable(),
            Column::make('Pembuat', 'user_id')
                ->sortable()
                ->searchable(),
            Column::make('Tgl Mutasi', 'tgl_mutasi')
                ->sortable(),
            Column::make('Action', 'actions')
        ];
    }

    public function query(): Builder
    {
        return StockMutasi::query()
            ->where('active_cash', session('ClosedCash'));
    }

    public function rowView(): string
    {
        return 'livewire-tables.Testing.testing_stock_mutasi';
    }
}
