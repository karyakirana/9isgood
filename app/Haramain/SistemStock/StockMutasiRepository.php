<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockMutasi;
use App\Models\Stock\StockMutasiDetail;

class StockMutasiRepository
{
    protected function kode($jenisMutasi)
    {
        $query = StockMutasi::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis_mutasi', $jenisMutasi)
            ->latest('kode');

        $kodeKondisi = ($jenisMutasi == 'baik_baik') ? 'MBB' : 'MBR';
        $kodeKondisi = ($jenisMutasi == 'rusak_rusak') ? 'MRR' : $kodeKondisi;

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kodeKondisi}/".date('Y');
        }

        $num = (int) $query->first()->last_num_trans + 1;
        return sprintf("%04s", $num)."/{$kodeKondisi}/".date('Y');
    }

    public function getDataById($stockMutasiId)
    {
        return StockMutasi::query()->find($stockMutasiId);
    }

    public function getDataAll($activeCash)
    {
        $query = StockMutasi::query();
        if ($activeCash){
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        return $query->get();
    }

    public function store($data)
    {
        $data = (object) $data;
        $stockMutasi = StockMutasi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode($data->jenisMutasi),
                'jenis_mutasi'=>$data->jenisMutasi,
                'gudang_asal_id'=>$data->gudangAsalId,
                'gudang_tujuan_id'=>$data->gudangTujuanId,
                'tgl_mutasi'=>tanggalan_database_format($data->tglMutasi, 'd-M-Y'),
                'user_id'=>$data->userId,
                'keterangan'=>$data->keterangan,
            ]);
        $this->storeDetail($data->dataDetail, $stockMutasi->id);
        return $stockMutasi;
    }

    public function update($data)
    {
        $data = (object) $data;
        $stockMutasi = $this->getDataById($data->stockMutasiId);
        $stockMutasi->update([
            'jenis_mutasi'=>$data->jenisMutasi,
            'gudang_asal_id'=>$data->gudangAsalId,
            'gudang_tujuan_id'=>$data->gudangTujuanId,
            'tgl_mutasi'=>tanggalan_database_format($data->tglMutasi, 'd-M-Y'),
            'user_id'=>$data->userId,
            'keterangan'=>$data->keterangan,
        ]);
        $this->storeDetail($data->dataDetail, $stockMutasi->id);
        return $stockMutasi;
    }

    public function rollback($stockMutasiId)
    {
        return StockMutasiDetail::query()->where('stock_mutasi_id', $stockMutasiId)->delete();
    }

    public function destroy($stockMutasiId)
    {
        $this->rollback($stockMutasiId);
        return StockMutasi::destroy($stockMutasiId);
    }

    protected function storeDetail($dataDetail, $stockMutasiId)
    {
        foreach ($dataDetail as $item) {
            $item = (object) $item;
            StockMutasiDetail::query()
                ->create([
                    'stock_mutasi_id'=>$stockMutasiId,
                    'produk_id'=>$item->produk_id,
                    'jumlah'=>$item->jumlah,
                ]);
        }
    }
}
