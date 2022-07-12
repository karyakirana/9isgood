<?php namespace App\Haramain\Service\SistemPenjualan\SubPenjualan;

use App\Haramain\Service\SystemCore\SessionTraits;
use App\Models\Penjualan\Penjualan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


class PenjualanRepo
{
    public function kode()
    {
        //
    }

    public function store($data)
    {
        //
    }

    public function update($data)
    {
        //
    }

    public function destroy($penjualanId)
    {
        //
    }

    public function updateStatus($penjualanID, $status): bool|int
    {
        $penjualan = $this->getById($penjualanID);
        return $penjualan->update(['status_bayar'=>$status]);
    }

    public function getById($penjualanId): Model|Collection|Builder|array|null
    {
        return Penjualan::query()
            ->find($penjualanId);
    }
}
