<?php namespace App\Haramain\Service\Generator;

use App\Haramain\Repository\Persediaan\PersediaanRepository;
use App\Haramain\Repository\Persediaan\PersediaanTransaksiRepo;
use App\Models\Purchase\Pembelian;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GenPembelianInternalService
{
    public function handleGenerate()
    {
        \DB::beginTransaction();
        try {
            // get pembelian
            $dataPembelianAll = Pembelian::query()->where('active_cash', session('ClosedCash'))->get();
            // each pembelian
            foreach ($dataPembelianAll as $item) {
                // store persediaan transaksi
                $pembelianDetail = $item->pembelianDetail;
                $persediaanTransaksi = $item->persediaan_transaksi();
                if ($persediaanTransaksi->count() > 0){
                    // update
                    $updatePersediaanTransaksi = $persediaanTransaksi->first()
                        ->update([
                            'tgl_input'=>$item->tgl_nota,
                            'kondisi'=>'baik', // baik atau rusak
                            'gudang_id'=>$item->gudang_id,
                        ]);
                    $persediaanTransaksiDetail = $persediaanTransaksi->first()->persediaan_transaksi_detail();
                    // delete first
                    $persediaanTransaksiDetail->delete();
                } else {
                    // create
                    $storePersediaanTransaksi = $persediaanTransaksi->create([
                        'active_cash'=>session('ClosedCash'),
                        'kode'=>(new PersediaanTransaksiRepo())->kode(),
                        'jenis'=>'masuk', // masuk atau keluar
                        'tgl_input'=>$item->tgl_nota,
                        'kondisi'=>'baik', // baik atau rusak
                        'gudang_id'=>$item->gudang_id,
                    ]);
                    $persediaanTransaksiDetail = $storePersediaanTransaksi->persediaan_transaksi_detail();
                }
                // each pembelian detail
                foreach ($pembelianDetail as $value) {
                    // update or create persediaan
                    $persediaan = (new PersediaanRepository())->storeInObject($item->gudang_id, 'baik', $item->tgl_nota, $value);
                    $persediaanTransaksiDetail->create([
                        'persediaan_id'=>$persediaan->id,
                        'produk_id'=>$persediaan->produk_id,
                        'harga'=>$value->harga,
                        'jumlah'=>$value->jumlah,
                        'sub_total'=>$value->sub_total,
                    ]);
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

    /**
     * stock masuk proses
     */
    private function storeStockMasuk($data, $stockableType, $stockableId)
    {
        //
    }

    /**
     * persediaan transaksi proses
     */
    private function storePersediaan($data, $persediaanType, $persediaanId)
    {
        //
    }
}
