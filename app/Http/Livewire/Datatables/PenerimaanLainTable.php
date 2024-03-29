<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\PenerimaanLain;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PenerimaanLainTable extends DataTableComponent
{
    use DatatablesTraits;
    public function columns(): array
    {
        return [
            Column::make('ID', 'kode'),
            Column::make('Tanggal', 'tgl_penerimaan'),
            Column::make('Asal'),
            Column::make('Penerima', 'users.name'),
            Column::make('Nominal'),
            Column::make('')
        ];
    }

    public function query(): Builder
    {
        return PenerimaanLain::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.penerimaan_lain_table';
    }
}
