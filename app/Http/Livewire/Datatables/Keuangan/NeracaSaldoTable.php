<?php


namespace App\Http\Livewire\Datatables\Keuangan;

use App\Haramain\Traits\ModelTraits\AkunTrait;
use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\NeracaSaldo;
use App\Models\Keuangan\Akun;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;


class NeracaSaldoTable extends DataTableComponent
{
    
    use DatatablesTraits, AkunTrait;

    public function columns(): array
    {
        return [
            Column::make('Akun ID'),
            Column::make('Debet'),
            Column::make('Kredit'),
        ];
    }

    public function query(): Builder
    {
        return NeracaSaldo::query()->latest();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.neraca_saldo_table';
    }
}
