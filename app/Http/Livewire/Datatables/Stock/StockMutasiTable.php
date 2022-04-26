<?php

namespace App\Http\Livewire\Datatables\Stock;

use App\Models\Stock\StockMutasi;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class StockMutasiTable extends DataTableComponent
{
    public $jenisMutasi;

    public function mount($jenisMutasi=null)
    {
        $this->jenisMutasi = $jenisMutasi;
    }

    public function columns(): array
    {
        return [
            Column::make('Column Name'),
        ];
    }

    public function query(): Builder
    {
        $query = StockMutasi::query()
            ->where('active_cash', session('ClosedCash'));
        if ($this->jenisMutasi == null){
            return $query;
        }

        return $query->where('jenis_mutasi', $this->jenisMutasi);
    }
}
