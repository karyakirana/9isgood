<?php namespace App\Haramain\SistemKeuangan\SubOther\Generator;

use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanMasuk;
use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Purchase\Pembelian;
use DB;

class FromPembelianUntilPersediaan
{
    protected $pembelianClass;
    protected $pembelianTable = 'haramainv2.pembelian';
    protected $persediaanTransaksiTable = 'haramain_keuangan.persediaan_transaksi_table';

    public function __construct()
    {
        $this->pembelianClass = Pembelian::class;
    }

    public function generate()
    {
        $pembelianGet = $this->getPembelian();
        // todo each
        foreach ($pembelianGet as $pembelian)
        {
            // todo store persediaan transaksi masuk
            $persediaanTransaksi = $this->updateOrCreatePersediaanTransaksi($pembelian);
            $this->createPersediaanTransaksiDetail(
                $persediaanTransaksi,
                $this->storePembelianDetailToArray($pembelian)
            );
        }
    }

    protected function getPembelian()
    {
        return Pembelian::where('active_cash', session('ClosedCash'))->get();
    }

    protected function updateOrCreatePersediaanTransaksi($pembelian)
    {
        $persediaanTransaksi = $pembelian->persediaan_transaksi();
        if ($persediaanTransaksi->doesntExist()){
            // todo create persediaan transaksi
            return $persediaanTransaksi->create();
        }
        // todo update persediaan transaksi
        $persediaanTransaksi = $persediaanTransaksi->first();
        $persediaanTransaksi->persediaan_transaksi_detail->delete();
        $persediaanTransaksi->update();
        return $persediaanTransaksi->refresh();
    }

    protected function createPersediaanTransaksiDetail(PersediaanTransaksi $persediaanTransaksi, array $detail)
    {
        return $persediaanTransaksi->persediaan_transaksi_detail()->createMany($detail);
    }

    protected function storePembelianDetailToArray(Pembelian $pembelian): array
    {
        $detail = [];
        foreach ($pembelian->pembelianDetail as $item){
            $persediaan = PersediaanMasuk::set(
                $pembelian->gudang_id,
                'baik',
                $pembelian->tgl_nota,
                $item->produk_id,
                $item->harga,
                $item->jumlah
            )->update();
            $detail[] = [
                'persediaan_id'=>$persediaan->id,
                'produk_id'=>$item->produk_id,
                'harga'=>$item->harga,
                'jumlah'=>$item->jumlah,
                'sub_total'=>$item->sub_total,
            ];
        }
        return $detail;
    }
}
