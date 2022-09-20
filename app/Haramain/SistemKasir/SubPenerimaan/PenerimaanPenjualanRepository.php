<?php namespace App\Haramain\SistemKasir\SubPenerimaan;

use App\Models\Keuangan\PenerimaanPenjualan;

class PenerimaanPenjualanRepository
{
    protected $penerimaanPenjualanId;
    protected $kode;
    protected $activeCash;
    protected $tglPenerimaan;
    protected $customerId;
    protected $akunKasId;
    protected $nominalKas;
    protected $akunPiutangId;
    protected $nominalPiutangId;

    protected $dataDetail;

    public function __construct()
    {
        $this->activeCash = session('ClosedCash');
    }

    public static function kode()
    {
        return null;
    }

    protected function getById()
    {
        return PenerimaanPenjualan::findOrFail($this->penerimaanPenjualanId);
    }

    public function store()
    {
        $penerimaanPenjualan = PenerimaanPenjualan::create([
            'active_cash'=>$this->activeCash,
            'kode'=>self::kode(),
            'tgl_penerimaan'=>$this->tglPenerimaan,
            'customer_id'=>$this->customerId,
            'akun_kas_id'=>$this->akunKasId,
            'nominal_kas'=>$this->nominalKas,
            'akun_piutang_id'=>$this->akunPiutangId,
            'nominal_piutang'=>$this->nominalPiutangId,
        ]);
        $penerimaanPenjualan->penerimaanPenjualanDetail()->createMany($this->storeDetail());
        return $penerimaanPenjualan->refresh();
    }

    protected function storeDetail()
    {
        $detail = [];
        foreach ($this->dataDetail as $detail)
        {
            $detail[] = [
                'piutang_penjualan_id'=>$detail['piutang_penjualan_id'],
                'nominal_dibayar'=>$detail['nominal_dibayar'],
                'kurang_bayar'=>$detail['kurang_bayar'],
            ];
        }
        return $detail;
    }
}
