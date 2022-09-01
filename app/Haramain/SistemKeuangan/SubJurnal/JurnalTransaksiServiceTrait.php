<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

trait JurnalTransaksiServiceTrait
{
    protected function rollbackJurnalAndSaldo($class)
    {
        $getJurnal = $this->jurnalTransaksiRepo->getData($class::class, $class->id);
        //dd($getJurnal);
        foreach ($getJurnal as $jurnal) {
            if ((int)$jurnal->nominal_debet > 0){
                $this->neracaSaldoRepo->debetRollback($jurnal->akun_id, $jurnal->nominal_debet);
            }
            if ((int)$jurnal->nominal_kredit > 0){
                $this->neracaSaldoRepo->kreditRollback($jurnal->akun_id, $jurnal->nominal_kredit);
            }
        }
        return $this->jurnalTransaksiRepo->rollback($class::class, $class->id);
    }
}
