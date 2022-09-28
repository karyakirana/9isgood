<?php

namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\PenerimaanPenjualan;
use App\Models\Keuangan\PiutangPenjualan;

class PenerimaanPenjualanRepository
{
    public static function getKode()
    {
        $query = PenerimaanPenjualan::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');
        if ($query->doesntExist()) {
            return '00001/PP/'. date('Y');
        }
        $num = (int)$query->first()->last_num_char + 1 ;
        return sprintf("%05s", $num) . "/PP/" . date('Y');
    }

    protected static function setData(array $data)
    {
        $dataPenerimaan = $data;
        $dataPenerimaan['active_cash'] = session('ClosedCash');
        $dataPenerimaan['kode'] = self::getKode();
        $dataPenerimaan['user_id'] = auth()->id();
        $dataPayment = $dataPenerimaan['dataPayment'];
        unset($dataPenerimaan['dataPayment']);
        $dataPenerimaanPenjualanDetail = $dataPenerimaan['dataDetail'];
        unset($dataPenerimaan['dataDetail']);
        return [
            'dataPenerimaan'=>$dataPenerimaan,
            'dataPenerimaanPenjualanDetail'=>$dataPenerimaanPenjualanDetail,
            'dataPayment'=>$dataPayment
        ];
    }

    public static function store(array $data)
    {
        $data = self::setData($data);
        $penerimaanPenjualan = PenerimaanPenjualan::create($data['dataPenerimaan']);
        $penerimaanPenjualan->penerimaanPenjualanDetail()->createMany($data['dataPenerimaanPenjualanDetail']);
        $penerimaanPenjualan->paymentable()->createMany($data['dataPayment']);
        return $penerimaanPenjualan;
    }

    public static function update(array $data)
    {
        $data = self::setData($data);
        $penerimaanPenjualan = PenerimaanPenjualan::find($data['dataPenerimaan']['penerimaan_penjualan_id']);
        unset($data['dataPenerimaan']['kode']);
        $penerimaanPenjualan->update($data['dataPenerimaan']);
        $penerimaanPenjualan->penerimaanPenjualanDetail()->createMany($data['dataPenerimaanPenjualanDetail']);
        $penerimaanPenjualan->paymentable()->createMany($data['dataPayment']);
        self::updatePiutangPenjualan($data['dataPenerimaanPenjualanDetail']);
        return $penerimaanPenjualan->refresh();
    }

    protected static function updatePiutangPenjualan(array $dataPenerimaanPenjualanDetail)
    {
        foreach ($dataPenerimaanPenjualanDetail as $item) {
            $piutangPenjualan = PiutangPenjualan::find($item['piutang_penjualan_id']);
            $statusBayar = ($item['kurang_bayar'] == 0) ? 'lunas' : 'kurang';
            $piutangPenjualan->update([
                'status_bayar'=>$statusBayar,
                'kurang_bayar'=>$item['kurang_bayar']
            ]);
            $piutangPenjualan->piutangablePenjualan()->update(['status_bayar'=>$statusBayar]);
        }
    }

    public static function rollback($penerimaanPenjualanId)
    {
        $penerimaanPenjualan = PenerimaanPenjualan::find($penerimaanPenjualanId);
        $penerimaanPenjualan->paymentable()->delete();
        foreach ($penerimaanPenjualan->penerimaanPenjualanDetail as $penerimaanPenjualanDetail) {
            PiutangPenjualanRollback::fromPenerimaanPenjualan($penerimaanPenjualanDetail);
        }
        $penerimaanPenjualan->penerimaanPenjualanDetail()->delete();
        return $penerimaanPenjualan;
    }

    public static function destroy(PenerimaanPenjualan $penerimaanPenjualan)
    {
        return self::rollback($penerimaanPenjualan)->delete();
    }
}
