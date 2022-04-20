<?php

namespace App\Http\Livewire\Datatables;

use App\Models\Stock\StockKeluar;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class StockKeluarTable extends DataTableComponent
{
    public $kondisi, $gudang;

    public function mount($kondisi = 'baik')
    {
        $this->kondisi = $kondisi;
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'kode')
                ->sortable()
                ->searchable()
                ->addClass('hidden md:table-cell')
                ->selected(),
            Column::make('Jenis')
                ->sortable()
                ->searchable(),
            Column::make('Gudang', 'gudang_id')
                ->sortable()
                ->searchable(),
            Column::make('Tgl Keluar', 'tgl_keluar')
                ->sortable()
                ->searchable(),
            Column::make('Supplier'),
            Column::make('Customer'),
            Column::make('Pembuat', 'user_id')
                ->sortable()
                ->searchable(),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        $query = StockKeluar::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('kondisi', $this->kondisi);

        if ($this->gudang)
        {
            return $query->where('gudang_id', $this->gudang);
        }

        return $query;
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.stock_keluar_table';
    }
}
