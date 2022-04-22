<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Purchase\Pembelian;
use App\Models\Master\Supplier;
use App\Haramain\Traits\ModelTraits\SupplierTraits;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PembelianTable extends DataTableComponent
{
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('ID', 'kode')
                ->searchable()
                ->addClass('hidden md:table-cell')
                ->selected()
                ->sortable(),
            Column::make('Supplier', 'supplier.nama')
                ->searchable()
                ->sortable(function(Builder $query, $direction) {
                    return $query->orderBy(Supplier::query()->select('nama')->whereColumn('supplier.id', 'pembelian.supplier_id'), $direction);
                }),
            Column::make('Tgl Nota', 'tgl_nota')
                ->searchable(),
            Column::make('Tgl Tempo', 'tgl_tempo')
                ->searchable(),
            Column::make('Jenis Bayar', 'jenis_bayar')
                ->searchable(),
            Column::make('Status Bayar', 'status_bayar')
                ->searchable(),
            Column::make('Total Bayar', 'total_bayar')
                ->searchable(),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return Pembelian::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.pembelian_table';
    }
}
