<?php

namespace App\Http\Livewire\Datatables\Keuangan;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\ClosedCash;
use App\Models\Penjualan\Penjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PenjualanSetPiutangTable extends DataTableComponent
{
    use DatatablesTraits;

    protected $listeners = [
        'refreshDatatable'=>'$refresh',
        'set_customer'
    ];

    public $customer_id;
    public $oldClosedCash;

    public function mount()
    {
        $this->oldClosedCash = ClosedCash::query()
            ->where('closed', session('ClosedCash'))
            ->first()->active;
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
            Column::make('Jenis Bayar', 'jenis_bayar')
                ->searchable(),
            Column::make('Status Bayar', 'status_bayar')
                ->searchable(),
            Column::make('Total Bayar', 'total_bayar')
                ->searchable(),
            Column::make(''),
        ];
    }

    public function set_customer($id)
    {
        $this->customer_id = $id;
    }

    public function query(): Builder
    {
        return Penjualan::query()
            ->with('customer')
            ->where('active_cash', $this->oldClosedCash)
            ->where('customer_id', $this->customer_id);
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.penjualan_set_piutang_table';
    }
}
