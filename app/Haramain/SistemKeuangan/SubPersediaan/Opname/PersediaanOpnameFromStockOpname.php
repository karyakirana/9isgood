<?php namespace App\Haramain\SistemKeuangan\SubPersediaan\Opname;

use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Haramain\SistemKeuangan\SubOther\KonfigurasiJurnalRepository;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanForOpname;
use App\Models\Keuangan\Persediaan;
use App\Models\Keuangan\PersediaanOpname;
use App\Models\Stock\StockOpname;

class PersediaanOpnameFromStockOpname
{
    protected $akunPersediaanKalimas;
    protected $akunPersediaanPerak;
    protected $akunModalAwal;

    public function __construct()
    {
        $this->akunPersediaanKalimas = KonfigurasiJurnalRepository::build('persediaan_baik_kalimas')->getAkun();
        $this->akunPersediaanPerak = KonfigurasiJurnalRepository::build('persediaan_baik_perak')->getAkun();
        $this->akunModalAwal = KonfigurasiJurnalRepository::build('prive_modal_awal')->getAkun();
    }

    public function generate()
    {
        // todo persediaan opname set to 0
        Persediaan::where('active_cash')->update(['stock_opname'=> 0]);
        // todo get stock opname
        $getStockOpname = $this->getStockOpname();
        foreach ($getStockOpname as $stockOpname) {
            $persediaanOpname = $this->storeToPersediaan($stockOpname);
            $persediaanOpname->persediaan_opname_detail()->createMany($this->setDetail($stockOpname));
            $this->jurnal($persediaanOpname->refresh());
        }
    }

    protected function jurnal(PersediaanOpname $persediaanOpname)
    {
        $jurnalTransaksi = JurnalTransaksiRepo::build($persediaanOpname);
        $sumSubTotal = $persediaanOpname->persediaan_opname_detail->sum('sub_total');
        // todo persediaan debet
        if ($persediaanOpname->gudang_id == 1){
            $jurnalTransaksi->debet($this->akunPersediaanKalimas, $sumSubTotal);
            NeracaSaldoRepository::debet($this->akunPersediaanKalimas, $sumSubTotal);
        }
        if ($persediaanOpname->gudang_id == 2){
            $jurnalTransaksi->debet($this->akunPersediaanPerak, $sumSubTotal);
            NeracaSaldoRepository::debet($this->akunPersediaanPerak, $sumSubTotal);
        }
        // todo modal awal kredit
        $jurnalTransaksi->kredit($this->akunModalAwal, $sumSubTotal);
        NeracaSaldoRepository::kredit($this->akunModalAwal, $sumSubTotal);
    }

    protected function getStockOpname()
    {
        return StockOpname::where('active_cash', session('ClosedCash'))->get();
    }

    protected function storeToPersediaan(StockOpname $stockOpname)
    {
        $data = [
            'kode'=>PersediaanOpnameRepository::getKode($stockOpname->jenis),
            'active_cash'=>$stockOpname->active_cash,
            'kondisi'=>$stockOpname->jenis,
            'gudang_id'=>$stockOpname->gudang_id,
            'user_id'=>$stockOpname->user_id,
            'keterangan'=>$stockOpname->keterangan,
        ];
        $persediaanOpname = $stockOpname->persediaanOpname();
        if ($persediaanOpname->doesntExist()){
            // create
            return $persediaanOpname->create($data);
        }
        unset($data['kode']);
        // update
        $persediaanOpname->update($data);
        return $persediaanOpname->first()->refresh();
    }

    protected function setDetail(StockOpname $stockOpname)
    {
        $detail = [];
        foreach ($stockOpname->stockOpnameDetail as $stockOpnameDetail)
        {
            // todo get persediaan from persediaan opname price
            $price = PersediaanOpnamePriceRepository::getData($stockOpname->jenis, $stockOpnameDetail->produk_id);
            // todo set to persediaan
            PersediaanForOpname::build([
                'kondisi'=>$stockOpname->jenis,
                'tglInput'=>$stockOpname->tgl_input,
                'gudangId'=>$stockOpname->gudang_id,
                'produkId'=>$stockOpnameDetail->produk_id,
                'harga'=>$price->harga,
                'jumlah'=>$stockOpnameDetail->jumlah,
                'type'=>'increment'
            ])->store();
            // todo set detail
            $detail[] = [
                'produk_id'=>$stockOpnameDetail->produk_id,
                'jumlah'=>$stockOpnameDetail->jumlah,
                'harga'=>$price->harga,
                'sub_total'=>$stockOpnameDetail->jumlah * $price->harga,
            ];
        }
        return $detail;
    }
}
