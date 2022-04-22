<?php namespace App\Haramain\Repository\Persediaan;

use App\Models\Keuangan\Persediaan;

class PersediaanRepository
{
    /**
     * get data from persediaan table
     * digunakan untuk menyimpan pada transaksi persediaan transaksi
     * @param $produk_id
     * @param $gudang_id
     * @param $jumlah
     * @return array
     */
    public function getProdukForMutasi($produk_id, $gudang_id, $jumlah)
    {
        $query = Persediaan::query()
            ->where('produk_id', $produk_id)
            ->where('active_cash', session('ClosedCash'))
            ->where('gudang_id', $gudang_id);
        $queryGet = $query->get();

        $data = [];

        // check jumlah produk yang ada
        $count = $query->count();

        $i = $jumlah;
        for ($y = 0; $y < $count ;$y++){
            if ($i < $queryGet[$y]->stock_saldo){
                $data [] = (object) [
                    'produk_id'=>$queryGet[$y]->produk_id,
                    'harga'=>$queryGet[$y]->harga,
                    'jumlah'=>$i
                ];
                break;
            } else {
                // jika stock saldo adalah 0
                // maka dilewati (tidak ada proses)
                if ($queryGet[$y]->stock_saldo <= 0){
                    continue;
                }
                // jika data terakhir dan masih ada sisa produk
                // maka semua barang akan menjadi saldo_keluar
                if ($y == $count-1){
                    $data [] = (object) [
                        'produk_id'=>$queryGet[$y]->produk_id,
                        'harga'=>$queryGet[$y]->harga,
                        'jumlah'=>$i
                    ];
                }
                $data[] = (object)[
                    'produk_id'=>$queryGet[$y]->produk_id,
                    'harga'=>$queryGet[$y]->harga,
                    'jumlah'=>$queryGet[$y]->stock_saldo,
                ];
            }
        }
        return $data;
    }

    public function getProdukForKeluar($produk_id, $gudang_id, $jumlah, $kondisi = 'baik'): array
    {
        // initiate
        $query = Persediaan::query()
            ->where('gudang_id', $gudang_id)
            ->where('produk_id', $produk_id)
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $kondisi);

        $hasil = [];

        // check exist
        if ($query->doesntExist()){
            $hasil[] = (object)[
                'produk_id'=>'kosong',
                'harga'=>'kosong',
                'jumlah'=>'kosong',
                'keterangan'=>'yoman'
            ];
            return $hasil;
        }

        $sumStockOpname = $query->sum('stock_opname');
        $sumStockMasuk = $query->sum('stock_masuk');
        $jumlahStockAll = $sumStockOpname + $sumStockMasuk;
        //dd($jumlahStockAll);
        $count = $query->count() - 1;

        $dataPersediaanTersedia = $query->oldest()->get();

