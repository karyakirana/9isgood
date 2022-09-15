<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\HutangPembelian;

class HutangPembelianRepo
{
    protected $saldoHutangPembelianId;
    protected $hutangableType;
    protected $hutangableId;
    protected $statusBayar;
    protected $kurangBayar;

    public static function build(...$params)
    {
        return new static(...$params);
    }

    public function getDataById()
    {
        return HutangPembelian::query()
            ->where('pembelian_type', $this->hutangableType)
            ->where('pembelian_id', $this->hutangableId)
            ->first();
    }

    public function store()
    {
        return HutangPembelian::query()
            ->create([
                'saldo_hutang_pembelian_id'=>$this->saldoHutangPembelianId,
                'pembelian_type'=>$this->hutangableType,
                'pembelian_id'=>$this->hutangableId,
                'status_bayar'=>$this->statusBayar, // lunas, belum, kurang
                'total_bayar'=>$this->kurangBayar,
                'kurang_bayar'=>$this->kurangBayar,
            ]);
    }

    public function update()
    {
        $this->getDataById()->update([
            'saldo_hutang_pembelian_id'=>$this->saldoHutangPembelianId,
            'pembelian_type'=>$this->hutangableType,
            'pembelian_id'=>$this->hutangableId,
            'status_bayar'=>$this->statusBayar, // lunas, belum, kurang
            'total_bayar'=>$this->kurangBayar,
            'kurang_bayar'=>$this->kurangBayar,
        ]);
        return $this->getDataById();
    }
}
