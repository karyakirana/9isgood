<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockKeluar;

class StockKeluarRepository
{
    protected $kode;
    protected $activeCash;
    protected $stockableKeluarType;
    protected $stockableKeluarId;
    protected $kondisi;
    protected $gudangId;
    protected $supllierId;
    protected $tglKeluar;
    protected $userId;
    protected $keterangan;

    protected $dataDetail;

    protected $produk_id;
    protected $jumlah;

    public function __construct()
    {
        $this->activeCash = session('ClosedCash');
    }

    public static function build(...$params)
    {
        return new static(...$params);
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
            return "0001/$kodeKondisi/".date('Y');
        }

        $num = (int) $query->first()->last_num_trans + 1;
        return sprintf("%04s", $num)."/$kodeKondisi/".date('Y');
    }

    protected function getDataByStockable()
    {
        return StockKeluar::query()
            ->where('stockable_keluar_type', $this->stockableKeluarType)
            ->where('stockable_keluar_id', $this->stockableKeluarId)
            ->firstOrFail();
    }

    public function store()
    {
        $stockKeluar = StockKeluar::query()
            ->create([
                'kode'=>$this->kode,
                'supplier_id'=>$this->supllierId,
                'active_cash'=>$this->activeCash,
                'stockable_keluar_id'=>$this->stockableKeluarId,
                'stockable_keluar_type'=>$this->stockableKeluarType,
                'kondisi'=>$this->kondisi,
                'gudang_id'=>$this->gudangId,
                'tgl_keluar'=>$this->tglKeluar,
                'user_id'=>$this->userId,
                'keterangan'=>$this->keterangan,
            ]);
        $stockKeluar->stockKeluarDetail()->createMany($this->storeDetail());
        return $stockKeluar;
    }

    public function update()
    {
        $stockKeluar = $this->getDataByStockable();
        $stockKeluar->update([
            'supplier_id'=>$this->supllierId,
            'stockable_keluar_id'=>$this->stockableKeluarId,
            'stockable_keluar_type'=>$this->stockableKeluarType,
            'kondisi'=>$this->kondisi,
            'gudang_id'=>$this->gudangId,
            'tgl_keluar'=>$this->tglKeluar,
            'user_id'=>$this->userId,
            'keterangan'=>$this->keterangan,
        ]);
        $stockKeluar->stockKeluarDetail()->createMany($this->storeDetail());
        return $stockKeluar;
    }

    protected function setDataDetail($item)
    {
        $this->produk_id = $item->produk_id;
        $this->jumlah = $item->jumlah;
    }

    protected function storeDetail()
    {
        $detail = [];
        foreach ($this->dataDetail as $item) {
            $this->setDataDetail($item);
            $detail[] = [
                'produk_id'=>$this->produk_id,
                'jumlah'=>$this->jumlah
            ];
            // update stock
            (new StockInventoryRepository($this->kondisi, $this->gudangId, $item))->update('stock_keluar');
        }
        return $detail;
    }

    public function rollback()
    {
        foreach ($this->dataDetail as $item) {
            StockInventoryRepository::build($this->kondisi, $this->gudangId, $item)->rollback('stock_keluar');
        }
        return $this->getDataByStockable()->stockKeluarDetail()->delete();
    }
}
