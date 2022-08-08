<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\ClosedCash;
use App\Models\Penjualan\Penjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PenjualanSetTable extends DataTableComponent
{
    use DatatablesTraits;

    protected $listeners =[
        'set_customer',
        'unset_customer',
        'refreshDatatable' => '$refresh'
    ];

    public $customer_id;

    public $lastSession;
    public $oldClosedCash;
    public $setPiutang;

    public function mount($lastSession = false, $setPiutang = false)
    {
        $this->lastSession = $lastSession;
        $this->oldClosedCash = ClosedCash::query()
            ->where('closed', session('ClosedCash'))
            ->first()->active;
    }

    public function set_customer($customer_id = null)
    {
        $this->customer_id = $customer_id;
    }

    public function unset_customer()
    {
        $this->customer_id = null;
        $this->emit('$refresh');
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
        $query = Penjualan::query()->with(['customer', 'gudang', 'users']);

        if ($this->customer_id){
            $query->where('customer_id', $this->customer_id);
        }

        if ($this->lastSession){
            $query->where('active_cash', $this->oldClosedCash);
        }

        if($this->setPiutang){
            $query->whereNotIn('status_bayar', ['set_piutang']);
        }

        return $query->latest();
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.penjualan_set_table';
    }
}
