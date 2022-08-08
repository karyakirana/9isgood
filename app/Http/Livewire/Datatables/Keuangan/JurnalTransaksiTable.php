<?php

namespace App\Http\Livewire\Datatables\Keuangan;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\JurnalTransaksi;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class JurnalTransaksiTable extends DataTableComponent
{
    use DatatablesTraits;
    public function columns(): array
    {
        return [
            Column::make('Type'),
            Column::make('ID'),
            Column::make('Akun'),
            Column::make('Debet'),
            Column::make('Kredit'),
        ];
    }

    public function query(): Builder
    {
        return JurnalTransaksi::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.jurnal_transaksi_table';
    }
}
