<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\PiutangPenjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PiutangPenjualanDetailTable extends DataTableComponent
{
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('Customer'),
            Column::make('Jenis'),
            Column::make('Kode'),
            Column::make('Status'),
            Column::make('Kurang Bayar'),
        ];
    }

    public function query(): Builder
    {
        return PiutangPenjualan::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.piutang_penjualan_detail_table';
    }
}
