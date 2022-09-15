<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Purchase\Pembelian;

class PersediaanTransaksiPembelian extends PersediaanTransaksiRepository
{
    public function __construct(Pembelian $pembelian)
    {
        parent::__construct();

        $this->jenis = 'masuk';
        $this->tglInput = $pembelian->tgl_nota;
        $this->kondisi = 'baik';
        $this->gudangId = $pembelian->gudang_id;
        $this->persediaanableType = $pembelian::class;
        $this->persediaanbleId = $pembelian->id;

        $this->dataDetail = $pembelian->pembelianDetail;
    }

    protected function detail():array
    {
        $detail = [];
        foreach ($this->dataDetail as $item){
            $this->setDataDetailFromArray($item);
            $persediaan = PersediaanMasuk::set(
                $this->gudangId,
                $this->kondisi,
                $this->tglInput,
                $this->produk_id,
                $this->jumlah,
                $this->jumlah
            )
                ->update();
            $detail[] = [
                'persediaan_id'=>$persediaan->id,
                'produk_id'=>$this->produk_id,
                'harga'=>$this->harga,
                'jumlah'=>$this->jumlah,
                'sub_total'=>$this->sub_total,
            ];
        }
        $this->debet = array_sum(array_column($detail, 'sub_total'));
        return $detail;
    }

    public function rollback()
    {
        // TODO rollback persediaan keluar
        $persediaanTransaksi = $this->getByPersediaanable();
        $persediaanTransaksiDetail = $persediaanTransaksi->persediaan_transaksi_detail;
        foreach ($persediaanTransaksiDetail as $item) {
            PersediaanRollback::set($item->persediaan_id, $item->jumlah)->rollbackStockMasuk();
        }
        // TODO delete persediaan detail
        return $persediaanTransaksi->persediaan_transaksi_detail()->delete();
    }
}
