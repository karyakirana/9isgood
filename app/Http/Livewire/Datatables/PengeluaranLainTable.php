<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\PengeluaranLain;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PengeluaranLainTable extends DataTableComponent
{
    use DatatablesTraits;
    public function columns(): array
    {
        return [
            Column::make('ID', 'kode'),
            Column::make('Tanggal', 'tgl_pengeluaran'),
            Column::make('Pihak Ketiga', 'personRelation.nama'),
            Column::make('Tujuan'),
            Column::make('Pembuat', 'users.name'),
            Column::make('Nominal'),
            Column::make('')
        ];
    }

    public function query(): Builder
    {
        return PengeluaranLain::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.pengeluaran_lain_table';
    }
}
