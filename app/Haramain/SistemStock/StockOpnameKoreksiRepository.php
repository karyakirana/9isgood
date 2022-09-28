<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockOpnameKoreksi;
use App\Models\Stock\StockOpnameKoreksiDetail;

class StockOpnameKoreksiRepository
{
    public function __construct()
    {
        //
    }

    public function getById($stockOpnameKoreksiId)
    {
        return StockOpnameKoreksi::query()->findOrFail($stockOpnameKoreksiId);
    }

    protected function kode()
    {
        $query = StockOpnameKoreksi::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()){
            return "0001/KOREKSI/".date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/KOREKSI/".date('Y');
    }

    public function store($data)
    {
        $data = (object) $data;
        $stockOpnameKoreksi = StockOpnameKoreksi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode(),
                'jenis'=>$data->jenis,
                'kondisi'=>$data->kondisi,
                'tgl_input'=>$data->tglInput,
                'gudang_id'=>$data->gudangId,
                'user_id'=>$data->userId,
                'keterangan'=>$data->keterangan ?? '-',
            ]);
        $this->storeDetail($data, $stockOpnameKoreksi->id);
        return $stockOpnameKoreksi;
    }

    public function update($data)
    {
        $data = (object) $data;
        $this->getById($data->stockOpnameKoreksiId)->update([
            'jenis'=>$data->jenis,
            'kondisi'=>$data->kondisi,
            'tgl_input'=>$data->tglInput,
            'gudang_id'=>$data->gudangId,
            'user_id'=>$data->userId,
            'keterangan'=>$data->keterangan ?? '-',
        ]);
        $stockOpnameKoreksi = $this->getById($data->stockOpnameKoreksiId);
        $this->storeDetail($data, $stockOpnameKoreksi->id);
        return $stockOpnameKoreksi;
    }

    protected function storeDetail($data, $stockOpnameKoreksiId)
    {
        foreach ($data->dataDetail as $detail) {
            $detail = (object) $detail;
            // store detail
            StockOpnameKoreksiDetail::query()->create([
                'stock_opname_koreksi_id'=>$stockOpnameKoreksiId,
                'produk_id'=>$detail->produk_id,
                'jumlah'=>$detail->jumlah,
            ]);
            StockInventoryStaticRepo::stockOpnameChange($data->jenis,  $data->kondisi, $data->gudangId, $detail->produk_id, $detail->jumlah);
        }
    }

    public function rollback($stockOpnameKoreksiId)
    {
        $stockOpnameKoreksi = $this->getById($stockOpnameKoreksiId);
        $stockOpnameKoreksiDetail = $stockOpnameKoreksi->stockOpnameKoreksiDetail;
        foreach ($stockOpnameKoreksiDetail as $item) {
            StockInventoryStaticRepo::stockOpnameChangeRollback($stockOpnameKoreksi->jenis, $stockOpnameKoreksi->kondisi, $stockOpnameKoreksi->gudangId, $item->produk_id, $item->jumlah);
        }
        return $stockOpnameKoreksi->stockOpnameKoreksiDetail()->delete();
    }

    public function delete($stockOpnameKoreksiId)
    {
        $this->rollback($stockOpnameKoreksiId);
        return $this->getById($stockOpnameKoreksiId)->delete();
    }
}
