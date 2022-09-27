<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Master\PersonRelation;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PersonRelationSetTable extends DataTableComponent
{
    use DatatablesTraits;
    public function columns(): array
    {
        return [
            Column::make('ID', 'kode'),
            Column::make('Nama'),
            Column::make('Telepon'),
            Column::make('Alamat'),
            Column::make('')
        ];
    }

    public function query(): Builder
    {
        return PersonRelation::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.person_relation_set_table';
    }
}
