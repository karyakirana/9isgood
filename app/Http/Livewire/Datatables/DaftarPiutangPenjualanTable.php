<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\SaldoPiutangPenjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DaftarPiutangPenjualanTable extends DataTableComponent
{
    use DatatablesTraits;
    public function columns(): array
    {
        return [
            Column::make('Customer'),
            Column::make('Saldo'),
            Column::make('')
        ];
    }

    public function query(): Builder
    {
        return SaldoPiutangPenjualan::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.daftar_piutang_penjualan_table';
    }
}
