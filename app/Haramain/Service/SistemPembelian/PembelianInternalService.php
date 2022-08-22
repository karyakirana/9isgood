<?php namespace App\Haramain\Service\SistemPembelian;


use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PembelianInternalService extends PembelianService
{
    public function __construct()
    {
        parent::__construct();
        $this->jenis = 'INTERNAL';
    }

    /**
     * Handle Method
     * Must Public
     */

    public function handleStore($data)
    {
        DB::beginTransaction();
        try {
            $store = $this->store($data);
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$store
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleEdit($id)
    {
        return $this->getDataById($id);
    }

    public function handleUpdate($data)
    {
        DB::beginTransaction();
        try {
            $store = $this->update($data);
            DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$store
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleDelete($id)
    {
        //
    }
}
