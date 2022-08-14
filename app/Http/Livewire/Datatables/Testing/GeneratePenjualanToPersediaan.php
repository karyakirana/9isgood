<?php

namespace App\Http\Livewire\Datatables\Testing;

use App\Models\Master\Customer;
use App\Models\Penjualan\Penjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class GeneratePenjualanToPersediaan extends DataTableComponent
{
    public function columns(): array
    {
        return [
            Column::make('ID', 'kode')
                ->searchable()
                ->sortable()
                ->addClass('hidden md:table-cell')
                ->selected(),
            Column::make('Customer', 'customer.nama')
                ->searchable()
                ->sortable(function(Builder $query, $direction) {
                    return $query->orderBy(Customer::query()->select('nama')->whereColumn('customer.id', 'penjualan.customer_id'), $direction);
                }),
            Column::make('Tgl Nota', 'tgl_nota')
                ->searchable()
                ->sortable(),
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
        return Penjualan::query()
            ->where('active_cash', session('ClosedCash'));
    }

    public function rowView(): string
    {
        return 'livewire-tables.testing.testing_penjualan_to_persediaan';
    }
}
