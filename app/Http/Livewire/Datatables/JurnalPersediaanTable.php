<?php

namespace App\Http\Livewire\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class JurnalPersediaanTable extends DataTableComponent
{

    public function columns(): array
    {
        return [
            Column::make('Column Name'),
        ];
    }

    public function query(): Builder
    {

    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.jurnal_persediaan_table';
    }
}
