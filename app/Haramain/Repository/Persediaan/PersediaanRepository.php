<?php namespace App\Haramain\Repository\Persediaan;

use App\Models\Keuangan\Persediaan;

class PersediaanRepository
{
    // initiate
    protected $persediaan;

    public function __construct()
    {
        $this->persediaan = new Persediaan();
    }

    protected function query($gudangId, $kondisi,$dataItem)
    {
        return $this->persediaan->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('gudang_id', $gudangId)
            ->where('jenis', $kondisi)
            ->where('produk_id', $dataItem['produk_id'])
            ->where('harga', $dataItem['harga']);
    }

    protected function queryCreate($gudangId, $kondisi, $tglInput, $dataItem)
    {
        return $this->persediaan->newQuery()->create([
            'active_cash'=>session('ClosedCash'),
            'jenis'=>$kondisi,// baik or buruk
            'tgl_input'=>$tglInput,
            'gudang_id'=>$gudangId,
            'produk_id'=>$dataItem['produk_id'],
            'harga'=>$dataItem['harga'],
            'stock_masuk'=>$dataItem['jumlah'],
            'stock_keluar'=>0,
            'saldo'=>$dataItem['jumlah'],
        ]);
    }

    public function checkException()
    {
        // check apakah barang ada atau tidak pada persediaan
        // kepentingan stock keluar
    }

    public function storeIn($gudangId, $kondisi, $tglInput, $dataItem)
    {
        // check barang
        $query = $this->query($gudangId, $kondisi, $dataItem);
        // jika ada update
        if ($query->exists()){
            $queryLatest = $query->latest('tgl_input')->first();
            // check tanggal
            if ($queryLatest->harga == $dataItem['harga'])
            {
                // jika harga terakhir sama dengan harga input sekarang, maka persediaan nambah
                $query->increment('stock_masuk', $dataItem['jumlah']);
                return $query->increment('saldo', $dataItem['jumlah']);
            }
            return $this->queryCreate($gudangId, $kondisi, $tglInput, $dataItem);
        }
        // jika tidak create
        return $this->queryCreate($gudangId, $kondisi, $tglInput, $dataItem);
    }

    public function rollbackIn($gudangId, $kondisi, $tglInput, $dataItem)
    {
        // initiate
        $query = $this->query($gudangId, $kondisi, $dataItem)->where('tgl_input', $tglInput);
        $query->decrement('stock_masuk', $dataItem->jumlah);
        return $query->decrement('saldo', $dataItem->jumlah);
    }

    public function getStockOut($gudangId, $kondisi, $dataItem)
    {
        // check barang
        $query = $this->query($gudangId, $kondisi, $dataItem)
            ->where('saldo', '>', 0)
            ->oldest('tgl_input');
        $itemSum = $query->sum('saldo');
        $itemCount = $query->count();
        $itemJumlah = $dataItem['jumlah'];
        $returnItem = [];
        // check ketersediaan
        if ($itemSum >= $dataItem['jumlah']){
            // jika persediaan lebih besar dari yang diminta
            for ($x = 0; $x<$itemCount; $x++){
                $data = $query->get($x);
                $itemJumlah -= $data->saldo;
                $returnItem[] = [
                    'produk_id'=>$dataItem['produk_id'],
                    'harga'=>$dataItem['harga'],
                    'jumlah'=> ($itemJumlah <= 0) ? $data->saldo : abs($itemJumlah)
                ];
                if ($itemJumlah <= 0){
                    // jika item sudah dipenuhi, looping selesai
                    break;
                }
            }
        } else {
            // jika persediaan kurang dari yang diminta
            // barang lebihan stock akan diminuskan saldonya pada persediaan terakhie
            for ($x = 0; $x<$itemCount; $x++){
                $data = $query->get($x);
                $itemJumlah -= $data->saldo;
                if ($x = $itemCount - 1){
                    // data terakhir break
                    $returnItem[] = [
                        'produk_id'=>$dataItem['produk_id'],
                        'harga'=>$dataItem['harga'],
                        'jumlah'=> $itemJumlah
                    ];
                    break;
                }
                $returnItem[] = [
                    'produk_id'=>$dataItem['produk_id'],
                    'harga'=>$dataItem['harga'],
                    'jumlah'=> $data->saldo
                ];
            }
        }
        return $returnItem;
    }

    public function storeOut($gudangId, $kondisi, $dataItemOut)
    {
        // check barang
        $query = $this->query($gudangId, $kondisi, $dataItemOut);
        $query->increment('stock_keluar', $dataItemOut['jumlah']);
        return $query->decrement('saldo');
    }

    public function rollbackOut()
    {
        //
    }
}
