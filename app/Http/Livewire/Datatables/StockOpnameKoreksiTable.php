<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Stock\StockOpnameKoreksi;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class StockOpnameKoreksiTable extends DataTableComponent
{
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('ID', 'kode'),
            Column::make('Tanggal'),
            Column::make('Jenis'),
            Column::make('Kondisi'),
            Column::make('Gudang'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return StockOpnameKoreksi::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.stock_opname_koreksi_table';
    }
}
