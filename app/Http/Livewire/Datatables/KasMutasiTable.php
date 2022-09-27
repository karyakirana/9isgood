<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\KasMutasi;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class KasMutasiTable extends DataTableComponent
{
    use DatatablesTraits;
    public function columns(): array
    {
        return [
            Column::make('ID', 'kode'),
            Column::make('Tanggal', 'tgl_mutasi'),
            Column::make('Pembuat', 'users.name'),
            Column::make('Total', 'total_mutasi'),
            Column::make('Keterangan'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return KasMutasi::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.kas_mutasi_table';
    }
}
