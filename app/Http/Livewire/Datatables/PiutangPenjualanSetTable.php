<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\PiutangPenjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PiutangPenjualanSetTable extends DataTableComponent
{
    /**
     * use case :
     *
     */
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('Jenis'),
            Column::make('ID'),
            Column::make('Customer'),
            Column::make('Tgl Nota'),
            Column::make('Tempo'),
            Column::make('Status'),
            Column::make('Kurang Bayar'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return PiutangPenjualan::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.piutang_penjualan_set_table';
    }
}
