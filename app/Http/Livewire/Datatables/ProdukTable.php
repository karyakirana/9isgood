<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Master\Produk;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProdukTable extends DataTableComponent
{
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('ID', 'kode')
                ->sortable()
                ->searchable(),
            Column::make('Lokal', 'kode_lokal')
                ->searchable(),
            Column::make('Produk', 'nama')
                ->sortable()
                ->searchable(),
            Column::make('Harga')
                ->searchable(),
            Column::make('Cover'),
            Column::make('Penerbit'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return Produk::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.produk_table';
    }
}
