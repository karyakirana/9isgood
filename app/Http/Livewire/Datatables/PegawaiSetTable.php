<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Master\Pegawai;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PegawaiSetTable extends DataTableComponent
{
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('ID', 'kode'),
            Column::make('Nama'),
            Column::make('Keterangan'),
            Column::make('')
        ];
    }

    public function query(): Builder
    {
        return Pegawai::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.pegawai_set_table';
    }
}
