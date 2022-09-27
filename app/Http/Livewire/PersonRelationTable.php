<?php

namespace App\Http\Livewire;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Master\PersonRelation;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PersonRelationTable extends DataTableComponent
{
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('ID', 'Kode'),
            Column::make('Nama')
                ->searchable(),
            Column::make('Telepon'),
            Column::make('Alamat')
                ->searchable(),
            Column::make('Keterangan'),
            Column::make('')
        ];
    }

    public function query(): Builder
    {
        return PersonRelation::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.person_relation_table';
    }
}
