<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\PenerimaanLain;

class PenerimaanLainRepository
{
    public static function kode()
    {
        $query = PenerimaanLain::where('active_cash', session('ClosedCash'))
            ->latest('kode');
        if ($query->doesntExist()) {
            return '00001/PL/'. date('Y');
        }
        $num = (int)$query->first()->last_num_char + 1 ?? 1;
        return sprintf("%05s", $num) . "/PL/" . date('Y');
    }

    public static function getById($penerimaan_lain_id)
    {
        return PenerimaanLain::find($penerimaan_lain_id);
    }

    public static function store($data)
    {
        $data['active_cash'] = session('ClosedCash');
        $data['kode'] = self::kode();
        $penerimaanLain = PenerimaanLain::create($data);
        $penerimaanLain->penerimaanLainDetail()->createMany($data['dataDetail']);
        $penerimaanLain->paymentable()->createMany($data['dataPayment']);
        return $penerimaanLain->refresh();
    }

    public static function update($data)
    {
        $penerimaanLain = PenerimaanLain::find($data['penerimaan_lain_id']);
        $penerimaanLain->update($data);
        $penerimaanLain->penerimaanLainDetail()->createMany($data['dataDetail']);
        $penerimaanLain->paymentable()->createMany($data['dataPayment']);
        return $penerimaanLain->refresh();
    }

    public static function rollback($penerimaan_lain_id)
    {
        $penerimaanLain = PenerimaanLain::find($penerimaan_lain_id);
        $penerimaanLain->penerimaanLainDetail()->delete();
        $penerimaanLain->paymentable()->delete();
        return $penerimaanLain->refresh();
    }

    public static function destroy($penerimaan_lain_id)
    {
        return self::rollback($penerimaan_lain_id)->delete();
    }
}
