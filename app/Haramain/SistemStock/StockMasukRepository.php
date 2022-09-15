<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockMasuk;

class StockMasukRepository
{
    protected $stockInventoryRepository;

    protected $kode;
    protected $activeCash;
    protected $stockableType;
    protected $stockableId;
    protected $kondisi;
    protected $gudangId;
    protected $supplierId;
    protected $tglMasuk;
    protected $userId;
    protected $nomorPo;
    protected $nomorSuratJalan;
    protected $keterangan;

    protected $dataDetail;

    protected $produk_id;
    protected $jumlah;

    public static function build(...$params)
    {
        return new static(...$params);
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

    protected function getDataByStockable()
    {
        return StockMasuk::query()
            ->where('stockable_masuk_type', $this->stockableType)
            ->where('stockable_masuk_id', $this->stockableId)
            ->first();
    }

    public function store()
    {
        $stockMasuk = StockMasuk::query()
            ->create([
                'kode'=>$this->kode,
                'active_cash'=>$this->activeCash,
                'stockable_masuk_id'=>$this->stockableId,
                'stockable_masuk_type'=>$this->stockableType,
                'kondisi'=>$this->kondisi,
                'gudang_id'=>$this->gudangId,
                'supplier_id'=>$this->supplierId,
                'tgl_masuk'=>$this->tglMasuk,
                'user_id'=>$this->userId,
                'nomor_po'=>$this->nomorPo,
                'nomor_surat_jalan'=>$this->nomorSuratJalan,
                'keterangan'=>$this->keterangan,
            ]);
        $stockMasuk->stockMasukDetail()->createMany($this->storeDetail());
        return $stockMasuk;
    }

    public function update()
    {
        $stockMasuk = $this->getDataByStockable();
        $stockMasuk->update([
            'stockable_masuk_id'=>$this->stockableId,
            'stockable_masuk_type'=>$this->stockableType,
            'kondisi'=>$this->kondisi,
            'gudang_id'=>$this->gudangId,
            'supplier_id'=>$this->supplierId,
            'tgl_masuk'=>$this->tglMasuk,
            'user_id'=>\Auth::id(),
            'nomor_po'=>$this->nomorPo,
            'nomor_surat_jalan'=>$this->nomorSuratJalan,
            'keterangan'=>$this->keterangan,
        ]);
        $stockMasuk = $stockMasuk->refresh();
        $stockMasuk->stockMasukDetail()->createMany($this->storeDetail());
        return $stockMasuk;
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
            StockInventoryRepository::build($this->kondisi, $this->gudangId, $item)->update('stock_masuk');
        }
        return $detail;
    }

    public function rollback()
    {
        foreach ($this->dataDetail as $item) {
            StockInventoryRepository::build($this->kondisi, $this->gudangId, $item)->rollback('stock_masuk');
        }
        return $this->getDataByStockable()->stockMasukDetail()->delete();
    }
}
