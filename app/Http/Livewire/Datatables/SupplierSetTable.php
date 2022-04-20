<?php

namespace App\Http\Livewire\Datatables;

use App\Models\Master\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SupplierSetTable extends DataTableComponent
{

    public function columns(): array
    {
        return [
            Column::make(''),
            Column::make('Jenis'),
            Column::make('Nama')
                ->searchable(),
            Column::make('Alamat')
                ->searchable(),
            Column::make('Telepon'),
            Column::make('Keterangan'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return Supplier::query()
        ->latest();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.supplier_set_table';
    }
}
