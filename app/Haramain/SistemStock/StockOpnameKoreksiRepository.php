<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockOpnameKoreksi;
use App\Models\Stock\StockOpnameKoreksiDetail;

class StockOpnameKoreksiRepository
{
    protected $stockInventoryRepository;

    public function __construct()
    {
        $this->stockInventoryRepository = new StockInventoryRepository();
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
            if ($data->jenis == 'tambah'){
                // update stock inventory
                $this->stockInventoryRepository->update($data->kondisi, $data->gudangId, 'stock_opname', $detail);
            } else {
                // update stock decrement
                $this->stockInventoryRepository->updateDecrement($data->kondisi, $data->gudangId, 'stock_opname', $detail);
            }
        }
    }

    public function rollback($stockOpnameKoreksiId)
    {
        $stockOpnameKoreksi = $this->getById($stockOpnameKoreksiId);
        $stockOpnameKoreksiDetail = $stockOpnameKoreksi->stockOpnameKoreksiDetail;
        foreach ($stockOpnameKoreksiDetail as $item) {
            if ($stockOpnameKoreksi->jenis == 'tambah'){
                // update stock inventory
                $this->stockInventoryRepository->rollback($stockOpnameKoreksi->kondisi, $stockOpnameKoreksi->gudangId, 'stock_opname', $item);
            } else {
                // update stock decrement
                $this->stockInventoryRepository->rollbackDecrement($stockOpnameKoreksi->kondisi, $stockOpnameKoreksi->gudangId, 'stock_opname', $item);
            }
        }
        return $stockOpnameKoreksi->stockOpnameKoreksiDetail()->delete();
    }

    public function delete($stockOpnameKoreksiId)
    {
        $this->rollback($stockOpnameKoreksiId);
        return $this->getById($stockOpnameKoreksiId)->delete();
    }
}
