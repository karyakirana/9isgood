<?php namespace App\Haramain\SistemStock;

use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanMutasiRepo;
use App\Models\Stock\StockMutasi;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockMutasiService
{
    use JurnalTransaksiServiceTrait;

    protected $stockMutasiRepo;
    protected $stockMasukRepo;
    protected $stockKeluarRepo;
    protected $persediaanMutasiRepo;
    protected $persediaanTransaksiRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;



    public function __construct()
    {
        $this->akunStockMutasiService();
    }

    /**
     * ambil data berdasarkan parameter
     * @param $stockMutasiId
     * @return Builder|Model|object|null
     */
    public function handleGetData($stockMutasiId)
    {
        return $this->persediaanMutasiRepo->getDataById($stockMutasiId);
    }

    /**
     * ambil data berdasarkan form dengan urutan proses
     * menyimpan data stock mutasi dengan nilai pengembalian object untuk initiate
     * penyimpanan pada stock
     * mengambil data dari persediaan
     * data dari persediaan diproses pada persediaan transaksi
     * penyimpanan pada jurnal transaksi dan update neraca saldo sesuai dari akun akuntansi
     * @param $data
     * @return object
     */
    public function handleStore($data)
    {
        DB::beginTransaction();
        try {
            // simpan dan initiate stock mutasi
            $stockMutasi = StockMutasiRepository::build($data)->store();
            // stock keluar
            StockKeluarMutasi::build($stockMutasi)->store();
            // stock masuk
            StockMasukMutasi::build($stockMutasi)->store();
            // persediaan mutasi
            $persediaanMutasi = PersediaanMutasiRepo::build($stockMutasi)->store();

            $this->jurnalStockMutasiService($persediaanMutasi);
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'data berhasil disimpan'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    /**
     * @param $data
     * @return object
     */
    public function handleUpdate($data)
    {
        DB::beginTransaction();
        try {
            $stockMutasi = StockMutasi::find($data['mutasiId']);
            // rollback first
            $this->rollback($stockMutasi);
            // update stock mutasi
            $stockMutasi = StockMutasiRepository::build($data)->update();
            // update stock keluar
            StockKeluarMutasi::build($stockMutasi)->update();
            // update stock masuk
            StockMasukMutasi::build($stockMutasi)->update();
            // persediaan mutasi
            $persediaanMutasi = PersediaanMutasiRepo::build($stockMutasi)->update();

            $this->jurnalStockMutasiService($persediaanMutasi);
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'data berhasil disimpan'
            ];
        }catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function destroy($mutasiId)
    {
        DB::beginTransaction();
        try {
            $stockMutasi = StockMutasi::find($mutasiId);
            // rollback
            $this->rollback($stockMutasi);
            // todo delete stock keluar
            $stockMutasi->stockKeluar()->delete();
            // todo delete stock masuk
            $stockMutasi->stockMasuk()->delete();
            // todo delete persediaan transaksi
            $persediaanMutasi = $stockMutasi->persediaanMutasi;
            $persediaanMutasi->persediaan_transaksi->delete();
            // todo delete persediaan mutasi
            $persediaanMutasi->delete();
            // delete persediaan mutasi
            $stockMutasi->delete();
            DB::commit();
            return (object)[
                'status'=>true
            ];
        }catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    protected function rollback(StockMutasi $stockMutasi)
    {
        $persediaanMutasi = $stockMutasi->persediaanMutasi;
        $this->rollbackJurnalAndSaldo($persediaanMutasi);
        // todo rollback persediaan mutasi
        PersediaanMutasiRepo::build($stockMutasi)->rollback();
        // todo rollback stock masuk
        StockMasukMutasi::build($stockMutasi)->rollback();
        // todo rollback stock keluar
        StockKeluarMutasi::build($stockMutasi)->rollback();
        // todo rollback stock mutasi
        $stockMutasi->stockMutasiDetail()->delete();
    }
}
