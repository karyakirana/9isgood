<?php namespace App\Haramain\SistemPenjualan;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubKasir\PiutangPenjualanFromPenjualan;
use App\Haramain\SistemKeuangan\SubPersediaan\Transaksi\PersediaanTransaksiFromPenjualan;
use App\Haramain\SistemStock\StockKeluarPenjualan;
use App\Models\Penjualan\Penjualan;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenjualanService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;

    protected $penjualanRepository;
    protected $stockKeluarRepository;
    protected $persediaanTransaksiRepo;
    protected $piutangPenjualanRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    public function __construct()
    {
        // penjualan
        $this->akunForPenjualanService();
    }

    public function handleGetData($id)
    {
        return PenjualanRepository::getDataById($id);
    }

    /**
     * alur penjualan :
     * simpan penjualan dan mengembalikan nilai penjualan
     * simpan stock keluar dan mengembalikan nilai stock keluar
     * simpan transaksi persediaan barang keluar dan mengembalikan dari nilai transaksi persediaan
     * @param $data
     * @return object
     */
    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            // store penjualan
            $penjualan = PenjualanRepository::store($data);
            // store stock keluar dan stock inventory
            $stockKeluar = StockKeluarPenjualan::build($penjualan)->store();
            // store persediaan transaksi
            $persediaanTransaksi = PersediaanTransaksiFromPenjualan::build($penjualan)->store();
            // store piutang penjualan
            PiutangPenjualanFromPenjualan::build($penjualan)->store();
            $this->jurnalPenjualanService($penjualan, $persediaanTransaksi);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penjualan
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object) [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            // initiate
            $penjualan = $this->handleGetData($data['penjualan_id']);
            // rollback
            $this->rollback($penjualan);
            // update penjualan
            $penjualan = PenjualanRepository::update($data);
            // update stock keluar
            StockKeluarPenjualan::build($penjualan)->update();
            // update persediaan transaksi
            $persediaanTransaksi = PersediaanTransaksiFromPenjualan::build($penjualan)->update();
            // update piutang penjualan
            PiutangPenjualanFromPenjualan::build($penjualan)->update();
            $this->jurnalPenjualanService($penjualan, $persediaanTransaksi);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penjualan
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object) [
                'status'=>false,
                'keterangan'=>'error'
            ];
        }
    }

    public function handleDestroy($id)
    {
        \DB::beginTransaction();
        try {
            // initiate
            $penjualan = $this->penjualanRepository->getDataById($id);
            // destroy stock keluar
            $this->stockKeluarRepository->destory($penjualan::class, $penjualan->id);
            // destroy persediaan transaksi
            $this->persediaanTransaksiRepo->destroyKeluar($penjualan::class, $penjualan->id);
            // destroy piutang penjualan
            $this->piutangPenjualanRepo->destroy($penjualan::class, $penjualan->id);
            // destroy penjualan
            $this->penjualanRepository->destroy($id);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penjualan
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object) [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    protected function rollback(Penjualan $penjualan): void
    {
        // stock keluar
        StockKeluarPenjualan::build($penjualan)->rollback();
        // persediaan transaksi
        PersediaanTransaksiFromPenjualan::build($penjualan)->rollback();
        // piutang penjualan
        PiutangPenjualanFromPenjualan::build($penjualan)->rollback();
        // penjualan
        $penjualan->penjualanDetail()->delete();
        // rollback jurnal
        $this->rollbackJurnalAndSaldo($penjualan);
    }
}
