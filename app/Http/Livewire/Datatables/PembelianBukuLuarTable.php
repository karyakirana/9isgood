<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Purchase\Pembelian;
use App\Models\Master\Supplier;
use App\Haramain\Traits\ModelTraits\SupplierTraits;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PembelianBukuLuarTable extends DataTableComponent
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
            Column::make('Gudang'),
            Column::make('Tgl Nota', 'tgl_nota')
                ->sortable(),
            Column::make('Surat Jalan', 'nomor_surat_jalan')
                ->sortable(),
            Column::make('Pembuat'),
            Column::make('Keterangan'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return Pembelian::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', 'BLU');
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.pembelian_buku_luar_table';
    }
}
