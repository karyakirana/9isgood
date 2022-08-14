<?php

namespace App\Http\Livewire\Testing;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class GeneratePenjualanToStockMasuk extends DataTableComponent
{

    public function columns(): array
    {
        return [
            Column::make('Column Name'),
        ];
    }

    public function query(): Builder
    {
        return Penjualan::query()
            ->where('active_cash', session('ClosedCash'));
    }

    public function rowView(): string
    {
        return 'livewire-tables.testing.testing_penjualan_to_stock_masuk_table';
    }
}
