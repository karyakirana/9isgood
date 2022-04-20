<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Master\Pegawai;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PegawaiTable extends DataTableComponent
{
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('Kode', 'id')
                ->sortable()
                ->searchable(),
            Column::make('Nama') 
                ->sortable()
                ->searchable(),
            Column::make('Alamat'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return Pegawai::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.pegawai_table';
    }
}
