<?php namespace App\Haramain\SistemKeuangan\SubPersediaan\Transaksi;

use App\Models\Purchase\PembelianRetur;

class PersediaanTransaksiKeluarPembelianRetur extends PersediaanTransaksiRepository
{
    public function __construct(PembelianRetur $pembelianRetur)
    {
        parent::__construct();
        $this->jenis = 'keluar';
        $this->tglInput = $pembelianRetur->tgl_nota;
        $this->kondisi = $pembelianRetur->kondisi;
        $this->gudangId = $pembelianRetur->gudang_id;
        $this->persediaanableType = $pembelianRetur::class;
        $this->persediaanbleId = $pembelianRetur->id;

        $this->dataDetail = $pembelianRetur->returDetail;
    }
}
