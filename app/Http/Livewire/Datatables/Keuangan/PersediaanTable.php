<?php

namespace App\Http\Livewire\Datatables\Keuangan;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\Persediaan;
use App\Models\Master\Produk;
use App\Haramain\Traits\ModelTraits\ProdukTraits;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PersediaanTable extends DataTableComponent
{
    use DatatablesTraits, ProdukTraits;
    public function columns(): array
    {
        return [
            Column::make('Jenis', 'jenis')
                ->searchable()
                ->sortable(),
            Column::make('Gudang', 'gudang_id')
                ->searchable()
                ->sortable(),
            Column::make('Produk', 'produk.nama')
                ->searchable()
                ->sortable(function(Builder $query, $direction) {
                    return $query->orderBy(Produk::query()->select('nama')->whereColumn('produk.id', 'persediaan.produk_id'), $direction);
                }),
            Column::make('Harga', 'harga')
                ->searchable()
                ->sortable(),
            Column::make(' Opname','stock_opname')
                ->searchable()  
                ->sortable(),
            Column::make(' Masuk','stock_masuk')
                ->searchable()
                ->sortable(),
            Column::make(' Keluar','stock_keluar')
                ->searchable()
                ->sortable(),
            Column::make(' Saldo','stock_saldo')
                ->searchable()
                ->sortable(),
        ];
    }

    public function query(): Builder
    {
        return Persediaan::query()
        ->where('active_cash', session('ClosedCash'));
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.persediaan_table';
    }
}
