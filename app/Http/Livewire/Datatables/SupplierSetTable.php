<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Master\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SupplierSetTable extends DataTableComponent
{
    use DatatablesTraits;

    public $search;

    protected $listeners = [
        'refreshSupplierTable'=>'$refresh',
    ];

    protected string $pageName = 'supplier';
    protected string $tableName = 'haramainv2.supplier';

    protected $queryString = [

        'search' => ['as' => 's'],

    ];

    public function columns(): array
    {
        return [
            Column::make(''),
            Column::make('Jenis'),
            Column::make('Nama', 'nama')
                ->sortable()
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
        return Supplier::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.supplier_set_table';
    }
}
