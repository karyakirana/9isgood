<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Penjualan\PenjualanRetur;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PenjualanReturSetTable extends DataTableComponent
{
    use DatatablesTraits;

    protected $listeners =[
        'set_customer'
    ];

    public $customer_id;

    public function set_customer($customer_id = null)
    {
        $this->customer_id = $customer_id;
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'kode')
                ->searchable()
                ->addClass('hidden md:table-cell')
                ->selected(),
            Column::make('Customer', 'customer.nama')
                ->searchable(),
            Column::make('Tgl Nota', 'tgl_nota')
                ->searchable(),
            Column::make('Tgl Tempo', 'tgl_tempo')
                ->searchable(),
            Column::make('Jenis', 'jenis_bayar')
                ->searchable(),
            Column::make('Status', 'status_bayar')
                ->searchable(),
            Column::make('Total', 'total_bayar')
                ->searchable(),
            Column::make(''),
        ];
    }

    public function query(): Builder
    {
        $query = PenjualanRetur::query()->with(['customer', 'gudang', 'users']);

        if ($this->customer_id){
            return $query->where('customer_id', $this->customer_id)->latest();
        }

        return $query->latest();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.penjualan_retur_set_table';
    }
}