        if ($jumlahStockAll < $jumlah){
            // exception
            //dd($jumlahStockAll);
            foreach ($dataPersediaanTersedia as $item) {
                $hasil [] = (object)[
                    'produk_id'=>$item->produk_id,
                    'harga'=>$item->harga,
                    'jumlah'=>$item->stock_opname + $item->stock_masuk,
                    'keterangan'=>'yoman'
                ];
                $jumlah -= ($item->stock_opname + $item->stock_masuk);
            }
            // hasil dari stock sisa
            $hasil [] = (object)[
                'produk_id'=>$dataPersediaanTersedia[$count]->produk_id,
                'harga'=>$dataPersediaanTersedia[$count]->harga,
                'jumlah'=>$jumlah,
                'keterangan'=>'yoman1'
            ];
            return $hasil;
        } else {
            //dd($jumlah);
            // keadaan normal
            // ketika $jumlahStockAll > $jumlah
            $j=$count;
            for ($i = $jumlah;  $i >= 0 ;$i -= $stockSaldo){

                $stockSaldo = (int) $dataPersediaanTersedia[$j]->stock_opname + (int) $dataPersediaanTersedia[$j]->stock_masuk;

                if ($stockSaldo <= 0){
                    $j--;
                    continue;
                }

                if ($stockSaldo > $i){
                    $hasil [] = (object)[
                        'produk_id'=>$dataPersediaanTersedia[$j]->produk_id,
                        'harga'=>$dataPersediaanTersedia[$j]->harga,
                        'jumlah'=>$i,
                        'keterangan'=>'yoman$i'
                    ];
                    //dd($jumlahStockAll);
                    break;
                }

                $hasil [$j-1] = (object)[
                    'produk_id'=>$dataPersediaanTersedia[$j]->produk_id,
                    'harga'=>$dataPersediaanTersedia[$j]->harga,
                    'jumlah'=>$stockSaldo + $hasil [$j]['jumlah'],
                    'keterangan'=>'yoman$i--2'
                ];
                $j--;
            }
            return $hasil;
        }
    }

    public function store(object $dataMaster, array $dataDetail, $field)
    {
        $persediaan = Persediaan::query()->create([
            'active_cash'=>session('ClosedCash'),
            'jenis'=>$dataMaster->kondisi ?? $dataMaster->jenis,// baik or buruk
            'gudang_id'=>$dataMaster->gudang_id,
            'produk_id'=>$dataDetail['produk_id'],
            'harga'=>$dataDetail['harga_hpp'] ?? $dataDetail['harga'],
            $field=>$dataDetail['jumlah']
        ]);

        if ($field == 'stock_masuk' || 'stock_opname'){
            $persediaan->increment('stock_saldo', $dataDetail['jumlah']);
        }

        if ($field == 'stock_keluar'){
            $persediaan->decrement('stock_saldo', $dataDetail['jumlah']);
        }
        return $persediaan->id;
    }

    public function storeObject(object $dataMaster, object $dataDetail, $field)
    {
        $persediaan = Persediaan::query()->create([
            'active_cash'=>session('ClosedCash'),
            'jenis'=>$dataMaster->kondisi ?? $dataMaster->jenis,// baik or buruk
            'gudang_id'=>$dataMaster->gudang_id,
            'produk_id'=>$dataDetail->produk_id,
            'harga'=>$dataDetail->harga_hpp ?? $dataDetail->harga,
            $field=>$dataDetail->jumlah
        ]);

        if ($field == 'stock_masuk' || 'stock_opname'){
            $persediaan->increment('stock_saldo', $dataDetail->jumlah);
        }

        if ($field == 'stock_keluar'){
            $persediaan->decrement('stock_saldo', $dataDetail->jumlah);
        }
        return $persediaan->id;
    }

    public function update(object $dataMaster, array $dataDetail, $field)
    {
        $persediaan = Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $dataMaster->kondisi ?? $dataMaster->jenis)
            ->where('gudang_id', $dataMaster->gudang_id)
            ->where('produk_id', $dataDetail['produk_id'])
            ->where('harga', $dataDetail['harga_hpp'] ?? $dataDetail['harga']);

        if ($persediaan->doesntExist()){
            return $this->store($dataMaster, $dataDetail, $field);
        }

        $persediaan = $persediaan->first();

        $persediaan->increment($field, $dataDetail['jumlah']);

        if ($field == 'stock_masuk' || 'stock_opname'){
            $persediaan->increment('stock_saldo', $dataDetail['jumlah']);
        }

        if ($field == 'stock_keluar'){
            $persediaan->decrement('stock_saldo', $dataDetail['jumlah']);
        }
        return $persediaan->id;
    }

    public function updateObject(object $dataMaster, object $dataDetail, $field)
    {
        $persediaan = Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $dataMaster->kondisi ?? $dataMaster->jenis)
            ->where('gudang_id', $dataMaster->gudang_id)
            ->where('produk_id', $dataDetail->produk_id)
            ->where('harga', $dataDetail->harga_hpp ?? $dataDetail->harga);

        if ($persediaan->doesntExist()){
            return $this->storeObject($dataMaster, $dataDetail, $field);
        }

        $persediaan = $persediaan->first();

        $persediaan->increment($field, $dataDetail->jumlah);

        if ($field == 'stock_masuk' || 'stock_opname'){
            $persediaan->increment('stock_saldo', $dataDetail->jumlah);
        }

        if ($field == 'stock_keluar'){
            $persediaan->decrement('stock_saldo', $dataDetail->jumlah);
        }
        return $persediaan->id;
    }

    public function updatePenjualan($dataMaster, $dataDetail)
    {
        //
    }

    public function rollback(object $dataMaster, object $dataDetail, $field)
    {
        $persediaan = Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $dataMaster->kondisi ?? $dataMaster->jenis)
            ->where('gudang_id', $dataMaster->gudang_id)
            ->where('produk_id', $dataDetail['produk_id'])
            ->where('harga', $dataDetail['harga_hpp'])->first();

        $persediaan->decrement($field, $dataDetail['jumlah']);

        if ($field == 'stock_masuk' || 'stock_opname'){
            $persediaan->decrement('stock_saldo', $dataDetail['jumlah']);
        }

        if ($field == 'stock_keluar'){
            $persediaan->increment('stock_saldo', $dataDetail['jumlah']);
        }
        return $persediaan->id;
    }

    public function rollbackObject(object $dataMaster, object $dataDetail, $field, $kondisi=null)
    {
        $persediaan = Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $kondisi ?? $dataMaster->kondisi ?? $dataMaster->jenis)
            ->where('gudang_id', $dataMaster->gudang_id)
            ->where('produk_id', $dataDetail->produk_id)
            ->where('harga', $dataDetail->harga)->first();

        $persediaan->decrement($field, $dataDetail->jumlah);

        if ($field == 'stock_masuk' || 'stock_opname'){
            $persediaan->decrement('stock_saldo', $dataDetail->jumlah);
        }

        if ($field == 'stock_keluar'){
            $persediaan->increment('stock_saldo',$dataDetail->jumlah);
        }
        return $persediaan->id;
    }
}
