<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Penjualan\Penjualan;

class PersediaanTransaksiFromPenjualan extends PersediaanTransaksiRepository
{
    protected $penjualan;

    public function __construct(Penjualan $penjualan)
    {
        parent::__construct();

        $this->jenis = 'keluar';
        $this->tglInput = $penjualan->tgl_nota;
        $this->kondisi = 'baik';
        $this->gudangId = $penjualan->gudang_id;
        $this->persediaanableType = $penjualan::class;
        $this->persediaanbleId = $penjualan->id;

        $this->dataDetail = $penjualan->penjualanDetail;
    }

    protected function getDataFromPersediaan(): array
    {
        $data = [];
        foreach ($this->dataDetail as $item) {
            $data[] = PersediaanKeluar::set($this->kondisi, $this->gudangId, $item->produk_id, $item->jumlah)->getData();
        }
        return $data;
    }

    /**
     * @return array
     */
    protected function detail(): array
    {
        $detail = [];
        foreach ($this->getDataFromPersediaan() as $persediaan){
            foreach ($persediaan as $item){
                $this->setDataDetailFromArray($item);
                // update persediaan keluar
                $persediaan = PersediaanKeluarUpdate::set($this->persediaan_id, $this->jumlah)->updateData();
                $detail[] = [
                    'persediaan_id'=>$persediaan->id,
                    'produk_id'=>$this->produk_id,
                    'harga'=>$this->harga,
                    'jumlah'=>$this->jumlah,
                    'sub_total'=>$this->sub_total,
                ];
            }
        }
        $this->kredit = array_sum(array_column($detail, 'sub_total'));
        return $detail;
    }

    public function rollback()
    {
        // TODO rollback persediaan keluar
        $persediaanTransaksi = $this->getByPersediaanable();
        $persediaanTransaksiDetail = $persediaanTransaksi->persediaan_transaksi_detail;
        foreach ($persediaanTransaksiDetail as $item) {
            PersediaanRollback::set($item->persediaan_id, $item->jumlah)->rollbackStockKeluar();
        }
        // TODO delete persediaan detail
        return $persediaanTransaksi->persediaan_transaksi_detail()->delete();
    }
}
