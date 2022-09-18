<?php namespace App\Haramain\SistemPenjualan;

use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubKasir\PiutangPenjualanFromRetur;
use App\Haramain\SistemKeuangan\SubPersediaan\Transaksi\PersediaanTransaksiFromPenjualanRetur;
use App\Haramain\SistemStock\StockMasukPenjualanRetur;
use App\Models\Penjualan\PenjualanRetur;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenjualanReturService
{
    use JurnalTransaksiServiceTrait;

    protected $penjualanReturRepo;
    protected $stockMasukRepository;
    protected $piutangPenjualanRepo;
    protected $persediaanTransaksiRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepository;

    public function __construct()
    {
        // penjualan retur
        $this->akunPenjualanReturService();
    }

    public function handleGetData($penjualanReturId)
    {
        return PenjualanRetur::find($penjualanReturId);
    }

    /**
     * alur penjualan retur:
     * @param $data
     * @return object
     */
    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            $penjualanRetur = PenjualanReturRepository::build($data)->store();
            $stockMasuk = StockMasukPenjualanRetur::build($penjualanRetur)->store();
            PiutangPenjualanFromRetur::build($penjualanRetur)->store();
            $persediaanTransaksi = PersediaanTransaksiFromPenjualanRetur::build($penjualanRetur)->store();
            $this->jurnalPenjualanReturService($penjualanRetur, $persediaanTransaksi);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penjualanRetur
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            $penjualanRetur = $this->handleGetData($data['penjualanReturId']);
            $this->rollback($penjualanRetur);
            $penjualanRetur = PenjualanReturRepository::build($data)->update();
            $stockMasuk = StockMasukPenjualanRetur::build($penjualanRetur)->update();
            PiutangPenjualanFromRetur::build($penjualanRetur)->update();
            $persediaanTransaksi = PersediaanTransaksiFromPenjualanRetur::build($penjualanRetur)->update();
            $this->jurnalPenjualanReturService($penjualanRetur, $persediaanTransaksi);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penjualanRetur
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleDestroy($penjualanReturId)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
    }

    protected function rollback($penjualanRetur)
    {
        // stock masuk
        StockMasukPenjualanRetur::build($penjualanRetur)->rollback();
        // persediaan transaksi
        PersediaanTransaksiFromPenjualanRetur::build($penjualanRetur)->rollback();
        // penjualan retur
        $penjualanRetur->returDetail()->delete();
        // jurnal rollback
        $this->rollbackJurnalAndSaldo($penjualanRetur);
    }

}
