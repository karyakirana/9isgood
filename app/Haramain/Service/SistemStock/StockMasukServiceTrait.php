<?php namespace App\Haramain\Service\SistemStock;

use App\Models\Stock\StockMasuk;

trait StockMasukServiceTrait
{
    // initiate stock masuk variabel
    protected $stockMasuk;
    protected $stockMasukDetail;

    // stock masuk variabel
    protected $kodeStock;
    protected $kondisi;
    protected $tglMasuk;
    protected $nomorPo;

    protected function setStockMasukData($data)
    {
        $this->kondisi = $data['kondisi'];
        $this->kodeStock = $this->setStockKode($this->kondisi);
        $this->tglMasuk = $data['tglNota'];
        $this->nomorPo = $data['nomorPo'] ?? null;
    }

    // store stock masuk
    public function storeStockMasuk()
    {
        return $this->stockMasuk->create([
            'kode'=>$this->setStockKode($this->kondisi),
            'active_cash'=>$this->activeCash,
            'kondisi'=>$this->kondisi,
            'gudang_id'=>$this->gudangId,
            'supplier_id'=>$this->supplierId,
            'tgl_masuk'=>$this->tglMasuk,
            'user_id'=>$this->userId,
            'nomor_po'=>$this->nomorPo,
            'nomor_surat_jalan'=>$this->suratJalan,
            'keterangan'=>$this->keterangan,
        ]);
    }

    // update stock masuk
    public function updateStockMasuk()
    {
        return $this->stockMasuk->update([
            'gudang_id'=>$this->gudangId,
            'supplier_id'=>$this->supplierId,
            'tgl_masuk'=>$this->tglMasuk,
            'user_id'=>$this->userId,
            'nomor_po'=>$this->nomorPo,
            'nomor_surat_jalan'=>$this->suratJalan,
            'keterangan'=>$this->keterangan,
        ]);
    }

    public function rollbackStockMasuk()
    {
        $stockRepository = new StockRepository();
        return $this->stockMasukDetail->delete();
    }

    // store stock masuk detail
    public function storeStockMasukDetail($dataItem)
    {
        // update stock inventory
        $stockRepository = new StockRepository();
        $stockRepository->stockInIncrement($this->kondisi, $this->gudangId, $dataItem['produk_id'], 'stock_masuk', $dataItem['jumlah']);
        // store stock masuk detail
        return $this->stockMasukDetail->create([
            'produk_id'=>$dataItem['produk_id'],
            'jumlah'=>$dataItem['jumlah'],
        ]);
    }
}
