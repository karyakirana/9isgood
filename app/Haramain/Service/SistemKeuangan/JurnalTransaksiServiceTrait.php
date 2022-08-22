<?php namespace App\Haramain\Service\SistemKeuangan;

trait JurnalTransaksiServiceTrait
{
    // jurnal transaksi variabel

    protected function storeDebet($akunDebetId, $nominal)
    {
        return $this->jurnalTransaksi->create([
            'akun_id'=>$akunDebetId,
            'nominal_debet'=>$nominal
        ]);
    }

    protected function storeKredit($akunKreditId, $nominal)
    {
        return $this->jurnalTransaksi->create([
            'akun_id'=>$akunKreditId,
            'nominal_debet'=>$nominal
        ]);
    }
}
