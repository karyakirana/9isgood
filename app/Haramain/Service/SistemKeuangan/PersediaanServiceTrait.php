<?php namespace App\Haramain\Service\SistemKeuangan;

trait PersediaanServiceTrait
{
    // initiate
    protected $persediaanTransaksi;
    protected $persediaanTransaksiDetail;
    protected $persediaan;

    // persediaan transaksi variabel
    protected $jenisPersediaan; // keluar atau masuk
    protected $tglInput;

    // persediaan jurnal transaksi
    protected $akunHppId, $akunPersediaanId;

    protected function setPersediaanKode()
    {
        return $this->kode;
    }

    protected function setPersediaanData($data)
    {
        $this->jenisPersediaan = $data['jenisPersediaan'];
        $this->tglInput = $this->tglNota;
    }

    public function storePersediaan()
    {
        return $this->persediaanTransaksi->create([
            'active_cash'=>session('ClosedCash'),
            'kode'=>$this->setPersediaanKode(),
            'jenis'=>$this->jenisPersediaan, // masuk atau keluar
            'kondisi'=>$this->kondisi, // baik atau rusak
            'gudang_id'=>$this->gudangId,
        ]);
    }

    public function storePersediaanDetailMasuk($dataItem)
    {
        $persediaan = new PersediaanRepository();
        // store persediaan masuk
        $persediaan->storeIn($dataItem, $this->kondisi, $this->tglInput, $this->gudangId);
        // store persediaan transaksi detail
        return $this->createPersediaanDetail($dataItem);
    }

    public function storePersediaanDetailKeluar($dataItem)
    {
        //
    }

    protected function createPersediaanDetail($dataItem)
    {
        return $this->persediaanTransaksiDetail->create([
            'produk_id'=>$dataItem['jumlah'],
            'harga'=>$dataItem['harga'],
            'jumlah'=>$dataItem['jumlah'],
            'sub_total'=>$dataItem['sub_total'],
        ]);
    }
}
