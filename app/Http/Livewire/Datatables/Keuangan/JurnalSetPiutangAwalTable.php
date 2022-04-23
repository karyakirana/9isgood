<?php

namespace App\Http\Livewire\Datatables\Keuangan;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\JurnalSetPiutangAwal;
use App\Models\Master\Customer;
use App\Haramain\Traits\ModelTraits\CustomerTraits;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class JurnalSetPiutangAwalTable extends DataTableComponent
{
    use DatatablesTraits, CustomerTraits;
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
                        return $query->orderBy(Customer::query()->select('nama')->whereColumn('customer.id', 'jurnal_set_piutang_awal.customer_id'), $direction);
                    }),
            Column::make('Pembuat'),
            Column::make('Keterangan'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return JurnalSetPiutangAwal::query()
            ->where('active_cash', session('ClosedCash'));
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.jurnal_set_piutang_awal_table';
    }
}
