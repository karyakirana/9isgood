<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Penjualan\PenjualanRetur;
use App\Models\Master\Customer;
use App\Haramain\Traits\ModelTraits\CustomerTraits;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PenjualanReturTable extends DataTableComponent
{
    use DatatablesTraits, CustomerTraits;

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
            Column::make('Customer', 'customer.nama')
                ->searchable()
                ->sortable(function(Builder $query, $direction) {
                    return $query->orderBy(Customer::query()->select('nama')->whereColumn('customer.id', 'penjualan_retur.customer_id'), $direction);
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
        return PenjualanRetur::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis_retur', $this->kondisi);
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.penjualan_retur_table';
    }
}
