<?php namespace App\Haramain\SistemPembelian;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubKasir\HutangPembelianFromPembelian;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanTransaksiPembelian;
use App\Haramain\SistemStock\StockMasukPembelian;
use App\Models\Purchase\Pembelian;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PembelianService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;

    protected $akunHutangPembelian;
    protected $akunPersediaanKalimas;
    protected $akunPersediaanPerak;
    protected $akunPPNPembelian;
    protected $akunBiayaLainPembelian;

    public function __construct()
    {
        // akun pembelian
        $this->akunPembelianService();
    }

    public function handleStore($data)
    {
        DB::beginTransaction();
        try {
            // store pembelian
            $pembelian = PembelianRepository::build($data)->store();
            // store stock masuk
            StockMasukPembelian::build($pembelian, $data)->store();
            // store hutang pembelian
            HutangPembelianFromPembelian::build($pembelian)->store();
            // store persediaan transaksi
            PersediaanTransaksiPembelian::build($pembelian)->store();
            // store jurnal
            $this->jurnalPembelianService($pembelian);
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Sukses di simpan'
            ];
        }catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleUpdate($data)
    {
        DB::beginTransaction();
        try {
            // initiate
            $pembelian = Pembelian::find($data['pembelianId']);
            // rollback
            $this->rollback($pembelian);
            // update pembelian
            $pembelian = PembelianRepository::build($data)->update();
            // update stock masuk
            StockMasukPembelian::build($pembelian, $data)->update();
            // update hutang pembelian
            HutangPembelianFromPembelian::build($pembelian)->update();
            // update persediaan transaksi
            PersediaanTransaksiPembelian::build($pembelian)->update();
            // store jurnal transaksi dan neraca saldo
            $this->jurnalPembelianService($pembelian);
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Data Berhasil disimpan'
            ];
        }catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleGetData($id)
    {
        return Pembelian::find($id);
    }

    public function handleDestroy($id)
    {
        DB::beginTransaction();
        try {
            $pembelian = Pembelian::find($id);
            $this->rollback($pembelian);
            $pembelian->stockMasuk->delete();
            $pembelian->hutang_pembelian->delete();
            $pembelian->persediaan_transaksi->delete();
            $pembelian->delete();
            DB::commit();
            return (object)[
                'status'=>true
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    protected function rollback($pembelian)
    {
        // rollback stock masuk
        StockMasukPembelian::build($pembelian)->rollback();
        // rollback hutang pembelian
        HutangPembelianFromPembelian::build($pembelian)->rollback();
        // rollback persediaan transaksi
        PersediaanTransaksiPembelian::build($pembelian)->rollback();
        // rollback pembelian
        $pembelian->pembelianDetail()->delete();
        // rollback jurnal dan neraca saldo
        $this->rollbackJurnal($pembelian);
    }



    protected function rollbackJurnal($pembelian)
    {
        return $this->rollbackJurnalAndSaldo($pembelian);
    }
}
