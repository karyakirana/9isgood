<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\PenerimaanPenjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PenerimaanPenjualanTable extends DataTableComponent
{
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('ID'),
            Column::make('Tgl'),
            Column::make('Customer'),
            Column::make('Nominal'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return PenerimaanPenjualan::query()
            ->where('active_cash', session('ClosedCash'));
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.penerimaan_penjualan_table';
    }
}
