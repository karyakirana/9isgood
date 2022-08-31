<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockKeluar;
use App\Models\Stock\StockKeluarDetail;

class StockKeluarRepository implements StockTransaksiInterface
{
    protected $stockInventoryRepo;

    public function __construct()
    {
        $this->stockInventoryRepo = new StockInventoryRepository();
    }

    protected function kode($kondisi)
    {
        // query
        $query = StockKeluar::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('kondisi', $kondisi)
            ->latest('kode');

        $kodeKondisi = ($kondisi == 'baik') ? 'SK' : 'SKR';

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kodeKondisi}/".date('Y');
        }

        $num = (int) $query->first()->last_num_trans + 1;
        return sprintf("%04s", $num)."/{$kodeKondisi}/".date('Y');
    }

    public function getDataById($stockableType, $stockableId)
    {
        return StockKeluar::query()
            ->where('stockable_keluar_type', $stockableType)
            ->where('stockable_keluar_id', $stockableId)
            ->first();
    }

    public function getDataAll($activeCash = true)
    {
        $query = StockKeluar::query();
        if ($activeCash){
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        return $query->get();
    }

    public function store($data, $stockableType, $stockableId)
    {
        $data = (object) $data;
        if (isset($data->jenisMutasi)){
            $kondisi = \Str::before($data->jenisMutasi, '_');
        } else {
            $kondisi = $data->kondisi;
        }
        $gudang = $data->gudangId ?? $data->gudangAsalId;
        $tglKeluar = $data->tglKeluar ?? $data->tglNota ?? $data->tglMutasi;
        $stockKeluar = StockKeluar::query()
            ->create([
                'kode'=>$this->kode($kondisi),
                'supplier_id'=>$data->supplierId ?? null,
                'active_cash'=>session('ClosedCash'),
                'stockable_keluar_id'=>$stockableId,
                'stockable_keluar_type'=>$stockableType,
                'kondisi'=>$kondisi,
                'gudang_id'=>$gudang,
                'tgl_keluar'=>tanggalan_database_format($tglKeluar, 'd-M-Y'),
                'user_id'=>$data->userId,
                'keterangan'=>$data->keterangan,
            ]);
        $this->storeDetail($data->dataDetail, $gudang, $kondisi, $stockKeluar->id);
        return $stockKeluar;
    }

    public function update($data, $stockableType, $stockableId)
    {
        $data = (object) $data;
        if (isset($data->jenisMutasi)){
            $kondisi = \Str::before($data->jenisMutasi, '_');
        } else {
            $kondisi = $data->kondisi;
        }
        $gudang = $data->gudangId ?? $data->gudangAsalId;
        $tglKeluar = $data->tglNota ?? $data->tglMutasi;
        $stockKeluar = $this->getDataById($stockableType, $stockableId);
        $stockKeluar->update([
            'supplier_id'=>$data->supplierId ?? null,
            'stockable_keluar_id'=>$stockableId,
            'stockable_keluar_type'=>$stockableType,
            'kondisi'=>$kondisi,
            'gudang_id'=>$gudang,
            'tgl_keluar'=>tanggalan_database_format($tglKeluar, 'd-M-Y'),
            'user_id'=>$data->userId,
            'keterangan'=>$data->keterangan,
        ]);
        $this->storeDetail($data->dataDetail, $gudang, $kondisi, $stockKeluar->id);
        return $stockKeluar;
    }

    public function rollback($stockableType, $stockableId)
    {
        $stockKeluar = $this->getDataById($stockableType, $stockableId);
        $stockKeluarDetail = StockKeluarDetail::query()->where('stock_keluar_id', $stockKeluar->id);
        // rollback stock inventory
        foreach ($stockKeluarDetail as $item) {
            $this->stockInventoryRepo->rollback($stockKeluar->kondisi, $stockKeluar->gudang_id, 'stock_keluar', $item);
        }
        return $stockKeluarDetail->delete();
    }

    public function destory($stockableType, $stockableId)
    {
        $this->rollback($stockableType, $stockableId);
        return $this->getDataById($stockableType, $stockableId)->delete();
    }

    protected function storeDetail($dataDetail, $gudangId, $kondisi, $stockKeluarId)
    {
        foreach ($dataDetail as $item) {
            $item = (object) $item;
            //dd($item);
            StockKeluarDetail::query()
                ->create([
                    'stock_keluar_id'=>$stockKeluarId,
                    'produk_id'=>$item->produk_id,
                    'jumlah'=>$item->jumlah,
                ]);
            // update stock
            $this->stockInventoryRepo->update($kondisi, $gudangId, 'stock_keluar', $item);
        }
    }
}
