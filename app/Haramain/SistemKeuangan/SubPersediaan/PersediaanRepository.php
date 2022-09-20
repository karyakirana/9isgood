<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Keuangan\HargaHppALL;
use App\Models\Keuangan\Persediaan;
use Illuminate\Database\Eloquent\Builder;

class PersediaanRepository
{
    protected function query($gudangId, $kondisi,$dataItem)
    {
        return Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('gudang_id', $gudangId)
            ->where('jenis', $kondisi)
            ->where('produk_id', $dataItem->produk_id)
            ->where('harga', $dataItem->harga);
    }

    protected function queryCreate($gudangId, $kondisi, $tglInput, $dataItem)
    {
        return Persediaan::query()->create([
            'active_cash'=>session('ClosedCash'),
            'jenis'=>$kondisi,// baik or buruk
            'tgl_input'=>$tglInput,
            'gudang_id'=>$gudangId,
            'produk_id'=>$dataItem->produk_id,
            'harga'=>$dataItem->harga,
            'stock_masuk'=>$dataItem->jumlah,
            'stock_keluar'=>0,
            'stock_saldo'=>$dataItem->jumlah,
        ]);
    }

    protected function queryCreateObject($gudangId, $kondisi, $tglInput, $dataItem)
    {
        return Persediaan::query()->create([
            'active_cash'=>session('ClosedCash'),
            'jenis'=>$kondisi,// baik or buruk
            'tgl_input'=>$tglInput,
            'gudang_id'=>$gudangId,
            'produk_id'=>$dataItem->produk_id,
            'harga'=>$dataItem->harga,
            'stock_masuk'=>$dataItem->jumlah,
            'stock_keluar'=>0,
            'stock_saldo'=>$dataItem->jumlah,
        ]);
    }

    protected function queryCreateLine($gudangId, $kondisi, $tglInput, $produkId, $harga, $jumlah)
    {
        return Persediaan::query()->create([
            'active_cash'=>session('ClosedCash'),
            'jenis'=>$kondisi,// baik or buruk
            'tgl_input'=>$tglInput,
            'gudang_id'=>$gudangId,
            'produk_id'=>$produkId,
            'harga'=>$harga,
            'stock_masuk'=>$jumlah,
            'stock_keluar'=>0,
            'stock_saldo'=>$jumlah,
        ]);
    }

