<?php namespace App\Haramain\SistemKeuangan\SubPersediaan\Opname;

use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanForOpname;
use App\Models\Keuangan\PersediaanOpnameKoreksi;
use App\Models\Stock\StockOpnameKoreksi;

class PersediaanOpnameKoreksiRepository
{
    protected $stockOpnameKoreksiId;

    protected $activeCash;
    protected $jenis;
    protected $kondisi;
    protected $gudangId;
    protected $userId;

    protected $dataDetail;

    public function __construct(StockOpnameKoreksi $stockOpnameKoreksi)
    {
        $this->stockOpnameKoreksiId = $stockOpnameKoreksi->id;
        $this->activeCash = $stockOpnameKoreksi->active_cash;
        $this->jenis = $stockOpnameKoreksi->jenis;
        $this->kondisi = $stockOpnameKoreksi->kondisi;
        $this->gudangId = $stockOpnameKoreksi->gudang_id;
        $this->userId = $stockOpnameKoreksi->user_id;

        $this->dataDetail = $stockOpnameKoreksi->stockOpnameKoreksiDetail;
    }

    public static function build($stockOpnameKoreksi)
    {
        return new static($stockOpnameKoreksi);
    }

    protected function getDataByStockOpnameKoreksiId()
    {
        return PersediaanOpnameKoreksi::where('stock_opname_koreksi_id', $this->stockOpnameKoreksiId)->first();
    }

    public function store()
    {
        $persediaanOpnameKoreksi =$this->updateOrCreate();
        $persediaanOpnameKoreksi->persediaanOpnameKoreksiDetail()->createMany($this->setDetail());
        return $persediaanOpnameKoreksi;
    }

    protected function updateOrCreate()
    {
        $data = $this->setData();
        $persediaanOpnameKoreksi = $this->getDataByStockOpnameKoreksiId();
        if ($persediaanOpnameKoreksi){
            // update
            $persediaanOpnameKoreksi->update($data);
            return $persediaanOpnameKoreksi->refresh();
        }
        // create
        return  PersediaanOpnameKoreksi::create($data);
    }

    protected function setData()
    {
        return [
            'active_cash'=>$this->activeCash,
            'stock_opname_koreksi_id'=>$this->stockOpnameKoreksiId,
            'jenis'=>$this->jenis, // tambah or kurang
            'kondisi'=>$this->kondisi,
            'gudang_id'=>$this->gudangId,
            'user_id'=>$this->userId
        ];
    }

    protected function setDetail()
    {
        $detail = [];
        foreach ($this->dataDetail as $stockOpnameKoreksiDetail){
            // get price from opname price
            $price = PersediaanOpnamePriceRepository::getData($this->kondisi, $stockOpnameKoreksiDetail->produk_id);
            // update stock opname
            $persediaan = PersediaanForOpname::build([
                'kondisi'=>$this->kondisi,
                'tglInput'=>null,
                'gudangId'=>$this->gudangId,
                'produkId'=>$stockOpnameKoreksiDetail->produk_id,
                'harga'=>$price->harga,
                'jumlah'=>$stockOpnameKoreksiDetail->jumlah,
                'type'=>'increment'
            ])->store();
            $detail[] = [
                'persediaan_id'=>$persediaan->id,
                'produk_id'=>$stockOpnameKoreksiDetail->produk_id,
                'harga'=>$price->harga,
                'jumlah'=>$stockOpnameKoreksiDetail->jumlah,
                'sub_total'=>$price->harga * $stockOpnameKoreksiDetail->jumlah
            ];
        }
        return $detail;
    }
}
