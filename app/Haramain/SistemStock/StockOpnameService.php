<?php namespace App\Haramain\SistemStock;

use App\Haramain\ServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpParser\Node\Expr\Cast\Object_;

class StockOpnameService implements ServiceInterface
{
    protected $stockOpnameRepo;

    public function __construct()
    {
        $this->stockOpnameRepo = new StockOpnameRepository();
    }

    public function handleGetData($id)
    {
        return $this->stockOpnameRepo->getDataById($id);
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Stock Opname Berhasil Disimpan'
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>true,
                'keterangan'=>$e
            ];
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Stock Opname Berhasil Disimpan'
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>true,
                'keterangan'=>$e
            ];
        }
    }

    public function handleDestroy($id)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Stock Opname Berhasil Disimpan'
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>true,
                'keterangan'=>$e
            ];
        }
    }
}
