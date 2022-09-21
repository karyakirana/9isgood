<?php namespace App\Haramain\SistemStock;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubPersediaan\Opname\PersediaanOpnameKoreksiRepository;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockOpnameKoreksiService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;

    protected $stockOpnameKoreksiRepository;

    public function __construct()
    {
        $this->stockOpnameKoreksiRepository = new StockOpnameKoreksiRepository();
    }

    public function handleGetData($id)
    {
        try {
            return (object)[
                'status'=>'false',
                'data'=>$this->stockOpnameKoreksiRepository->getById($id)
            ];
        } catch (ModelNotFoundException $e){
            return (object)[
                'status'=>'false',
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleStore($data)
    {
        DB::beginTransaction();
        try {
            $stockOpnameKoreksi = $this->stockOpnameKoreksiRepository->store($data);
            $persediaanOpnameKoreksi = PersediaanOpnameKoreksiRepository::build($stockOpnameKoreksi)->store();
            //dd($store);
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Data berhasil Disimpan'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>'false',
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleUpdate($data)
    {
        DB::beginTransaction();
        try {
            $this->stockOpnameKoreksiRepository->rollback($data['stockOpnameKoreksiId']);
            $stockOpnameKoreksi = $this->stockOpnameKoreksiRepository->update($data);
            $persediaanOpnameKoreksi = PersediaanOpnameKoreksiRepository::build($stockOpnameKoreksi)->store();
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Data berhasil Disimpan'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>'false',
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleDestroy($id)
    {
        DB::beginTransaction();
        try {
            DB::commit();
            $this->stockOpnameKoreksiRepository->delete($id);
            return (object)[
                'status'=>true,
                'keterangan'=>'Data Berhasil dihapus'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>'false',
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    protected function jurnalStockOpnameKoreksiService($persediaanStockOpnameKoreksi)
    {
        // persediaan debet
        // modal awal kredit
    }
}
