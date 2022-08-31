<?php namespace App\Haramain\SistemPenjualan;

trait PenjualanServiceTrait
{
    protected function jurnalRollback($penjualan)
    {
        $getDataJurnal = $this->jurnalTransaksiRepo->getData($penjualan::class, $penjualan->id);
        foreach ($getDataJurnal as $jurnal) {
            if ((int) $jurnal->nominal_debet > 0){
                $this->neracaSaldoRepository->debetRollback($jurnal->akun_id, $jurnal->nominal_debet);
            }
            if ((int) $jurnal->nominal_kredit > 0){
                $this->neracaSaldoRepository->kreditRollback($jurnal->akun_id, $jurnal->nominal_kredit);
            }
        }
        $this->jurnalTransaksiRepo->rollback($penjualan::class, $penjualan->id);
    }
}
