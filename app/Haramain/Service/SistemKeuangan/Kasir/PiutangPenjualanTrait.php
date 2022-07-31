<?php namespace App\Haramain\Service\SistemKeuangan\Kasir;

use App\Models\Keuangan\PiutangPenjualan;
use App\Models\Keuangan\SaldoPiutangPenjualan;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanRetur;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait PiutangPenjualanTrait
{
    public function piutangPenjualan()
    {
        return $this->morphMany(PiutangPenjualan::class, 'piutangablePenjualan', 'penjualan_type', 'penjualan_id');
    }
}
