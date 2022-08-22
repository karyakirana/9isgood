<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Stock\StockKeluarDetail;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class StockKeluarLogTable extends DataTableComponent
{
    use DatatablesTraits;

    protected $activeCash;

    public function mount($activeCash = true)
    {
        $this->activeCash = $activeCash;
    }

    public function columns(): array
    {
        return [
            Column::make('ID'),
            Column::make('Tipe'),
            Column::make('Tanggal'),
            Column::make('Kode'),
            Column::make('Produk'),
            Column::make('Jumlah'),
        ];
    }

    public function query(): Builder
    {
        $query = StockKeluarDetail::query();
        if ($this->activeCash){
            $query = $query->whereRelation('stockKeluar', 'active_cash', session('ClosedCash'));
        }
        return $query->latest();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.stock_keluar_log_table';
    }
}
