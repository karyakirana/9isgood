<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\PengeluaranPembelian;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PengeluaranPembelianTable extends DataTableComponent
{
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('Id', 'kode'),
            Column::make('Jenis'),
            Column::make('Tanggal'),
            Column::make('Supplier'),
            Column::make('Pembuat', 'user_id'),
            Column::make('Total'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return PengeluaranPembelian::query()->where('active_cash', session('ClosedCash'))->latest();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.pengeluaran_pembelian_table';
    }
}
