<?php namespace App\Haramain\Service\Generator;

use App\Haramain\Repository\Persediaan\PersediaanRepository;
use App\Haramain\Repository\Persediaan\PersediaanTransaksiRepo;
use App\Models\Stock\StockMutasi;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GenPersediaanMutasiService
{
    public function handleGenerate()
    {
        \DB::beginTransaction();
        try{
            $stockMutasiAll = StockMutasi::query()->where('active_cash', session('ClosedCash'))->get();
            // each stock
            foreach ($stockMutasiAll as $item) {
                $kondisiKeluar = \Str::before($item->jenis_mutasi, '_');
                $kondisiMasuk = \Str::after($item->jenis_mutasi, '_');
                $stockMutasiDetail = $item->stockMutasiDetail;
                $persediaanMutasi = $item->persediaanMutasi();
                if ($persediaanMutasi->count() > 0){
                    // update
                    $persediaanMutasi = $persediaanMutasi->first();
                    $persediaanMutasi->update([
                        'jenis_mutasi'=>$item->jenis_mutasi,
                        'gudang_asal_id'=>$item->gudang_asal_id,
                        'gudang_tujuan_id'=>$item->gudang_tujuan_id,
                    ]);
                } else {
                    // create
                    $persediaanMutasi = $persediaanMutasi->create([
                        'jenis_mutasi'=>$item->jenis_mutasi,
                        'gudang_asal_id'=>$item->gudang_asal_id,
                        'gudang_tujuan_id'=>$item->gudang_tujuan_id,
                        'total_barang'=>0,
                        'total_harga'=>0,
                    ]);
                }
                $persediaanTransaksi = $persediaanMutasi->persediaan_transaksi();
                $persediaanTransaksiMasuk = $persediaanTransaksi->where('jenis', 'masuk');
                $persediaanTransaksiKeluar = $persediaanTransaksi->where('jenis', 'keluar');
                if ($persediaanTransaksiKeluar->count() > 0){
                    // update
                    $persediaanTransaksiKeluar->first()->update([
                        'jenis'=>'keluar', // masuk atau keluar
                        'tgl_input'=>$item->tgl_mutasi,
                        'kondisi'=>$kondisiKeluar, // baik atau rusak
                        'gudang_id'=>$item->gudang_asal_id,
                    ]);
                    $persediaanTransaksiKeluar = $persediaanTransaksiKeluar->first();
                } else {
                    // create
                    $persediaanTransaksiKeluar = $persediaanTransaksi->create([
                        'active_cash'=>session('ClosedCash'),
                        'kode'=>(new PersediaanTransaksiRepo())->kode(),
                        'jenis'=>'keluar', // masuk atau keluar
                        'tgl_input'=>$item->tgl_mutasi,
                        'kondisi'=>$kondisiKeluar, // baik atau rusak
                        'gudang_id'=>$item->gudang_asal_id,
                    ]);
                }
                if ($persediaanTransaksiMasuk->count() > 0){
                    // update
                    $persediaanTransaksiMasuk->first()->update([
                        'jenis'=>'masuk', // masuk atau keluar
                        'tgl_input'=>$item->tgl_mutasi,
                        'kondisi'=>$kondisiMasuk, // baik atau rusak
                        'gudang_id'=>$item->gudang_tujuan_id,
                    ]);
                    $persediaanTransaksiMasuk = $persediaanTransaksiMasuk->first();
                } else {
                    // create
                    $persediaanTransaksiMasuk = $persediaanTransaksi->create([
                        'active_cash'=>session('ClosedCash'),
                        'kode'=>(new PersediaanTransaksiRepo())->kode(),
                        'jenis'=>'masuk', // masuk atau keluar
                        'tgl_input'=>$item->tgl_mutasi,
                        'kondisi'=>$kondisiMasuk, // baik atau rusak
                        'gudang_id'=>$item->gudang_tujuan_id,
                    ]);
                }
                // persediaan transaksi detail
                $persediaanTransaksiDetailKeluar = $persediaanTransaksiKeluar->persediaan_transaksi_detail();
                $persediaanTransaksiDetailMasuk = $persediaanTransaksiMasuk->persediaan_transaksi_detail();
                // delete persediaan transaksi detail first
                $persediaanTransaksiDetailKeluar->delete();
                $persediaanTransaksiDetailMasuk->delete();
                // each detail
                foreach ($stockMutasiDetail as $value) {
                    // get persediaan
                    $getPersediaan = (new PersediaanRepository())->getStockOut($item->gudang_asal_id, $kondisiKeluar, $value, $item->tgl_mutasi);
                    // each item for persediaan
                    foreach ($getPersediaan as $row){
                        // transaksi keluar
                        (new PersediaanRepository())->storeOut($row['persediaan_id'], $row['jumlah']);
                        $persediaanTransaksiDetailKeluar->create([
                            'persediaan_id'=>$row['persediaan_id'],
                            'produk_id'=>$row['produk_id'],
                            'harga'=>$row['harga'],
                            'jumlah'=>$row['jumlah'],
                            'sub_total'=>$row['harga']*$row['jumlah'],
                        ]);
                        // transaksi masuk
                        (new PersediaanRepository())->storeIn($item->gudang_tujuan_id, $kondisiMasuk, $item->tgl_mutasi, $row);
                        $persediaanTransaksiDetailMasuk->create([
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
}
