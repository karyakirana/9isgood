<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\Persediaan;
use App\Models\Keuangan\PersediaanTransaksiDetail;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PersediaanLogTable extends DataTableComponent
{
    use DatatablesTraits;

    public $activeCash;

    public function mount($activeCash = true)
    {
        $this->activeCash = $activeCash;
    }

    public function columns(): array
    {
        return [
            Column::make('Tgl Input'),
            Column::make('Kondisi', 'jenis'),
            Column::make('Produk', 'produk.nama'),
            Column::make('Harga'),
            Column::make('Stock Saldo'),
            Column::make('Saldo'),
        ];
    }

    public function query(): Builder
    {
        $query = Persediaan::query();
        if ($this->activeCash){
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        return $query;
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.persediaan_log_table';
    }
}
