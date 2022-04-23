<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Purchase\PembelianRetur;
use App\Models\Master\Supplier;
use App\Haramain\Traits\ModelTraits\SupplierTraits;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PembelianReturTable extends DataTableComponent
{
    use DatatablesTraits;

    public $kondisi;

    public function mount($kondisi = 'baik')
    {
        $this->kondisi = $kondisi;
    }

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
                    return $query->orderBy(Supplier::query()->select('nama')->whereColumn('supplier.id', 'pembelian_retur.supplier_id'), $direction);
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
        return PembelianRetur::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('kondisi', $this->kondisi)
            ->latest('kode');
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.pembelian_retur_table';
    }
}