    public function getDataLatest($produkId, $kondisi)
    {
        return Persediaan::query()
            ->where('produk_id', $produkId)
            ->where('jenis', $kondisi)
            ->latest('tgl_input')
            ->firstOrFail();
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
            if ($queryLatest->harga == $dataItem->harga)
            {
                // jika harga terakhir sama dengan harga input sekarang, maka persediaan nambah
                $query->increment('stock_masuk', $dataItem->jumlah);
                $query->increment('stock_saldo', $dataItem->jumlah);
                return $queryLatest;
            }
            return $this->queryCreate($gudangId, $kondisi, $tglInput, $dataItem);
        }
        // jika tidak create
        return $this->queryCreate($gudangId, $kondisi, $tglInput, $dataItem);
    }

    public function storeInObject($gudangId, $kondisi, $tglInput, $dataItem)
    {
        $query = Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('gudang_id', $gudangId)
            ->where('jenis', $kondisi)
            ->where('produk_id', $dataItem->produk_id)
            ->where('harga', $dataItem->harga);
        // jika ada update
        if ($query->exists()){
            $queryLatest = $query->latest('tgl_input')->first();
            // check tanggal
            if ($queryLatest->harga == $dataItem->harga)
            {
                // jika harga terakhir sama dengan harga input sekarang, maka persediaan nambah
                $query->increment('stock_masuk', $dataItem->jumlah);
                $query->increment('stock_saldo', $dataItem->jumlah);
                return $queryLatest;
            }
            return $this->queryCreateObject($gudangId, $kondisi, $tglInput, $dataItem);
        }
        // jika tidak create
        return $this->queryCreateObject($gudangId, $kondisi, $tglInput, $dataItem);
    }

    public function storeInLine($gudangId, $kondisi, $tglInput, $produk_id, $harga, $jumlah)
    {
        $query = Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('gudang_id', $gudangId)
            ->where('jenis', $kondisi)
            ->where('produk_id', $produk_id)
            ->where('harga', $harga);
        // jika ada update
        if ($query->exists()){
            $queryLatest = $query->latest('tgl_input')->first();
            // check tanggal
            if ($queryLatest->harga == $harga)
            {
                // jika harga terakhir sama dengan harga input sekarang, maka persediaan nambah
                $query->increment('stock_masuk', $jumlah);
                $query->increment('stock_saldo', $jumlah);
                return $queryLatest;
            }
            return $this->queryCreateLine($gudangId, $kondisi, $tglInput, $produk_id, $harga, $jumlah);
        }
        // jika tidak create
        return $this->queryCreateLine($gudangId, $kondisi, $tglInput, $produk_id, $harga, $jumlah);
    }

    public function rollbackIn($persediaanId, $jumlah)
    {
        // initiate
        $query = Persediaan::query()->find($persediaanId);
        $query->decrement('stock_masuk', $jumlah);
        return $query->decrement('stock_saldo', $jumlah);
    }

    public function getStockOut($gudangId, $kondisi, $dataItem, $tglInput = null)
    {
        // dd($dataItem);
        $produkId = $dataItem->produk_id ?? $dataItem['produk_id'];
        $jumlah = $dataItem->jumlah ?? $dataItem['jumlah'];
        // check barang
        $query = Persediaan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('gudang_id', $gudangId)
            ->where('jenis', $kondisi)
            ->where('produk_id', $produkId)
            ->orderBy('tgl_input');
        //dd($query->count());
        $itemSum = $query->sum('stock_saldo');
        $itemCount = $query->count();
        $returnItem = [];
        // check persediaan sebelumnya
        if ($itemCount > 0){
            $itemJumlah = $jumlah;

            // check ketersediaan
            if ($itemSum >= $jumlah){
                // jika persediaan lebih besar dari yang diminta
                for ($x = 0; $x<$itemCount; $x++){
                    $data = $query->get()[$x];
                    $jumlah = ($itemJumlah <= $data->stock_saldo) ? $itemJumlah : $data->stock_saldo;
                    $itemJumlah -= $data->stock_saldo;
                    $returnItem[] = [
                        'persediaan_id'=>$data->id, // persediaan id
                        'produk_id'=>$produkId,
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
                    //dd($query->count());
                    $data = $query->get()[$x];

                    if ($x == $itemCount - 1){
                        // data terakhir break
                        $returnItem[] = [
                            'persediaan_id'=>$data->id, // persediaan id
                            'produk_id'=>$produkId,
                            'harga'=>$data->harga,
                            'jumlah'=> $itemJumlah,
                            'tgl_input'=> $data->tgl_input,
                            'sub_total'=>$data->harga * $itemJumlah
                        ];
                        break;
                    }
                    $itemJumlah -= $data->stock_saldo;
                    $returnItem[] = [
                        'persediaan_id'=>$data->id, // persediaan id
                        'produk_id'=>$produkId,
                        'harga'=>$data->harga,
                        'jumlah'=> $data->stock_saldo,
                        'tgl_input'=> $data->tgl_input,
                        'sub_total'=> $data->harga * $data->stock_saldo
                    ];
                }
            }
        } else {
            // create
            $hpp = HargaHppALL::query()->latest()->first()->persen;
            $harga = $dataItem->harga;
            $itemJumlah = $jumlah;
            $hargaHpp = $harga * $hpp;
            $create = Persediaan::query()->create([
                'active_cash'=>session('ClosedCash'),
                'jenis'=>$kondisi,// baik or buruk
                'tgl_input'=>$tglInput,
                'gudang_id'=>$gudangId,
                'produk_id'=>$produkId,
                'harga'=>$hargaHpp,
                'stock_masuk'=>0,
                'stock_keluar'=>$itemJumlah,
                'stock_saldo'=>0 - $itemJumlah,
            ]);
            $returnItem[] = [
                'persediaan_id'=>$create->id, // persediaan id
                'produk_id'=>$produkId,
                'harga'=> $create->harga,
                'jumlah'=> $itemJumlah,
                'tgl_input'=> $tglInput,
                'sub_total'=> $create->harga * $create->stock_saldo
            ];
        }
        return $returnItem;
    }

    public function storeOut($persediaanId, $jumlah)
    {
        // check barang
        $query = Persediaan::query()->find($persediaanId);
        $query->increment('stock_keluar', $jumlah);
        $query->decrement('stock_saldo', $jumlah);
        return $query;
    }

    public function rollbackOut($persediaanId, $jumlah)
    {
        // check barang
        $query = Persediaan::query()->find($persediaanId);
        $query->decrement('stock_keluar', $jumlah);
        $query->increment('stock_saldo', $jumlah);
        return $query;
    }
}
