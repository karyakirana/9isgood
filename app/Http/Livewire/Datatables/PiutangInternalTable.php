<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\PiutangInternal;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PiutangInternalTable extends DataTableComponent
{
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('ID', 'kode'),
            Column::make('Pegawai', 'saldoPegawai.pegawai.nama'),
            Column::make('Jenis', 'jenis_piutang'),
            Column::make('nominal'),
            Column::make('keterangan'),
            Column::make('')
        ];
    }

    public function query(): Builder
    {
        return PiutangInternal::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.piutang_internal_table';
    }
}
