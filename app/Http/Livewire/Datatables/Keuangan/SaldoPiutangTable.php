<?php

namespace App\Http\Livewire\Datatables\Keuangan;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\SaldoPiutangPenjualan;
use App\Models\Master\Customer;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SaldoPiutangTable extends DataTableComponent
{
    use DatatablesTraits;
    public function columns(): array
    {
        return [
            Column::make('ID', 'customer_id'),
            Column::make('Customer', 'customer.nama')
                ->searchable()
                ->sortable(function (Builder $query, $direction){
                    return $query->orderBy(Customer::query()->select('nama')->whereColumn('haramain_keuangan.saldo_piutang_penjualan.customer_id', 'haramainv2.customer.id'), $direction);
                }),
            Column::make('Saldo')
                ->sortable()
                ->searchable(),
        ];
    }

    public function query(): Builder
    {
        return SaldoPiutangPenjualan::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.saldo_piutang_table';
    }
}
