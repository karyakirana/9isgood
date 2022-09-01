<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\SistemKeuangan\SubNeraca\SaldoHutangPembelianRepo;
use App\Models\Keuangan\HutangPembelian;

class HutangPembelianRepo
{
    protected $saldoHutangPembelianRepo;

    public function __construct()
    {
        $this->saldoHutangPembelianRepo = new SaldoHutangPembelianRepo();
    }

    public function getDataById($hutangableType, $hutangableId)
    {
        return HutangPembelian::query()
            ->where('pembelian_type', $hutangableType)
            ->where('pembelian_id', $hutangableId)
            ->first();
    }

    public function getDataAll()
    {
        $query = HutangPembelian::all();
    }

    public function store($data, $hutangableType, $hutangableId)
    {
        $data = (object) $data;
        $hutangPembelian = HutangPembelian::query()
            ->create([
                'saldo_hutang_pembelian_id'=>$data->supplierId,
                'pembelian_type'=>$hutangableType,
                'pembelian_id'=>$hutangableId,
                'status_bayar'=>$data->statusBayar, // lunas, belum, kurang
                'total_bayar'=>$data->totalBayar,
                'kurang_bayar'=>$data->totalBayar,
            ]);
        // update saldo hutang pembelian
        $this->saldoHutangPembelianRepo->saldoIncrement($data->supplierId, $data->totalBayar);
        return $hutangPembelian;
    }

    public function update($data, $hutangableType, $hutangableId)
    {
        $data = (object) $data;
        $this->getDataById($hutangableType, $hutangableId)->update([
            'saldo_hutang_pembelian_id'=>$data->supplierId,
            'pembelian_type'=>$hutangableType,
            'pembelian_id'=>$hutangableId,
            'status_bayar'=>$data->statusBayar, // lunas, belum, kurang
            'total_bayar'=>$data->totalBayar,
            'kurang_bayar'=>$data->totalBayar,
        ]);
        // update saldo hutang pembelian
        $this->saldoHutangPembelianRepo->saldoIncrement($data->supplierId, $data->totalBayar);
        return $this->getDataById($hutangableType, $hutangableId);
    }

    public function rollback($hutangableType, $hutangableId)
    {
        $hutangPembelian = $this->getDataById($hutangableType, $hutangableId);
        $this->saldoHutangPembelianRepo->saldoDecrement($hutangPembelian->saldo_hutang_pembelian_id, $hutangPembelian->kurang_bayar);
        return $hutangPembelian;
    }

    public function destroy($hutangableType, $hutangableId)
    {
        return $this->rollback($hutangableType, $hutangableId)->delete();
    }
}
