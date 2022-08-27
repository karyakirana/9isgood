<?php namespace App\Haramain\Service\Generator;

use App\Haramain\Repository\Persediaan\PersediaanRepository;
use App\Haramain\Repository\Persediaan\PersediaanTransaksiRepo;
use App\Models\Keuangan\HargaHppALL;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanRetur;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GenPersediaanPenjualanService
{
    public function handleGeneratePenjualan()
    {
        \DB::beginTransaction();
        try {
            // get penjualan all
            $penjualanAll = Penjualan::query()->where('active_cash', session('ClosedCash'))->get();
            foreach ($penjualanAll as $item){
                // initiate
                $kondisi = 'baik';
                $penjualanDetail = $item->penjualanDetail;
                $persediaanTransaksi = $item->persediaan_transaksi();
                if ($persediaanTransaksi->count() > 0){
                    // update
                    $persediaanTransaksi = $persediaanTransaksi->first();
                    $persediaanTransaksi->update([
                        'tgl_input'=>$item->tgl_nota,
                        'kondisi'=>$kondisi, // baik atau rusak
                        'gudang_id'=>$item->gudang_id,
                    ]);
                } else {
                    // create
                    $persediaanTransaksi = $persediaanTransaksi->create([
                        'active_cash'=>session('ClosedCash'),
                        'kode'=>(new PersediaanTransaksiRepo())->kode(),
                        'jenis'=>'keluar', // masuk atau keluar
                        'tgl_input'=>$item->tgl_nota,
                        'kondisi'=>$kondisi, // baik atau rusak
                        'gudang_id'=>$item->gudang_id,
                    ]);
                }
                $persediaanTransaksiDetail = $persediaanTransaksi->persediaan_transaksi_detail();
                // delete first
                $persediaanTransaksiDetail->delete();
                // each detail
                foreach($penjualanDetail as $value){
                    // get stock out
                    $getPersediaanitem = (new PersediaanRepository())->getStockOut($item->gudang_id, $kondisi, $value, $item->tgl_nota);
                    // each persediaan
                    foreach ($getPersediaanitem as $row){
                        // store persediaan out
                        (new PersediaanRepository())->storeOut($row['persediaan_id'], $row['jumlah']);
                        // store transaksi detail
                        $persediaanTransaksiDetail->create([
                            'persediaan_id'=>$row['persediaan_id'],
                            'produk_id'=>$row['produk_id'],
                            'harga'=>$row['harga'],
                            'jumlah'=>$row['jumlah'],
                            'sub_total'=>$row['harga']*$row['jumlah'],
                        ]);
                    }
                }
            }
            \DB::commit();
            return [
                'status'=>true,
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleGeneratrPenjualanRetur()
    {
        \DB::beginTransaction();
        try {
            // harga hpp
            $hpp = HargaHppALL::query()->latest()->first()->persen;
            // get stock retur
            $penjualanRetur = PenjualanRetur::query()->where('active_cash', session('ClosedCash'));
            foreach ($penjualanRetur as $item) {
                $kondisi = $item->jenis_retur;
                $penjualanReturDetail = $item->returDetail();
                $persediaanTransaksi = $item->persediaan_transaksi();
                if ($persediaanTransaksi->count() > 0){
                    // update
                    $persediaanTransaksi = $persediaanTransaksi->first();
                    $persediaanTransaksi->update([
                        'jenis'=>'masuk', // masuk atau keluar
                        'tgl_input'=>$item->tgl_nota,
                        'kondisi'=>$item->jenis_retur, // baik atau rusak
                        'gudang_id'=>$item->gudang_id,
                    ]);
                } else {
                    // create
                    $persediaanTransaksi = $persediaanTransaksi->create([
                        'active_cash'=>session('ClosedCash'),
                        'kode'=>(new PersediaanTransaksiRepo())->kode(),
                        'jenis'=>'masuk', // masuk atau keluar
                        'tgl_input'=>$item->tgl_nota,
                        'kondisi'=>$item->jenis_retur, // baik atau rusak
                        'gudang_id'=>$item->gudang_id,
                    ]);
                }
                $persediaanTransaksiDetail = $persediaanTransaksi->persediaan_transaksi_detail();
                // delete first
                $persediaanTransaksiDetail->delete();
                // each detail
                foreach ($penjualanReturDetail as $row){
                    $hargaHpp = $row->harga * $hpp;
                    // store persediaan
                    $persediaan = (new PersediaanRepository())->storeInLine($item->gudang_id, $kondisi, $item->tgl_nota, $row->produk_id, $hargaHpp, $row->jumlah);
                    $persediaanTransaksiDetail->create([
                        'persediaan_id'=>$persediaan->id,
                        'produk_id'=>$persediaan->produk_id,
                        'harga'=>$hargaHpp,
                        'jumlah'=>$row->jumlah,
                        'sub_total'=>$hargaHpp*$row->jumlah,
                    ]);
                }
            }
            \DB::commit();
            return [
                'status'=>true,
            ];
        }catch (ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }
}
