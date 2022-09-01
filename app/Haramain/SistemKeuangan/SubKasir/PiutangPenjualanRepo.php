<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\SistemKeuangan\SubNeraca\SaldoPiutangPenjualanRepo;
use App\Models\Keuangan\PiutangPenjualan;

class PiutangPenjualanRepo
{
    protected $saldoPiutangPenjualanRepo;

    public function __construct()
    {
        $this->saldoPiutangPenjualanRepo = new SaldoPiutangPenjualanRepo();
    }

    public function getDataById($piutangableType, $piutangableId)
    {
        return PiutangPenjualan::query()
            ->where('penjualan_type', $piutangableType)
            ->where('penjualan_id', $piutangableId)
            ->first();
    }

    public function getDataAll()
    {
        return PiutangPenjualan::all();
    }

    public function store($data, $piutangableType, $piutangableId, $jurnalSetPiutangAwalId = null)
    {
        $data = (object) $data;
        $piutangPenjualan = PiutangPenjualan::query()
            ->create([
                'saldo_piutang_penjualan_id'=>$data->customerId,
                'jurnal_set_piutang_awal_id'=>$jurnalSetPiutangAwalId,
                'penjualan_type'=>$piutangableType,
                'penjualan_id'=>$piutangableId,
                'status_bayar'=>$data->statusBayar, // enum ['lunas', 'belum', 'kurang']
                'kurang_bayar'=>$data->totalBayar,
            ]);
        // update saldo piutang penjualan
        $this->saldoPiutangPenjualanRepo->penjualan($data->customerId, $data->totalBayar);
        return $piutangPenjualan;
    }

    public function update($data, $piutangableType, $piutangableId, $jurnalSetPiutangAwalId = null)
    {
        $data = (object) $data;
        $this->getDataById($piutangableType, $piutangableId)
            ->update([
                'saldo_piutang_penjualan_id'=>$data->customerId,
                'jurnal_set_piutang_awal_id'=>$jurnalSetPiutangAwalId,
                'status_bayar'=>$data->statusBayar, // enum ['lunas', 'belum', 'kurang']
                'kurang_bayar'=>$data->totalBayar,
            ]);
        // update saldo piutang penjualan
        $this->saldoPiutangPenjualanRepo->penjualan($data->customerId, $data->totalBayar);
        return $this->getDataById($piutangableType, $piutangableId);
    }

    public function rollback($piutangableType, $piutangableId)
    {
        $piutangPenjualan = $this->getDataById($piutangableType, $piutangableId);
        $this->saldoPiutangPenjualanRepo->penjualanRollback($piutangPenjualan->saldo_piutang_penjualan_id, $piutangPenjualan->kurang_bayar);
        return $piutangPenjualan;
    }

    public function destroy($piutangableType, $piutangableId)
    {
        return $this->rollback($piutangableType, $piutangableId)->delete();
    }
}
