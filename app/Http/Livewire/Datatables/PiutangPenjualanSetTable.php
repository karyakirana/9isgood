<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\PiutangPenjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PiutangPenjualanSetTable extends DataTableComponent
{
    /**
     * use case :
     *
     */
    use DatatablesTraits;

    protected $listeners = [
        'refreshDatatable' => '$refresh',
        'set_customer',
        'unset_customer'
    ];

    protected $customer_id;

    public function set_customer($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    public function unset_customer()
    {
        unset($this->customer_id);
        $this->emit('refreshDatatable');
    }

    public function columns(): array
    {
        return [
            Column::make('Jenis'),
            Column::make('ID'),
            Column::make('Customer'),
            Column::make('Tgl Nota'),
            Column::make('Tempo'),
            Column::make('Status'),
            Column::make('Kurang Bayar'),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        $query = PiutangPenjualan::query();
        if ($this->customer_id){
            return $query->where('saldo_piutang_penjualan_id', $this->customer_id);
        }
        return $query;
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.piutang_penjualan_set_table';
    }
}
