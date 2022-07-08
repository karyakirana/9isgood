<?php

namespace App\Http\Livewire\Datatable;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\PiutangPenjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PiutangPenjualanSudah extends DataTableComponent
{
    use DatatablesTraits;

    public $customer_id;

    public function mount($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    public function columns(): array
    {
        return [
            Column::make('ID Penjualan', 'penjualan.kode')
                ->footer(function ($rows){
                    return 'Total :';
                }),
            Column::make('Status'),
            Column::make('Tagihan', 'penjualan.total_bayar')
                ->footer(function ($rows){
                    return rupiah_format($rows->sum('penjualan.total_bayar'));
                }),
            Column::make('Kurang Bayar')
                ->footer(function($rows){
                    return rupiah_format($rows->sum('kurang_bayar'));
                })
        ];
    }

    public function query(): Builder
    {
        return PiutangPenjualan::query()
            ->where('saldo_piutang_penjualan_id', $this->customer_id)
            ->where('status_bayar', 'sudah');
    }

    public function setFooterDataClass(Column $column)
    {
        if ($column->column() == 'penjualan.kode'){
            return 'text-start';
        }
        return null;
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.piutang_penjualan_sudah';
    }
}
