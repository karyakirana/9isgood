<?php namespace App\Haramain\SistemKeuangan\SubOther\Generator;

use App\Models\Keuangan\PersediaanOpname;
use App\Models\Keuangan\PersediaanOpnamePrice;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KoreksiStockOpname
{
    // todo get data stock opname gudang, kondisi, produk, dan harga
    // todo set harga stock opname tgl_input, kondisi, gudang, produk_id, harga

    public function generate()
    {
        DB::beginTransaction();
        try {
            $getPersediaanOpname = $this->getPersediaanOpname();
            //dd($getPersediaanOpname);
            foreach ($getPersediaanOpname as $persediaanOpname) {
                $this->setOpnamePrice($persediaanOpname);
            }
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'Persediaan Price Sudah di Koreksi'
            ];
        }catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    protected function getPersediaanOpname()
    {
        return PersediaanOpname::where('active_cash', session('ClosedCash'))->get();
    }

    protected function setOpnamePrice(PersediaanOpname $persediaanOpname):void
    {
        foreach ($persediaanOpname->persediaan_opname_detail as $opnameDetail) {
            $query = PersediaanOpnamePrice::query()
                ->where('active_cash', $persediaanOpname->active_cash)
                ->where('kondisi', $persediaanOpname->kondisi)
                ->where('gudang_id', $persediaanOpname->gudang_id)
                ->where('produk_id', $opnameDetail->produk_id)
                ->where('harga', $opnameDetail->harga);
            if ($query->doesntExist()){
                PersediaanOpnamePrice::query()->create([
                    'active_cash'=>session('ClosedCash'),
                    'tgl_input'=>tanggalan_format($persediaanOpname->created_at),
                    'kondisi'=>$persediaanOpname->kondisi,
                    'gudang_id'=>$persediaanOpname->gudang_id,
                    'produk_id'=>$opnameDetail->produk_id,
                    'harga'=>$opnameDetail->harga
                ]);
            }
        }
    }
}
