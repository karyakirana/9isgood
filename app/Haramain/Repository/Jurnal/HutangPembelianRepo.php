<?php namespace App\Haramain\Repository\Jurnal;

use App\Haramain\Repository\Neraca\SaldoHutangPembelianRepo;
use App\Models\Keuangan\HutangPembelian;

class HutangPembelianRepo
{
    public $hutangPembelian;
    public $saldoHutangPembelianRepo;

    public function __construct()
    {
        $this->hutangPembelian = new HutangPembelian();
        $this->saldoHutangPembelianRepo = new SaldoHutangPembelianRepo();
    }

    public function store($data, $pembelianableType, $pembelianableId)
    {
        $hutangPembelian =  $this->hutangPembelian->newQuery()
            ->create([
                'saldo_hutang_pembelian_id'=>$data['supplierId'],
                'pembelian_type'=>$pembelianableType,
                'pembelian_id'=>$pembelianableId,
                'status_bayar'=>'belum', // lunas, belum, kurang
                'total_bayar'=>$data['totalBayar'],
                'kurang_bayar'=>$data['totalBayar'],
            ]);
        // update saldo hutang
        $this->saldoHutangPembelianRepo->saldoIncrement($data['supplierId'], $data['totalBayar']);
        return $hutangPembelian;
    }

    public function update($data, $pembelianableType, $pembelianableId)
    {
        $hutangPembelian = $this->hutangPembelian->newQuery()
            ->where('pembelian_type', $pembelianableType)
            ->where('pembelian_id', $pembelianableId)
            ->first();
        $hutangPembelianUpdate = $hutangPembelian->update([
            'saldo_hutang_pembelian_id'=>$data['supplierId'],
            'status_bayar'=>'belum', // lunas, belum, kurang
            'total_bayar'=>$data['totalBayar'],
            'kurang_bayar'=>$data['totalBayar'],
        ]);
        // update saldo hutang
        $this->saldoHutangPembelianRepo->saldoIncrement($data['supplierId'], $data['totalBayar']);
        return $hutangPembelian;
    }

    public function rollback($pembelianableType, $pembelianableId)
    {
        $hutangPembelian = $this->hutangPembelian->newQuery()
            ->where('pembelian_type', $pembelianableType)
            ->where('pembelian_id', $pembelianableId)
            ->first();
        $this->saldoHutangPembelianRepo->saldoIncrement($hutangPembelian->saldo_hutang_pembelian_id, $hutangPembelian->total_bayar);
        return $hutangPembelian;
    }
}
