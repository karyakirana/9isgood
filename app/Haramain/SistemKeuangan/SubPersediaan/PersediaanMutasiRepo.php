<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Keuangan\PersediaanMutasi;
use App\Models\Keuangan\PersediaanMutasiDetail;
use App\Models\Stock\StockMutasi;

class PersediaanMutasiRepo
{
    public function getDataById($stockMutasiId)
    {
        return PersediaanMutasi::query()
            ->where('stock_mutasi_id', $stockMutasiId)
            ->first();
    }

    public function getDataAll($activeCash = true)
    {
        $query = PersediaanMutasi::query();
        if ($activeCash)
        {
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        return $query->get();
    }

    public function store($data, $stockMutasiId)
    {
        $data = (object) $data;
        $persediaanMutasi = PersediaanMutasi::query()
            ->create([
                'stock_mutasi_id'=>$stockMutasiId,
                'jenis_mutasi'=>$data->jenisMutasi,
                'gudang_asal_id'=>$data->gudangAsalId,
                'gudang_tujuan_id'=>$data->gudangTujuanId,
                'total_barang'=>$data->totalBarang,
                'total_harga'=>0,
            ]);
        $this->storeDetail($data->DataDetail, $persediaanMutasi->id);
        return $persediaanMutasi;
    }

    public function update($data, $stockMutasiId)
    {
        $data = (object) $data;
        $persediaanMutasi = $this->getDataById($stockMutasiId);
        $persediaanMutasi->update([
            'jenis_mutasi'=>$data->jenisMutasi,
            'gudang_asal_id'=>$data->gudangAsalId,
            'gudang_tujuan_id'=>$data->gudangTujuanId,
            'total_barang'=>$data->totalBarang,
            'total_harga'=>0,
        ]);
        $this->storeDetail($data->DataDetail, $persediaanMutasi->id);
        return $persediaanMutasi;
    }

    protected function storeDetail($dataDetail, $persediaanMutasiId)
    {
        foreach ($dataDetail as $item) {
            $item = (object) $item;
            PersediaanMutasiDetail::query()
                ->create([
                    'persediaan_mutasi_id'=>$persediaanMutasiId,
                    'produk_id'=>$item->produk_id,
                    'harga'=>$item->harga,
                    'jumlah'=>$item->jumlah,
                    'sub_total'=>$item->sub_total
                ]);
        }
    }

    public function rollback($stockMutasiId)
    {
        $persediaanMutasi = $this->getDataById($stockMutasiId);
        return PersediaanMutasiDetail::query()->where('persediaan_mutasi_id', $persediaanMutasi->id);
    }

    public function destroy($stockMutasiId)
    {
        $this->rollback($stockMutasiId);
        return StockMutasi::query()->where('stock_mutasi_id')->delete();
    }
}
