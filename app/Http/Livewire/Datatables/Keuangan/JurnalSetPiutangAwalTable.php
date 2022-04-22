<?php

namespace App\Http\Livewire\Datatables\Keuangan;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\JurnalSetPiutangAwal;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class JurnalSetPiutangAwalTable extends DataTableComponent
{
    use DatatablesTraits;
    public function columns(): array
    {
        return [
            Column::make('ID'),
            Column::make('Tgl Jurnal'),
            Column::make('Customer', 'customer.nama')
                ->searchable(),
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
