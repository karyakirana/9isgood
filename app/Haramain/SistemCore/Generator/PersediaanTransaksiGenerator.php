<?php namespace App\Haramain\SistemCore\Generator;

use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Penjualan\Penjualan;

class PersediaanTransaksiGenerator
{
    /**
     * Generator Persediaan Transaksi
     * Barang Awal :
     * dari stock opname
     * dari barang masuk :
     * pembelian eksternal
     * pembelian internal
     * retur penjualan
     * dari barang keluar :
     * penjualan
     * retur pembelian external
     * retur pembelian internal
     */

    public function handleFromPenjualan()
    {
        // get data penjualan
        $penjualanData = Penjualan::query()->where('active_cash', session('ClosedCash'))->get();
        // each data penjualan
        foreach ($penjualanData as $penjualan){
            // check persediaan transaksi
            $persediaanTransaksi = $penjualan->persediaanTransaksi()->first();
            if ($persediaanTransaksi != null){
                // create
            }
        }
        // update or create jurnal transaksi
    }

    protected function persediaanTransaksiStore($class)
    {
        $queryPersediaanTransaksi = PersediaanTransaksi::query()
            ->where('persediaan_type', $class::class)
            ->where('persediaan_id', $class->id);
        if ($queryPersediaanTransaksi->first() == null)
        {
            // create
        }
        // return update
    }

    protected function persediaanTransaksiCreate($class)
    {
        //
    }
}
