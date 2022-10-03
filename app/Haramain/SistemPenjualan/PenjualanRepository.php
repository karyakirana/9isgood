<?php namespace App\Haramain\SistemPenjualan;

use App\Models\Penjualan\Penjualan;

class PenjualanRepository
{
    public static function getKode()
    {
        $query = Penjualan::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()){
            return '0001/PJ/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PJ/".date('Y');
    }

    protected function kode()
    {
        return self::getKode();
    }

    public static function getDataById($penjualan_id)
    {
        return Penjualan::find($penjualan_id);
    }

    public static function store(array $data)
    {
        $data['kode'] = self::getKode();
        $data['active_cash'] = session('ClosedCash');
        $penjualan = Penjualan::create($data);
        $penjualan->penjualanDetail()->createMany($data['dataDetail']);
        return $penjualan->refresh();
    }

    public static function update(array $data)
    {
        $penjualan = self::getDataById($data['penjualan_id']);
        $penjualan->update($data);
        $penjualan->penjualanDetail()->createMany($data['dataDetail']);
        return $penjualan->refresh();
    }

    public static function rollback($penjualan_id)
    {
        // delete penjualan_detail
        $penjualan = self::getDataById($penjualan_id);
        $penjualan->penjualanDetail()->delete();
        return $penjualan->refresh();
    }

    public static function destroy($penjualan_id)
    {
        return self::rollback($penjualan_id)->delete();
    }
}
