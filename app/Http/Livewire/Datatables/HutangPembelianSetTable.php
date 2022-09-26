<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\HutangPembelian;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class HutangPembelianSetTable extends DataTableComponent
{
    use DatatablesTraits;

    protected $listeners = [
        'refreshHutangPembelianTable'=>'$refresh',
        'setSupplier'
    ];

    protected $supplier_id;

    public function columns(): array
    {
        return [
            Column::make(''),
            Column::make('Supplier', 'supplier.nama'),
            Column::make('Jenis'),
            Column::make('Status', 'status_bayar'),
            Column::make('Total Bayar'),
            Column::make('Kurang Bayar'),
            Column::make(''),
        ];
    }

    public function setSupplier($id)
    {
        $this->supplier_id = $id;
    }

    public function query(): Builder
    {
        $hutangPembelian = HutangPembelian::query();
        if ($this->supplier_id){
            return $hutangPembelian->where('saldo_hutang_pembelian_id', $this->supplier_id);
        }
        return $hutangPembelian;
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.hutang_pembelian_set_table';
    }
}
