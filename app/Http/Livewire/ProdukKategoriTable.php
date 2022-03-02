<?php

namespace App\Http\Livewire;

use App\Models\Master\ProdukKategori;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProdukKategoriTable extends DataTableComponent
{
    public function setTableClass(): ?string
    {
        return 'table table-striped gx-7 border';
    }

    public function setTableRowClass(): ?string
    {
        return 'border align-middle';
    }

    public function columns(): array
    {
        return [
            Column::make('Kode', 'kode_lokal')
                ->searchable(),
            Column::make('Nama')
                ->searchable(),
            Column::make('Keterangan'),
        ];
    }

    public function query(): Builder
    {
        return ProdukKategori::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.produk_kategori_table';
    }
}
