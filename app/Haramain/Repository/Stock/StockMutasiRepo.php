<?php namespace App\Haramain\Repository\Stock;

use App\Models\Stock\StockMutasi;
use App\Models\Stock\StockMutasiDetail;
use Illuminate\Support\Facades\Auth;

class StockMutasiRepo
{
    protected $stockMutasi;
    protected $stockMutasiDetail;

    public function __construct()
    {
        $this->stockMutasi = new StockMutasi();
        $this->stockMutasiDetail = new StockMutasiDetail();
    }

    public function kode($jenis = 'baik_baik')
    {
        $query = StockMutasi::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis_mutasi', $jenis)
            ->latest('kode');

        $kodeKondisi = ($jenis == 'baik_baik') ? 'MBB' : 'MBR';

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
            'jenis_mutasi'=>$data->jenisMutasi,
            'gudang_asal_id'=>$data->gudangAsalId,
            'gudang_tujuan_id'=>$data->gudangTujuanId,
            'tgl_mutasi'=>tanggalan_database_format($data->tglMutasi, 'd-M-Y'),
            'user_id'=>Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        $this->storeDetail($mutasi->id, $data['dataDetail']);

        return $mutasi;
    }

    protected function storeDetail($mutasiId, $dataDetail)
    {
        foreach ($dataDetail as $item) {
            $this->stockMutasiDetail->newQuery()
                ->create([
                    'stock_mutasi_id'=>$mutasiId,
                    'produk_id'=>$item['produk_id'],
                    'jumlah'=>$item['jumlah'],
                ]);
        }
    }

    public function update($data)
    {
        // initiate
        $mutasi = StockMutasi::query()->find($data->mutasi_id);

        // delete mutasi detail
        $mutasi->stockMutasiDetail()->delete();

        // update
        $mutasi->update([
            'gudang_asal_id'=>$data->gudangAsalId,
            'gudang_tujuan_id'=>$data->gudangTujuanId,
            'tgl_mutasi'=>tanggalan_database_format($data->tglMutasi, 'd-M-Y'),
            'user_id'=>Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        $this->storeDetail($mutasi->id, $data);

        return $mutasi;
    }
}
