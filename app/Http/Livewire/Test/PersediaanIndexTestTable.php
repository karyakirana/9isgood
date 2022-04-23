<?php

namespace App\Http\Livewire\Test;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\PersediaanTransaksi;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PersediaanIndexTestTable extends DataTableComponent
{
    use DatatablesTraits;
    public function columns(): array
    {
        return [
            Column::make('id', 'kode'),
            Column::make('Tanggal', 'created_at'),
            Column::make('Jenis'),
            Column::make('Kondisi'),
            Column::make('Gudang'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        return PersediaanTransaksi::query();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.persediaan_index_test_table';
    }
}
