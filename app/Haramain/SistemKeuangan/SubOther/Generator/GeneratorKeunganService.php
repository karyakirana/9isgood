<?php namespace App\Haramain\SistemKeuangan\SubOther\Generator;

use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GeneratorKeunganService
{
    // todo clear persediaan

    // todo generate from stock opname until persediaan
    public function handleFromStockOpnameUntilPersediaan()
    {
        // todo get data stock opname (persediaan price and persediaan opname done)
    }

    // todo generate from pembelian until persediaan
    public function handleFromPembelianUntilPersediaan()
    {
        DB::beginTransaction();
        try {
            (new FromPembelianUntilPersediaan())->generate();
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'Data Pembelian Berhasil di generate'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    // todo generate from mutasi stock until persediaan
    public function handleFromMutasiuntilPersediaan()
    {
        DB::beginTransaction();
        try {
            (new FromStockMutasiUntilPersediaan())->generate();
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'Data Pembelian Berhasil di generate'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    // todo generate from penjualan until persediaan
    public function handleFromPenjualanUntilPersediaan()
    {
        DB::beginTransaction();
        try {
            (new FromPenjualanUntilPersediaan())->generate();
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'Data Penjualan Berhasil di generate'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    // todo generate from penjualan retur until persediaan
    public function handleFromPenjualanReturUntilPersediaan()
    {
        DB::beginTransaction();
        try {
            (new FromPenjualanReturUntilPersediaan())->generate();
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'Data Pembelian Berhasil di generate'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }
}
