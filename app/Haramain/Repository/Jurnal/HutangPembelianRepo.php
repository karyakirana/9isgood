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
    }

    public function store($data, $pembelianableType, $pembelianableId)
    {
        $data = (object) $data;
        $hutangPembelian =  $this->hutangPembelian->newQuery()
            ->create([
                'saldo_hutang_pembelian_id'=>$data->supplier_id,
                'pembelian_type'=>$pembelianableType,
                'pembelian_id'=>$pembelianableId,
                'status_bayar'=>'belum', // lunas, belum, kurang
                'total_bayar'=>$data->totalBayar,
                'kurang_bayar'=>$data->totalBayar,
            ]);
        // update saldo hutang
        (new SaldoHutangPembelianRepo($data->supplierId))->saldoIncrement($data->totalBayar);
        return $hutangPembelian;
    }

    public function storeByMorph($classMorph, $data)
    {
        $data = (object) $data;
        $hutangPembelian =  $classMorph
            ->create([
                'saldo_hutang_pembelian_id'=>$data->supplier_id,
                'status_bayar'=>'belum', // lunas, belum, kurang
                'total_bayar'=>$data->totalBayar,
                'kurang_bayar'=>$data->totalBayar,
            ]);
        // update saldo hutang
        (new SaldoHutangPembelianRepo($data->supplierId))->saldoIncrement($data->totalBayar);
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
        (new SaldoHutangPembelianRepo($data->supplierId))->saldoIncrement($data->totalBayar);
        return $hutangPembelian;
    }

    public function updateByMorph($classMorph, $data)
    {
        //
    }

    public function rollback($pembelianableType, $pembelianableId)
    {
        $hutangPembelian = $this->hutangPembelian->newQuery()
            ->where('pembelian_type', $pembelianableType)
            ->where('pembelian_id', $pembelianableId)
            ->first();
        (new SaldoHutangPembelianRepo($hutangPembelian->saldo_hutang_pembelian_id))->saldoIncrement($hutangPembelian->total_bayar);
        return $hutangPembelian;
    }
}
