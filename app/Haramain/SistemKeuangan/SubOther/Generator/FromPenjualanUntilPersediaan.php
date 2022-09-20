<?php namespace App\Haramain\SistemKeuangan\SubOther\Generator;


use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanKeluar;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanKeluarUpdate;
use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Penjualan\Penjualan;

class FromPenjualanUntilPersediaan
{
    public function generate()
    {
        $penjualanGet = $this->getPenjualan();
        // todo each
        foreach ($penjualanGet as $penjualan){
            // todo store persediaan transaksi keluar
            $persediaanTransaksi = $this->updateOrCreatePersediaanTransaksi($penjualan);
            $this->createPersediaanTransaksiDetail(
                $persediaanTransaksi,
                $this->storePenjualanDetailToArray($penjualan)
            );
        }
    }

    protected function getPenjualan()
    {
        return Penjualan::query()->where('active_cash', session('ClosedCash'))->get();
    }

    protected function updateOrCreatePersediaanTransaksi(Penjualan $penjualan)
    {
        $persediaanTransaksi = $penjualan->persediaan_transaksi();
        if ($persediaanTransaksi->doesntExist()){
            // todo create persediaan transaksi
            return $persediaanTransaksi->create();
        }
        $persediaanTransaksi = $persediaanTransaksi->first();
        $persediaanTransaksi->persediaan_transaksi_detail->delete();
        $persediaanTransaksi->update();
        return $persediaanTransaksi->refresh();
    }

    protected function createPersediaanTransaksiDetail(PersediaanTransaksi $persediaanTransaksi, array $detail)
    {
        return $persediaanTransaksi->persediaan_transaksi_detail()->createMany($detail);
    }

    protected function storePenjualanDetailToArray(Penjualan $penjualan): array
    {
        $detail = [];
        foreach ($penjualan->penjualanDetail as $item){
            $getPersediaan = PersediaanKeluar::set(
                'baik',
                $penjualan->gudang_id,
                $item->produk_id,
                $item->jumlah
            )->getData();
            foreach ($getPersediaan as $value) {
                $persediaan = PersediaanKeluarUpdate::set(
                    $value['persediaan_id'],
                    $value['jumlah']
                )->updateData();
                $detail[] = [
                    'persediaan_id'=>$persediaan->id,
                    'produk_id'=>$value['produk_id'],
                    'harga'=>$value['harga'],
                    'jumlah'=>$value['jumlah'],
                    'sub_total'=>$value['sub_total'],
                ];
            }
        }
        return $detail;
    }
}
