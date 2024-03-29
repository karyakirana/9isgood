<?php namespace App\Haramain\Service\SistemKeuangan\Jurnal;

use App\Models\Keuangan\Persediaan;

class PersediaanRepository
{
    protected $persediaan;

    public function __construct()
    {
        $this->persediaan = new Persediaan();
    }

    protected function queryPersediaan($dataItem, $kondisi, $gudangId)
    {
        return $this->persediaan->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $kondisi)
            ->where('gudang_id', $gudangId)
            ->where('produk_id', $dataItem['produk_id'])
            ->where('harga', $dataItem['harga']);
    }

    public function getPersediaanToOut($dataDetail, $kondisi, $gudangId)
    {
        // get data by produk_id
        $persediaan = Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $kondisi)
            ->where('gudang_id', $gudangId)
            ->where('produk_id', $dataDetail['produk_id']);
        $persediaanSum = $persediaan->sum('jumlah');
        $persediaanCount = $persediaan->count();
        // get persediaan
        $persediaanGet = $persediaan->oldest('tgl_input')->get();
        // loop persediaan
        $setData = [];
        $jumlahProduk = $dataDetail['jumlah'];
        for ($count = 0; $count < $persediaanCount; $count++){
            $jumlahField = $persediaanGet[$count]->stock_saldo;
            $hargaField = $persediaanGet[$count]->harga;
            if ($jumlahProduk > $jumlahField){
                // continue
                $setData[] = [
                    'produk_id'=>$dataDetail['produk_id'],
                    'jumlah'=>$jumlahField,
                    'harga_persediaan'=>$hargaField
                ];
                continue;
            }
            // break
            $setData[] = [
                'produk_id'=>$dataDetail['produk_id'],
                'jumlah'=>$dataDetail['jumlah'],
                'harga_persediaan'=>$hargaField
            ];
            break;
        }
        return $setData;
    }

    public function updateStockIn($dataItem, $field, $tglInput, $gudangId, $kondisi)
    {
        $persediaan = $this->queryPersediaan($dataItem, $kondisi, $gudangId);
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

    public function updateStockOut($dataItem, $field, $gudangId, $kondisi)
    {
        $persediaan = $this->queryPersediaan($dataItem, $kondisi, $gudangId);
        $persediaan->increment($field, $dataItem['jumlah']);
        return $persediaan->decrement('stock_saldo', $dataItem['jumlah']);
    }

    public function rollbackStockIn($dataItem, $field, $gudangId,$kondisi)
    {
        $persediaan = $this->queryPersediaan($dataItem, $kondisi, $gudangId);
        $persediaan->decrement($field, $dataItem['jumlah']);
        return $persediaan->decrement('stock_saldo', $dataItem['jumlah']);
    }

    public function rollbackStockOut($dataItem, $field, $gudangId, $kondisi)
    {
        $persediaan = $this->queryPersediaan($dataItem, $kondisi, $gudangId);
        $persediaan->decrement($field, $dataItem['jumlah']);
        return $persediaan->increment('stock_saldo', $dataItem['jumlah']);
    }

    public function checkStockByItem($dataItem, $kondisi, $gudangId)
    {
        $persediaan = Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('gudang_id', $gudangId)
            ->where('produk_id', $dataItem['produk_id'])
            ->where('jenis', $kondisi);
        if ($persediaan->doesntExist() || $persediaan->sum('stock_saldo') < $dataItem['jumlah']){
            return 0;
        }
        return 1;
    }

    public function handleExceptionOut($dataDetail, $kondisi, $gudangId)
    {
        // check semua item keluar
        $count = count($dataDetail);
        // jika item lebih dari persediaan maka akan menghasilkan exception
        $a = 0;
        foreach ($dataDetail as $item) {
            $a += $this->checkStockByItem($item, $kondisi, $gudangId);
        }
        // jika salah satu item tidak ada atau kurang data
        // maka false
        if ($count < $a){
            return false;
        }
        return true;
    }
}
