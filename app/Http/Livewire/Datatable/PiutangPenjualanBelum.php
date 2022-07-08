<?php

namespace App\Http\Livewire\Datatable;

use App\Models\Keuangan\PiutangPenjualan;
use Illuminate\Database\Eloquent\Builder;

class PiutangPenjualanBelum extends PiutangPenjualanSudah
{
    public function query(): Builder
    {
        return PiutangPenjualan::query()
            ->where('saldo_piutang_penjualan_id', $this->customer_id)
            ->where('status_bayar', 'belum');
    }
}
