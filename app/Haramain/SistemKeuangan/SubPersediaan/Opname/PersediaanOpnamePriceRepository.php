<?php namespace App\Haramain\SistemKeuangan\SubPersediaan\Opname;

use App\Models\Keuangan\PersediaanOpnamePrice;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PersediaanOpnamePriceRepository
{
    // todo get data
    public static function getData($kondisi, $produkId)
    {
        $price = PersediaanOpnamePrice::where('active_cash', session('ClosedCash'))
            ->where('kondisi', $kondisi)
            ->where('produk_id', $produkId);
        if ($price->doesntExist()){
            throw new ModelNotFoundException('Data Harga Tidak Ada');
        }
        return $price->latest('tgl_input')->first();
    }
    // todo store data
}
