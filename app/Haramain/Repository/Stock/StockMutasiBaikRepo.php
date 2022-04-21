<?php namespace App\Haramain\Repository\Stock;

use App\Haramain\Repository\Persediaan\PersediaanRepository;
use App\Haramain\Repository\StockMasuk\StockMasukRepository;
use App\Models\Stock\StockMutasi;

class StockMutasiBaikRepo
{
    public function kode()
    {
        return null;
    }

    public function store($data)
    {
        $kodeStockMutasi = $this->kode();
        // store stock mutasi
        $stockMutasi = StockMutasi::query()->create([
            'active_cash'=>session('ClosedCash'),
            'kode'=>$kodeStockMutasi,
            'jenis_mutasi'=>'baik_baik',
            'gudang_asal_id'=>$data->gudang_asal_id,
            'gudang_tujuan_id'=>$data->gudang_tujuan_id,
            'tgl_mutasi'=>tanggalan_database_format($data->tgl_mutasi, 'd-M-Y'),
            'user_id'=>\Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);
        // store data stock keluar
        $stockKeluar = $stockMutasi->stockKeluarMorph()->create([
            'kode'=>(new StockKeluarRepo)->kode(),
            'active_cash'=>session('ClosedCash'),
            'kondisi'=>'baik',
            'gudang_id'=>$data->gudang_asal_id,
            'tgl_keluar'=>$data->tgl_mutasi,
            'user_id'=>\Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);
        // store stock masuk
        $stockMasuk = $stockMutasi->stockMasukMorph()->create([
            'kode'=>(new StockMasukRepo())->kode('baik'),
            'active_cash'=>session('ClosedCash'),
            'kondisi'=>'baik',
            'gudang_id'=>$data->gudang_tujuan_id,
            'tgl_masuk'=>tanggalan_database_format($data->tgl_mutasi, 'd-M-Y'),
            'user_id'=>\Auth::id(),
            'nomor_surat_jalan'=>$data->nomor_surat_jalan,
            'keterangan'=>$data->keterangan,
        ]);
        // jurnal persediaan mutasi
        $jurnalPersediaanMutasi = $stockMutasi->jurnalPersediaanTransaksi()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$kodeStockMutasi,
                'gudang_asal_id'=>$data->gudang_asal_id,
                'gudang_tujuan_id'=>$data->gudang_tujuan_id,
                'jenis'=>'baik_baik',
                'user_id'=>\Auth::id(),
                'keterangan'=>$data->keterangan,
            ]);
        // store persediaan keluar
        $persediaanKeluar = $jurnalPersediaanMutasi->persediaanTransaksi()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$kodeStockMutasi,
                'jenis'=>'keluar', // masuk atau keluar
                'kondisi'=>'baik', // baik atau rusak
                'gudang_id'=>$data->gudang_asal_id,
                'debet',
                'kredit',
            ]);
        // store persediaan masuk
        $persediaanMasuk = $jurnalPersediaanMutasi->persediaanTransaksi()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$kodeStockMutasi,
                'jenis'=>'masuk', // masuk atau keluar
                'kondisi'=>'baik', // baik atau rusak
                'gudang_id'=>$data->gudang_tujuan_id,
                'debet',
                'kredit',
            ]);

        // store detail
        foreach ($data->data_detail as $item) {
            // stock mutasi detail
            $stockMutasi->stockMutasiDetail()->create([
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah'],
            ]);
            // stock keluar detail
            $stockKeluar->stockKeluarDetail()->create([
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah'],
            ]);
            // stock inventory keluar
            (new StockInventoryRepo())->incrementArrayData($item, $stockKeluar->gudang_id, 'baik', 'stock_keluar');
            // stock masuk detail
            $stockMasuk->stockMasukDetail()->create([
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah'],
            ]);
            // stock inventory keluar
            (new StockInventoryRepo())->incrementArrayData($item, $stockMasuk->gudang_id, 'baik', 'stock_masuk');
            // get persediaan repo
            $persediaanRepo = (new PersediaanRepository())->getProdukForMutasi($item['produk_id'], $data->gudang_asal_id, $item['jumlah']);

            foreach ($persediaanRepo as $row){
                // persediaan keluar detail
                $persediaanKeluar->persediaan_transaksi_detail()->create([
                    'produk_id'=>$row->produk_id,
                    'harga'=>$row->harga,
                    'jumlah'=>$row->jumlah,
                    'sub_total'=>$row->harga * $row->jumlah,
                ]);
                // update stock keluar
                (new PersediaanRepository())->store($persediaanKeluar, $row, 'stock_keluar');
                // persediaan masuk detail
                $persediaanMasuk->persediaan_transaksi_detail()->create([
                    'produk_id'=>$row->produk_id,
                    'harga'=>$row->harga,
                    'jumlah'=>$row->jumlah,
                    'sub_total'=>$row->harga * $row->jumlah,
                ]);
                // update stock masuk
                (new PersediaanRepository())->store($persediaanMasuk, $row, 'stock_masuk');
            }
        }
        return $stockMutasi;
    }

    public function update($data)
    {
        // initiate
        $stockInventoryRepo = new StockInventoryRepo();
        $stockMutasi = StockMutasi::query()->find($data->mutasi_id);
        $stockKeluar = $stockMutasi->stockKeluarMorph();
        $stockMasuk = $stockMutasi->stockMasukMorph();

        // rollback stock masuk
        foreach ($stockKeluar->stockKeluarDetail as $item) {
            //
        }
    }
}
