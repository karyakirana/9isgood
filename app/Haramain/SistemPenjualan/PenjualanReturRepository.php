<?php namespace App\Haramain\SistemPenjualan;

use App\Models\Penjualan\PenjualanRetur;

class PenjualanReturRepository
{
    public static function kode($kondisi)
    {
        // query
        $query = PenjualanRetur::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis_retur', $kondisi)
            ->latest('kode');

        $kode = ($kondisi == 'baik') ? 'RB' : 'RR';

        // check last num
        if ($query->doesntExist()){
            return "0001/$kode/".date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/$kode/".date('Y');
    }

    public static function getDataById($penjualan_retur_id)
    {
        return PenjualanRetur::query()->find($penjualan_retur_id);
    }

    public static function store(array $data)
    {
        $data['kode'] = self::kode($data['kondisi']);
        $data['active_cash'] = session('ClosedCash');
        $penjualanRetur = PenjualanRetur::create($data);
        $penjualanRetur->returDetail()->createMany($data['dataDetail']);
        return $penjualanRetur->refresh();
    }

    public static function update(array $data)
    {
        $penjualanRetur = self::getDataById($data['penjualan_retur_id']);
        $penjualanRetur->update($data);
        $penjualanRetur = $penjualanRetur->refresh();
        $penjualanRetur->returDetail()->createMany($data['dataDetail']);
        return $penjualanRetur->refresh();
    }

    public static function rollback($penjualan_retur_id)
    {
        $penjualanRetur = self::getDataById($penjualan_retur_id);
        $penjualanRetur->returDetail()->delete();
        return $penjualanRetur->refresh();
    }

    public static function destroy($penjualan_retur_id)
    {
        return self::rollback($penjualan_retur_id)->delete();
    }
}
