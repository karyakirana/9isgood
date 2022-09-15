<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\PiutangPenjualan;

class PiutangPenjualanRepo
{
    protected $saldoPiutangPenjualanId;
    protected $jurnalSetPiutangAwalId;
    protected $piutangableType;
    protected $piutangableId;
    protected $statusBayar;
    protected $kurangBayar;

    protected function getDataById()
    {
        return PiutangPenjualan::query()
            ->where('penjualan_type', $this->piutangableType)
            ->where('penjualan_id', $this->piutangableId)
            ->first();
    }

    public function store()
    {
        return PiutangPenjualan::query()
            ->create([
                'saldo_piutang_penjualan_id'=>$this->saldoPiutangPenjualanId,
                'jurnal_set_piutang_awal_id'=>$this->jurnalSetPiutangAwalId,
                'penjualan_type'=>$this->piutangableType,
                'penjualan_id'=>$this->piutangableId,
                'status_bayar'=>$this->statusBayar, // enum ['lunas', 'belum', 'kurang']
                'kurang_bayar'=>$this->kurangBayar,
            ]);
    }

    public function update()
    {
        $this->getDataById()
            ->update([
                'saldo_piutang_penjualan_id'=>$this->saldoPiutangPenjualanId,
                'jurnal_set_piutang_awal_id'=>$this->jurnalSetPiutangAwalId,
                'penjualan_type'=>$this->piutangableType,
                'penjualan_id'=>$this->piutangableId,
                'status_bayar'=>$this->statusBayar, // enum ['lunas', 'belum', 'kurang']
                'kurang_bayar'=>$this->kurangBayar,
            ]);
        return $this->getDataById();
    }
}
