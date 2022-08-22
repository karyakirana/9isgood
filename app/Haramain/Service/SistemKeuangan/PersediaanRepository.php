<?php namespace App\Haramain\Service\SistemKeuangan;

use App\Models\Keuangan\Persediaan;

class PersediaanRepository
{
    protected $persediaan;

    public function __construct()
    {
        $this->persediaan = new Persediaan();
    }

    public function storeIn($dataItem, $kondisi, $tglInput, $gudangId)
    {
        return $this->persediaan->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jenis'=>$kondisi,// baik or buruk
                'tgl_input'=>$tglInput,
                'gudang_id'=>$gudangId,
                'produk_id'=>$dataItem['produk_id'],
                'harga'=>$dataItem['harga'],
                'stock_masuk'=>$dataItem['jumlah'],
            ]);
    }

    public function storeOut($dataItem)
    {
        // update berdasarkan persediaan_id
        return $this->persediaan->newQuery()
            ->find($dataItem['persediaan_id'])
            ->increment('stock_keluar', $dataItem['jumlah']);
    }
}
