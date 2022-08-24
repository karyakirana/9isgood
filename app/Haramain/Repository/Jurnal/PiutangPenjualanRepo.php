<?php namespace App\Haramain\Repository\Jurnal;

use App\Haramain\Repository\Neraca\SaldoPiutangPenjualanRepo;
use App\Models\Keuangan\PiutangPenjualan;

class PiutangPenjualanRepo
{
    protected $piutangPenjualan;
    protected $saldoPiutangPenjualanRepo;

    public function __construct()
    {
        $this->piutangPenjualan = new PiutangPenjualan();
        $this->saldoPiutangPenjualanRepo = new SaldoPiutangPenjualanRepo();
    }

    public function getData($penjualanableType, $penjualanableId)
    {
        return $this->piutangPenjualan->newQuery()
            ->where('penjualan_type', $penjualanableType)
            ->where('penjualan_id', $penjualanableId)
            ->first();
    }

    public function store($data, $penjualanableType, $penjualanableId, $jurnalSetPiutangAwalId = null)
    {
        $piutangPenjualan = $this->piutangPenjualan->newQuery()
            ->create([
                'saldo_piutang_penjualan_id'=>$data['customerId'],
                'jurnal_set_piutang_awal_id'=>$jurnalSetPiutangAwalId,
                'penjualan_type'=>$penjualanableType,
                'penjualan_id'=>$penjualanableId,
                'status_bayar'=>$data['statusBayar'], // enum ['lunas', 'belum', 'kurang']
                'kurang_bayar'=>$data['totalBayar'],
            ]);
        // update saldo piutang penjualan
        $this->saldoPiutangPenjualanRepo->increment($data['customerId'], $data['totalBayar']);
        return $piutangPenjualan;
    }

    public function update($data, $penjualanableType, $penjualanableId, $jurnalSetPiutangAwalId = null)
    {
        $piutangPenjualan = $this->getData($penjualanableType, $penjualanableId);
        $update = $piutangPenjualan->update([
                'saldo_piutang_penjualan_id'=>$data['customerId'],
                'jurnal_set_piutang_awal_id'=>$jurnalSetPiutangAwalId,
                'status_bayar'=>$data['statusBayar'], // enum ['lunas', 'belum', 'kurang']
                'kurang_bayar'=>$data['totalBayar'],
        ]);
        // update saldo piutang penjualan
        $this->saldoPiutangPenjualanRepo->increment($data['customerId'], $data['totalBayar']);
        return $piutangPenjualan;
    }

    public function rollback($penjualanableType, $penjualanableId)
    {
        $piutangPenjualan = $this->getData($penjualanableType, $penjualanableId);
        $this->saldoPiutangPenjualanRepo->decrement($piutangPenjualan->saldo_piutang_penjualan_id, $piutangPenjualan->kurang_bayar);
        return $piutangPenjualan;
    }

    public function destroy($penjualanableType, $penjualanableId)
    {
        return $this->rollback($penjualanableType, $penjualanableId)->delete();
    }
}
