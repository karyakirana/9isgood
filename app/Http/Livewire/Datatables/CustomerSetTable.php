<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Master\Customer;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CustomerSetTable extends DataTableComponent
{
    use DatatablesTraits;
    protected string $pageName = 'customerPage';
    protected string $tableName = 'customer';

    public function columns(): array
    {
        return [
            Column::make('Kode', 'kode')
                ->searchable()
                ->sortable(),
            Column::make('Nama', 'nama')
                ->searchable()
                ->sortable(),
            Column::make('Telepon', 'telepon'),
            Column::make('Diskon', 'diskon'),
            Column::make('Action'),
        ];
    }

    public function query(): Builder
    {
        return Customer::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.customer_set_table';
    }
}
