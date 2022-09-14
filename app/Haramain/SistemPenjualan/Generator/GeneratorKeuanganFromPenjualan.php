<?php namespace App\Haramain\SistemPenjualan\Generator;

use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanKeluar;
use App\Models\Keuangan\Persediaan;
use App\Models\Penjualan\Penjualan;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GeneratorKeuanganFromPenjualan
{
    // penjualan attribute
    // akun attribute
    protected $akunPiutangUsaha;
    protected $akunPenjualan;
    protected $ppnPenjualan;
    protected $biayaPenjualan;
    protected $akunHppInternal;
    protected $akunHppBukuLuar;

    public function __construct()
    {
        // TODO : Construct
    }

    public function cleanUp()
    {
        // TODO : cleanup jurnal Transaksi
        // TODO : cleanup Neraca Saldo
        // TODO : cleanup akun from penjualan
        // TODO : cleanup persediaan
    }

    /**
     * @param $kondisi
     * @param $gudangId
     * @param $produkId
     * @param $jumlah
     * @return array
     */
    protected function getPersediaan($kondisi, $gudangId, $produkId, $jumlah): array
    {
        return PersediaanKeluar::set($kondisi, $gudangId, $produkId, $jumlah)->getData();
    }

    public function generate()
    {
        // TODO: GET Penjualan
        $penjualanAll = Penjualan::query()
            ->where('active_cash', session('ClosedCash'));
        if ($penjualanAll->doesntExist()){
            throw new ModelNotFoundException('Penjualan Kosong');
        }
        foreach ($penjualanAll->get() as $penjualan) {
            // TODO : store persediaan transaksi
            // TODO: Get Penjualan Detail
            $penjualanDetail = $penjualan->penjualanDetail;
            // TODO : initiate buku luar
            $hppBukuLuar = 0;
            $hppBukuInternal = 0;
            // TODO : initiate buku internal
            foreach ($penjualanDetail as $detail) {
                $kategori = $detail->produk->kategori->kode_lokal;
                // TODO : Get Perediaan
                $persediaan = $this->getPersediaan('baik', $penjualan->gudang_id, $detail->produk_id, $detail->jumlah);
                // TODO : each persediaan
                foreach ($persediaan as $item) {
                    // TODO : store persediaan detail
                    // TODO : set HPP item
                    if ($kategori === 'BLU' || $kategori === 'BL'){
                        // TODO: increment hpp buku luar
                        $hppBukuLuar += $item['jumlah'];
                    } else {
                        // TODO: increment hpp buku internal
                        $hppBukuInternal += $item['jumlah'];
                    }
                }
            }
        }
    }

    protected function storePersediaanTransaksi()
    {
        //
    }

    protected function storePersediaanTransaksiDetail()
    {
        //
    }
}
