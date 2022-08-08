<?php

namespace App\Http\Livewire\Datatables\Keuangan;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\JurnalSetReturPenjualanAwal;
use App\Models\Master\Customer;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PiutangPenjualanReturAwalTable extends DataTableComponent
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
            Column::make('Tgl Jurnal', 'tgl_jurnal')
                ->sortable(),
            Column::make('Customer', 'customer.nama')
                ->searchable()
                ->sortable(function(Builder $query, $direction) {
                    return $query->orderBy(Customer::query()->select('nama')->whereColumn('customer.id', 'jurnal_set_retur_penjualan_awal.customer_id'), $direction);
                }),
            Column::make('Pembuat'),
            Column::make('Keterangan'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return JurnalSetReturPenjualanAwal::query()
            ->where('active_cash', session('ClosedCash'));
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.piutang_penjualan_retur_awal_table';
    }
}
