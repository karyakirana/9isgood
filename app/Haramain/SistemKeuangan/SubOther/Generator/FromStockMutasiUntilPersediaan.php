<?php namespace App\Haramain\SistemKeuangan\SubOther\Generator;

use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanKeluar;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanKeluarUpdate;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanMasuk;
use App\Haramain\SistemKeuangan\SubPersediaan\Transaksi\PersediaanTransaksiRepository;
use App\Models\Keuangan\PersediaanMutasi;
use App\Models\Stock\StockMutasi;
use Str;

class FromStockMutasiUntilPersediaan
{
    public function generate()
    {
        $stockMutasiGet = $this->getStockMutasi();
        foreach ($stockMutasiGet as $stockMutasi){
            // set detail mutasi
            $setDetail = $this->setDataDetailMutasi($stockMutasi);
            // store persediaan mutasi
            $persediaanMutasi = $this->updateOrCreatePersediaanMutasi($stockMutasi, $setDetail['totalBarang'], $setDetail['totalHarga']);
            // transaksi keluar
            $transaksiKeluar = $this->persediaanTransaksi($persediaanMutasi, 'keluar', $setDetail['totalHarga']);
            $transaksiKeluar->persediaan_transaksi_detail()->createMany($setDetail['detailKeluar']);
            // transaksi masuk
            $transaksiMasuk = $this->persediaanTransaksi($persediaanMutasi, 'masuk', $setDetail['totalHarga']);
            $transaksiMasuk->persediaan_transaksi_detail()->createMany($setDetail['detailMasuk']);
        }
    }

    public function getStockMutasi()
    {
        return StockMutasi::where('active_cash', session('ClosedCash'))->get();
    }

    protected function updateOrCreatePersediaanMutasi(StockMutasi $stockMutasi, $totalBarang, $totalHarga)
    {
        $persediaanMutasi = $stockMutasi->persediaanMutasi();
        $data = [
            'jenis_mutasi'=>$stockMutasi->jenis_mutasi,
            'gudang_asal_id'=>$stockMutasi->gudang_asal_id,
            'gudang_tujuan_id'=>$stockMutasi->gudang_tujuan_id,
            'total_barang'=>$totalBarang,
            'total_harga'=>$totalHarga,
        ];
        if ($persediaanMutasi->doesntExist()){
            // todo create persediaan mutasi
            return $persediaanMutasi->create($data);
        }
        // todo update persediaan mutasi
        $persediaanMutasi = $persediaanMutasi->first();
        $persediaanMutasi->update($data);
        return $persediaanMutasi->refresh();
    }

    protected function setDataDetailMutasi(StockMutasi $stockMutasi)
    {
        $kondisiKeluar = Str::before($stockMutasi, '_');
        $kondisiMasuk = Str::after($stockMutasi, '_');
        $detailKeluar = [];
        $detailMasuk = [];
        foreach ($stockMutasi->stockMutasiDetail as $item) {
            // todo get persediaan detail keluar
            $getPersediaan = PersediaanKeluar::set(
                $kondisiKeluar,
                $stockMutasi->gudang_asal_id,
                $item->produk_id,
                $item->jumlah
            )->getData();
            foreach ($getPersediaan as $value){
                // todo update persediaan keluar
                $persediaanKeluar = PersediaanKeluarUpdate::set(
                    $value['persediaan_id'],
                    $value['jumlah']
                )->updateData();
                $detailKeluar[] = [
                    'persediaan_id'=>$persediaanKeluar->id,
                    'produk_id'=>$value['produk_id'],
                    'harga'=>$value['harga'],
                    'jumlah'=>$value['jumlah'],
                    'sub_total'=>$value['sub_total'],
                ];
                // todo update persediaan masuk
                $persediaanMasuk = PersediaanMasuk::set(
                    $stockMutasi->gudang_tujuan_id,
                    $kondisiMasuk,
                    $stockMutasi->tgl_mutasi,
                    $value['produk_id'],
                    $value['harga'],
                    $value['jumlah'],
                )->update();
                $detailMasuk[] = [
                    'persediaan_id'=>$persediaanMasuk->id,
                    'produk_id'=>$value['produk_id'],
                    'harga'=>$value['harga'],
                    'jumlah'=>$value['jumlah'],
                    'sub_total'=>$value['sub_total'],
                ];
            }
        }
        return [
            'detailKeluar'=>$detailKeluar,
            'detailMasuk'=>$detailMasuk,
            'totalBarang'=>array_sum(array_column($detailKeluar, 'jumlah')),
            'totalHarga'=>array_sum(array_column($detailKeluar, 'sub_total'))
        ];
    }

    protected function persediaanTransaksi(PersediaanMutasi $persediaanMutasi, $jenis, $totalHarga)
    {
        $persediaanTransaksi = $persediaanMutasi->persediaan_transaksi->firstWhere('jenis', $jenis);
        $data = [
            'active_cash'=>session('ClosedCash'),
            'kode'=>PersediaanTransaksiRepository::getKode(),
            'jenis'=>$jenis, // masuk atau keluar
            'tgl_input'=>$persediaanMutasi->stockMutasi->tgl_mutasi,
            'kondisi'=>($jenis == 'keluar') ? Str::before($persediaanMutasi->jenis_mutasi, '_') : Str::after($persediaanMutasi->jenis_mutasi, '_'), // baik atau rusak
            'gudang_id'=>($jenis == 'keluar') ? $persediaanMutasi->gudang_asal_id : $persediaanMutasi->gudang_tujuan_id,
            'debet'=>($jenis == 'mausk') ? $totalHarga : null,
            'kredit'=>($jenis == 'keluar') ? $totalHarga : null,
        ];
        if ($persediaanTransaksi){
            // todo create persedian transaksi
            return $persediaanMutasi->persediaan_transaksi()->create($data);
        }
        // todo update persediaan transaksi
        unset($data['kode']);
        $persediaanTransaksi->update($data);
        return $persediaanTransaksi->refresh();
    }
}
