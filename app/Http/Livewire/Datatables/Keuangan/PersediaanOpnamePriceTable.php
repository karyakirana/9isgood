<?php

namespace App\Http\Livewire\Datatables\Keuangan;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\PersediaanOpnamePrice;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PersediaanOpnamePriceTable extends DataTableComponent
{
    use DatatablesTraits;

    public function columns(): array
    {
        return [
            Column::make('Tgl Input'),
            Column::make('kondisi'),
            Column::make('Gudang'),
            Column::make('Produk', 'produk.nama')
                ->searchable(),
            Column::make('Harga'),
        ];
    }

    public function query(): Builder
    {
        return PersediaanOpnamePrice::query()->where('active_cash', session('ClosedCash'))
            ->oldest('produk_id');
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.persediaan_opname_price_table';
    }
}
