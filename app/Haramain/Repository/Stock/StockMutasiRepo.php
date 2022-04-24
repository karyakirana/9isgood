<?php namespace App\Haramain\Repository\Stock;

use App\Models\Stock\StockMutasi;
use Illuminate\Support\Facades\Auth;

class StockMutasiRepo
{
    public function kode($jenis = 'baik_baik')
    {
        $query = StockMutasi::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis_mutasi', $jenis)
            ->latest('kode');

        $kodeKondisi = ($jenis == 'baik') ? 'MBB' : 'MBR';

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kodeKondisi}/".date('Y');
        }

        $num = (int) $query->first()->last_num_trans + 1;
        return sprintf("%04s", $num)."/{$kodeKondisi}/".date('Y');
    }

    public function store($data)
    {
        $mutasi = StockMutasi::query()->create([
            'active_cash'=>session('ClosedCash'),
            'kode'=>$this->kode(),
            'jenis_mutasi'=>$data->jenis_mutasi,
            'gudang_asal_id'=>$data->gudang_asal_id,
            'gudang_tujuan_id'=>$data->gudang_tujuan_id,
            'tgl_mutasi'=>tanggalan_database_format($data->tgl_mutasi, 'd-M-Y'),
            'user_id'=>Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        foreach ($data->data_detail as $item) {
            $mutasi->stockMutasiDetail()->create([
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah'],
            ]);
        }
        // stock keluar
        $stockKeluar = (new StockKeluarRepo())->storeFromRelation($mutasi, $data);
        // stock masuk
        $stockMasuk = (new StockMasukRepo())->storeFromRelation($mutasi, $data);

        return $mutasi->id;
    }

    public function update($data)
    {
        // initiate
        $mutasi = StockMutasi::query()->find($data->mutasi_id);

        // delete mutasi detail
        $mutasi->stockMutasiDetail()->delete();

        // update
        $mutasi->update([
            'jenis_mutasi'=>$data->jenis_mutasi,
            'gudang_asal_id'=>$data->gudang_asal_id,
            'gudang_tujuan_id'=>$data->gudang_tujuan_id,
            'tgl_mutasi'=>tanggalan_database_format($data->tgl_mutasi, 'd-M-Y'),
            'user_id'=>Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        foreach ($data->data_detail as $item) {
            $mutasi->stockMutasiDetail()->create([
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah'],
            ]);
        }

        $stockKeluar = (new StockKeluarRepo())->updateFromRelation($mutasi->stockKeluarMorph(), $data);
        $stockMasuk = (new StockMasukRepo())->updateFromRelation($mutasi->stockMasukMorph(), $data);

        return $mutasi->id;
    }
}
