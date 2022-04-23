<?php

namespace App\Http\Livewire\Pembelian;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Purchase\Pembelian;
use App\Models\Master\Supplier;
use App\Haramain\Traits\ModelTraits\SupplierTraits;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PembelianInternalTable extends DataTableComponent
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
            Column::make('Supplier', 'supplier_id')
                ->searchable()
                ->sortable(function(Builder $query, $direction) {
                    return $query->orderBy(Supplier::query()->select('nama')->whereColumn('supplier.id', 'pembelian.supplier_id'), $direction);
                }),
            Column::make('Gudang', 'gudang_id')
            ->sortable(),
            Column::make('Tgl Nota', 'tgl_nota')
            ->sortable(),
            Column::make('Surat Jalan'),
            Column::make('Pembuat'),
            Column::make('Keterangan'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return Pembelian::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', 'INTERNAL');
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.pembelian_internal_table';
    }
}
