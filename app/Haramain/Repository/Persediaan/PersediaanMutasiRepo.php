<?php namespace App\Haramain\Repository\Persediaan;

use App\Models\Keuangan\PersediaanMutasi;
use App\Models\Keuangan\PersediaanMutasiDetail;

class PersediaanMutasiRepo
{
    protected $persediaanMutasi;
    protected $persediaanMutasiDetail;

    public function __construct()
    {
        $this->persediaanMutasi = new PersediaanMutasi();
        $this->persediaanMutasiDetail = new PersediaanMutasiDetail();
    }

    public function store($stockMutasiId, $data, $dataOut)
    {
        $persediaanMutasi = $this->persediaanMutasi->newQuery()
            ->create([
                'stock_mutasi_id'=>$stockMutasiId,
                'jenis_mutasi'=>$data['jenisMutasi'],
                'gudang_asal_id'=>$data['gudangAsalId'],
                'gudang_tujuan_id'=>$data['gudangTujuanId'],
                'total_barang'=>$data['totalBarang'],
                'total_harga'=>$data['totalHarga'],
            ]);
        $this->storeDetail($dataOut, $persediaanMutasi->id);
        return $persediaanMutasi;
    }

    protected function storeDetail($dataDetail, $persediaanMutasiId)
    {
        foreach ($dataDetail as $item) {
            $this->persediaanMutasiDetail->newQuery()
                ->create([
                    'persediaan_mutasi_id'=>$persediaanMutasiId,
                    'produk_id'=>$item['produk_id'],
                    'harga'=>$item['harga'],
                    'jumlah'=>$item['jumlah'],
                    'sub_total'=>$item['sub_total']
                ]);
        }
    }

    public function update($stockMutasiId, $data)
    {
        $persediaanMutasi = PersediaanMutasi::query()->where('stock_masuk_id', $stockMutasiId)->first();
        $persediaanMutasi->update([
            'gudang_asal_id'=>$data['gudangAsalId'],
            'gudang_tujuan_id'=>$data['gudangTujuanId'],
            'total_barang'=>$data['totalBarang'],
            'total_harga'=>$data['totalHarga'],
        ]);
        $this->storeDetail($data['dataDetail'], $persediaanMutasi->id);
        return $persediaanMutasi;
    }

    public function rollback($stockMutasiId)
    {
        $persediaanMutasi = PersediaanMutasi::query()->where('stock_masuk_id', $stockMutasiId)->first();
        $this->persediaanMutasiDetail->newQuery()->where('persediaan_mutasi_id', $persediaanMutasi->id)->delete();
        return $persediaanMutasi;
    }

    public function destroy($stockMutasiId)
    {
        return $this->rollback($stockMutasiId)->delete();
    }
}
