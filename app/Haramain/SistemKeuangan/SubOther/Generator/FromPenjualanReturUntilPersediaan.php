<?php namespace App\Haramain\SistemKeuangan\SubOther\Generator;

use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanMasukReturPenjualan;
use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Penjualan\PenjualanRetur;

class FromPenjualanReturUntilPersediaan
{
    public function generate()
    {
        $penjualanReturGet = $this->getPenjualanRetur();
        // todo eaach
        foreach ($penjualanReturGet as $penjualanRetur) {
            // todo store persediaan masuk
            $persediaanTransaksi = $this->updateOrCreatePersediaanTransaksi($penjualanRetur);
            $this->createPersediaanTransaksiDetail(
                $persediaanTransaksi,
                $this->storePenjualanReturDetailToArray($penjualanRetur)
            );
        }
    }

    protected function getPenjualanRetur()
    {
        return PenjualanRetur::where('active_cash', session('ClosedCash'))->get();
    }

    protected function updateOrCreatePersediaanTransaksi(PenjualanRetur $penjualanRetur)
    {
        $persediaanTransaksi = $penjualanRetur->persediaan_transaksi();
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

    protected function storePenjualanReturDetailToArray(PenjualanRetur $penjualanRetur): array
    {
        $detail = [];
        foreach ($penjualanRetur->returDetail as $item){
            $persediaan = PersediaanMasukReturPenjualan::set(
                $penjualanRetur->gudang_id,
                $penjualanRetur->jenis_retur,
                $penjualanRetur->tgl_nota,
                $item->produk_id,
                $item->harga,
                $item->jumlah
            )->update();
            $detail[] = [
                'persediaan_id'=>$persediaan->id,
                'produk_id'=>$persediaan->produk_id,
                'harga'=>$persediaan->harga,
                'jumlah'=>$item->jumlah,
                'sub_total'=>$persediaan->harga * $item->jumlah
            ];
        }
        return $detail;
    }
}
