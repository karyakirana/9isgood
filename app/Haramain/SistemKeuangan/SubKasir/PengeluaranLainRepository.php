<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\PengeluaranLain;

class PengeluaranLainRepository
{
    public static function kode()
    {
        $query = PengeluaranLain::where('active_cash', session('ClosedCash'))
            ->latest('kode');
        if ($query->doesntExist()) {
            return '00001/PL/'. date('Y');
        }
        $num = (int)$query->first()->last_num_char + 1 ?? 1;
        return sprintf("%05s", $num) . "/TL/" . date('Y');
    }

    public static function getDataById($pengeluaran_lain_id)
    {
        return PengeluaranLain::find($pengeluaran_lain_id);
    }

    public static function store(array $data)
    {
        $data['active_cash'] = session('ClosedCash');
        $data['kode'] = self::kode();
        $pengeluaranLain = PengeluaranLain::create($data);
        $pengeluaranLain->pengeluaranLainDetail()->createMany($data['dataDetail']);
        $pengeluaranLain->paymentable()->createMany($data['dataPayment']);
        return $pengeluaranLain->refresh();
    }

    public static function update(array $data)
    {
        $pengeluaranLain = PengeluaranLain::find($data['pengeluaran_lain_id']);
        $pengeluaranLain->pengeluaranLainDetail()->createMany($data['dataDetail']);
        $pengeluaranLain->paymentable()->createMany($data['dataPayment']);
        return $pengeluaranLain->refresh();
    }

    public static function rollback($pengeluaran_lain_id)
    {
        $pengeluaran_lain = PengeluaranLain::find($pengeluaran_lain_id);
        $pengeluaran_lain->pengeluaranLainDetail()->delete();
        $pengeluaran_lain->paymentable()->delete();
        return $pengeluaran_lain->refresh();
    }

    public static function destroy($pengeluaran_lain_id)
    {
        return self::rollback($pengeluaran_lain_id)->delete();
    }
}
