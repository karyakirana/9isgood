<?php namespace App\Haramain\SistemStock;

use App\Haramain\ServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockOpnameKoreksiService implements ServiceInterface
{
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
        \DB::beginTransaction();
        try {
            $store = $this->stockOpnameKoreksiRepository->store($data);
            //dd($store);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Data berhasil Disimpan'
            ];
        } catch (ModelNotFoundException|\Exception $e){
            \DB::rollBack();
            return (object)[
                'status'=>'false',
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            $this->stockOpnameKoreksiRepository->rollback($data['stockOpnameKoreksiId']);
            $this->stockOpnameKoreksiRepository->update($data);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Data berhasil Disimpan'
            ];
        } catch (ModelNotFoundException|\Exception $e){
            \DB::rollBack();
            return (object)[
                'status'=>'false',
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleDestroy($id)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
            $this->stockOpnameKoreksiRepository->delete($id);
            return (object)[
                'status'=>true
            ];
        } catch (ModelNotFoundException|\Exception $e){
            \DB::rollBack();
            return (object)[
                'status'=>'false',
                'keterangan'=>$e->getMessage()
            ];
        }
    }
}
