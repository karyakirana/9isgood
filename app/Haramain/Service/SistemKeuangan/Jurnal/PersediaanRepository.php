<?php namespace App\Haramain\Service\SistemKeuangan\Jurnal;

use App\Models\Keuangan\Persediaan;

class PersediaanRepository
{
    protected $persediaan;

    public function __construct()
    {
        $this->persediaan = new Persediaan();
    }

    protected function queryPersediaan($dataItem)
    {
        return $this->persediaan->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('produk_id', $dataItem['produk_id'])
            ->where('harga', $dataItem['harga']);
    }

    public function getPersediaanToOut($dataDetail)
    {
        // get data by produk_id
        $persediaan = Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('produk_id', $dataItem['produk_id']);
        $persediaanSum = $persediaan->sum('jumlah');
        $persediaanCount = $persediaan->count();
        // get persediaan
        $persediaanGet = $persediaan->oldest('tgl_input')->get();
        // loop persediaan
        $setData = [];
        $jumlahProduk = $dataItem['jumlah'];
        for ($count = 0; $count < $persediaanCount; $count++){
            $jumlahField = $persediaanGet[$count]->stock_saldo;
            $hargaField = $persediaanGet[$count]->harga;
            if ($jumlahProduk > $jumlahField){
                // continue
                $setData[] = [
                    'produk_id'=>$dataItem['produk_id'],
                    'jumlah'=>$jumlahField,
                    'harga_persediaan'=>$hargaField
                ];
                continue;
            }
            // break
            $setData[] = [
                'produk_id'=>$dataItem['produk_id'],
                'jumlah'=>$dataItem['jumlah'],
                'harga_persediaan'=>$hargaField
            ];
            break;
        }
        return $setData;
    }

    public function updateStockIn($dataItem, $field, $tglInput, $gudangId, $kondisi)
    {
        $persediaan = $this->queryPersediaan($dataItem);
        if ($persediaan->exists()){
            // update persediaan
            $persediaan->increment($field, $dataItem['jumlah']);
            return $persediaan->increment('stock_saldo', $dataItem['jumlah']);
        }
        return $this->persediaan->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'tgl_input'=>$tglInput,
                'gudang_id'=>$gudangId,
                'jenis'=>$kondisi,
                'harga'=>$dataItem['harga'],
                $field => $dataItem['jumlah'],
                'stock_saldo'=>$dataItem['jumlah']
            ]);
    }

    public function updateStockOut($dataItem, $field)
    {
        $persediaan = $this->queryPersediaan($dataItem);
        $persediaan->increment($field, $dataItem['jumlah']);
        return $persediaan->decrement('stock_saldo', $dataItem['jumlah']);
    }

    public function rollbackStockIn($dataItem, $field)
    {
        $persediaan = $this->queryPersediaan($dataItem);
        $persediaan->decrement($field, $dataItem['jumlah']);
        return $persediaan->decrement('stock_saldo', $dataItem['jumlah']);
    }

    public function rollbackStockOut($dataItem, $field)
    {
        $persediaan = $this->queryPersediaan($dataItem);
        $persediaan->decrement($field, $dataItem['jumlah']);
        return $persediaan->increment('stock_saldo', $dataItem['jumlah']);
    }
}
