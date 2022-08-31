<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockOpname;
use App\Models\Stock\StockOpnameDetail;

class StockOpnameRepository
{
    protected $stockInventoryRepository;

    public function __construct()
    {
        $this->stockInventoryRepository = new StockInventoryRepository();
    }

    protected function kode($kondisi)
    {
        $query = StockOpname::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $kondisi)
            ->latest('kode');

        $kode = ($kondisi == 'baik') ? 'SO' : 'SOR';

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kode}/".date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/{$kode}/".date('Y');
    }

    public function getDataById($stockOpnameId)
    {
        return StockOpname::query()->find($stockOpnameId);
    }

    public function getDataAll($closedCash = true)
    {
        $query = StockOpname::query();
        if ($closedCash){
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        return $query->get();
    }

    public function store($data)
    {
        $data = (object) $data;
        $stockOpname = StockOpname::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode($data->kondisi),
                'jenis'=>$data->kondisi,
                'tgl_input'=>tanggalan_database_format($data->tglInput, 'd-M-Y'),
                'gudang_id'=>$data->gudangId,
                'user_id'=>$data->userId,
                'pegawai_id'=>$data->pegawaiId,
                'keterangan'=>$data->keterangan,
            ]);
        $this->storeDetail($data->dataDetail, $data->gudangId, $data->kondisi, $stockOpname->id);
        return $stockOpname->id;
    }

    public function update($data)
    {
        $data = (object) $data;
        $stockOpname = $this->getDataById($data->stockOpnameId);
        $stockOpname->update([
            'jenis'=>$data->kondisi,
            'tgl_input'=>tanggalan_database_format($data->tglInput, 'd-M-Y'),
            'gudang_id'=>$data->gudangId,
            'user_id'=>$data->userId,
            'pegawai_id'=>$data->pegawaiId,
            'keterangan'=>$data->keterangan,
        ]);
        $this->storeDetail($data->dataDetail, $data->gudangId, $data->kondisi, $stockOpname->id);
        return $stockOpname;
    }

    public function rollback($stockOpnameId)
    {
        return StockOpnameDetail::query()->where('stock_opname_id', $stockOpnameId)->delete();
    }

    public function destroy($stockOpnameId)
    {
        $this->rollback($stockOpnameId);
        return StockOpname::destroy($stockOpnameId);
    }

    protected function storeDetail($dataDetail, $gudangId, $kondisi, $stockOpnameId)
    {
        foreach ($dataDetail as $item) {
            $item = (object) $item;
            StockOpnameDetail::query()
                ->create([
                    'stock_opname_id'=>$stockOpnameId,
                    'produk_id'=>$item->produk_id,
                    'jumlah'=>$item->jumlah,
                ]);
            // update stock
            $this->stockInventoryRepository->update($kondisi, $gudangId, 'stock_opname', $item);
        }
    }
}
