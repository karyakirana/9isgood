<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Penjualan\PenjualanRetur;

class PersediaanTransaksiFromPenjualanRetur extends PersediaanTransaksiRepository
{
    protected $penjualanRetur;

    protected $persediaanMasukReturPenjualan;

    public function __construct(PenjualanRetur $penjualanRetur)
    {
        parent::__construct();

        $this->jenis = 'masuk';
        $this->tglInput = $penjualanRetur->tgl_nota;
        $this->kondisi = $penjualanRetur->jenis_retur;
        $this->gudangId = $penjualanRetur->gudang_id;
        $this->persediaanableType = $penjualanRetur::class;
        $this->persediaanbleId = $penjualanRetur->id;

        $this->dataDetail = $penjualanRetur->returDetail;
    }

    /**
     * @return array
     * @noinspection PhpMemberCanBePulledUpInspection
     */
    protected function detail():array
    {
        $detail = [];
        foreach ($this->dataDetail as $item){
            $this->setDataDetailFromArray($item);
            $persediaan = PersediaanMasukReturPenjualan::set(
                $this->gudangId,
                $this->kondisi,
                $this->tglInput,
                $this->produk_id,
                $this->harga,
                $this->jumlah
            )->update();
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

    /** @noinspection PhpMemberCanBePulledUpInspection */
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
