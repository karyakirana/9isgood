<?php

namespace App\Http\Livewire\Datatable;

use App\Models\Keuangan\PiutangPenjualan;
use Illuminate\Database\Eloquent\Builder;

class PiutangPenjualanAll extends PiutangPenjualanBelum
{

    public function query(): Builder
    {
        return PiutangPenjualan::query()
            ->where('saldo_piutang_penjualan_id', $this->customer_id);
    }
}
