<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockMasuk;
use App\Models\Stock\StockMasukDetail;

class StockMasukRepository implements StockTransaksiInterface
{
    protected $stockInventoryRepository;

    public function __construct()
    {
        $this->stockInventoryRepository = new StockInventoryRepository();
    }

    protected function kode($kondisi)
    {
        $query = StockMasuk::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('kondisi', $kondisi)
            ->latest('kode');

        $kodeKondisi = ($kondisi == 'baik') ? 'SM' : 'SMR';

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kodeKondisi}/".date('Y');
        }

        $num = (int) $query->first()->last_num_trans + 1;
        return sprintf("%04s", $num)."/{$kodeKondisi}/".date('Y');
    }

    public function getDataById($stockableType, $stockableId)
    {
        return StockMasuk::query()
            ->where('stockable_masuk_type', $stockableType)
            ->where('stockable_masuk_id', $stockableId)
            ->first();
    }

    public function getDataAll($activeCash = true)
    {
        $query = StockMasuk::query();
        if ($activeCash){
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        return $query;
    }

    public function store($data, $stockableType, $stockableId)
    {
        $data = (object) $data;
        if (isset($data->jenisMutasi)){
            $kondisi = \Str::after($data->jenisMutasi, '_');
        } else {
            $kondisi = $data->kondisi;
        }
        $gudang = $data->gudangId ?? $data->gudangTujuanId;
        $tglMasuk = $data->tglNota ?? $data->tglMutasi;
        $stockMasuk = StockMasuk::query()
            ->create([
                'kode'=>$this->kode($kondisi),
                'active_cash'=>session('ClosedCash'),
                'stockable_masuk_id'=>$stockableId,
                'stockable_masuk_type'=>$stockableType,
                'kondisi'=>$kondisi,
                'gudang_id'=>$gudang,
                'supplier_id'=>$data->supplierId ?? null,
                'tgl_masuk'=>tanggalan_database_format($tglMasuk, 'd-M-Y'),
                'user_id'=>\Auth::id(),
                'nomor_po'=>$data->nomorPo ?? '-',
                'nomor_surat_jalan'=>$data->suratJalan ?? '-',
                'keterangan'=>$data->keterangan,
            ]);
        $this->storeDetail($data->dataDetail, $gudang, $kondisi, $stockMasuk->id);
        return $stockMasuk;
    }

    public function update($data, $stockableType, $stockableId)
    {
        $data = (object) $data;
        if (isset($data->jenisMutasi)){
            $kondisi = \Str::after($data->jenisMutasi, '_');
        } else {
            $kondisi = $data->kondisi;
        }
        $gudang = $data->gudangId ?? $data->gudangTujuanId;
        $tglMasuk = $data->tglNota ?? $data->tglMutasi;
        $this->getDataById($stockableType, $stockableId)->update([
            'stockable_masuk_id'=>$stockableId,
            'stockable_masuk_type'=>$stockableType,
            'kondisi'=>$kondisi,
            'gudang_id'=>$gudang,
            'supplier_id'=>$data->supplierId ?? null,
            'tgl_masuk'=>tanggalan_database_format($tglMasuk, 'd-M-Y'),
            'user_id'=>\Auth::id(),
            'nomor_po'=>$data->nomorPo ?? '-',
            'nomor_surat_jalan'=>$data->suratJalan ?? '-',
            'keterangan'=>$data->keterangan,
        ]);
        $stockMasuk = $this->getDataById($stockableType, $stockableId);
        $this->storeDetail($data->dataDetail, $gudang, $kondisi, $stockMasuk->id);
        return $stockMasuk;
    }

    public function rollback($stockableType, $stockableId)
    {
        $stockMasuk = $this->getDataById($stockableType, $stockableId);
        $stockMasukDetail = StockMasukDetail::query()->where('stock_masuk_id', $stockMasuk->id);
        // rollback stock inventory
        foreach ($stockMasukDetail as $item) {
            $this->stockInventoryRepository->rollback($stockMasuk->kondisi, $stockMasuk->gudang_id, 'stock_masuk', $item);
        }
        return $stockMasukDetail->delete();
    }

    public function destory($stockableType, $stockableId)
    {
        $this->rollback($stockableType, $stockableId);
        return $this->getDataById($stockableType, $stockableId)->delete();
    }

    protected function storeDetail($dataDetail, $gudangId, $kondisi, $stockMasukId)
    {
        foreach ($dataDetail as $item) {
            $item = (object) $item;
            StockMasukDetail::query()->create([
                'stock_masuk_id'=>$stockMasukId,
                'produk_id'=>$item->produk_id,
                'jumlah'=>$item->jumlah,
            ]);
            // update stock
            $this->stockInventoryRepository->update($kondisi, $gudangId, 'stock_masuk', $item);
        }
    }
}