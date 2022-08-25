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
                $query->increment('saldo', $dataItem['jumlah']);
                return $queryLatest;
            }
            return $this->queryCreate($gudangId, $kondisi, $tglInput, $dataItem);
        }
        // jika tidak create
        return $this->queryCreate($gudangId, $kondisi, $tglInput, $dataItem);
    }

    public function rollbackIn($persediaanId, $jumlah)
    {
        // initiate
        $query = $this->persediaan->newQuery()->find($persediaanId);
        $query->decrement('stock_masuk', $jumlah);
        return $query->decrement('saldo', $jumlah);
    }

    public function getStockOut($gudangId, $kondisi, $dataItem)
    {
        // check barang
        $query = $this->persediaan->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('gudang_id', $gudangId)
            ->where('jenis', $kondisi)
            ->where('produk_id', $dataItem['produk_id'])
            ->where('saldo', '>', 0)
            ->orderBy('tgl_input');
        $itemSum = $query->sum('saldo');
        $itemCount = $query->count();
        $itemJumlah = $dataItem['jumlah'];
        $returnItem = [];
        // check ketersediaan
        if ($itemSum >= $dataItem['jumlah']){
            // jika persediaan lebih besar dari yang diminta
            for ($x = 0; $x<$itemCount; $x++){
                $data = $query->get()[$x];
                $jumlah = ($itemJumlah <= $data->saldo) ? $itemJumlah : $data->saldo;
                $itemJumlah -= $data->saldo;
                $returnItem[] = [
                    'persediaan_id'=>$data->id, // persediaan id
                    'produk_id'=>$dataItem['produk_id'],
                    'harga'=>$data->harga,
                    'jumlah'=> $jumlah,
                    'sub_total'=>$data->harga * $jumlah
                ];
                if ($itemJumlah <= 0){
                    // jika item sudah dipenuhi, looping selesai
                    break;
                }
            }
        } else {
            // jika persediaan kurang dari yang diminta
            // barang lebihan stock akan diminuskan saldonya pada persediaan terakhie
            for ($x = 0; $x<=$itemCount; $x++){
                $data = $query->get()[$x];

                if ($x == $itemCount - 1){
                    // data terakhir break
                    $returnItem[] = [
                        'persediaan_id'=>$data->id, // persediaan id
                        'produk_id'=>$dataItem['produk_id'],
                        'harga'=>$data->harga,
                        'jumlah'=> $itemJumlah,
                        'tgl_input'=> $data->tgl_input,
                        'sub_total'=>$data->harga * $itemJumlah
                    ];
                    break;
                }
                $itemJumlah -= $data->saldo;
                $returnItem[] = [
                    'persediaan_id'=>$data->id, // persediaan id
                    'produk_id'=>$dataItem['produk_id'],
                    'harga'=>$data->harga,
                    'jumlah'=> $data->saldo,
                    'tgl_input'=> $data->tgl_input,
                    'sub_total'=> $data->harga * $data->saldo
                ];
            }
        }
        return $returnItem;
    }

    public function storeOut($persediaanId, $jumlah)
    {
        // check barang
        $query = $this->persediaan->newQuery()->find($persediaanId);
        $query->increment('stock_keluar', $jumlah);
        $query->decrement('saldo');
        return $query;
    }

    public function rollbackOut($persediaanId, $jumlah)
    {
        // check barang
        $query = $this->persediaan->newQuery()->find($persediaanId);
        $query->decrement('stock_keluar', $jumlah);
        $query->increment('saldo');
        return $query;
    }
}
