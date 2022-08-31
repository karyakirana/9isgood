<?php namespace App\Haramain\Service\Generator;

use App\Haramain\Repository\Neraca\SaldoPiutangPenjualanRepo;
use App\Models\Penjualan\PenjualanRetur;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Penjualan\Penjualan;

/**
 * Mengenerate piutang penjualan
 * jika piutang penjualan ada, maka update
 * jika tidak ada, maka akan dibuat piutang penjualan baru
 * update piutang saldo
 */
class GenSaldoPiutangPenjualanService
{
    protected $saldoPiutangPenjualanRepo;

    public function __construct()
    {
        $this->saldoPiutangPenjualanRepo = new SaldoPiutangPenjualanRepo();
    }

    public function handleGeneratePenjualan()
    {
        \DB::beginTransaction();
        try {
            // get data penjualan
            $penjualanAll = Penjualan::query()->where('active_cash', session('ClosedCash'))->get();
            // each data penjualan
            foreach ($penjualanAll as $penjualan){
                $piutangPenjualan = $penjualan->piutangPenjualan()->first();
                if ($piutangPenjualan){
                    // rollback saldo piutang penjualan repo
                    $this->saldoPiutangPenjualanRepo->decrement($penjualan->customer_id, $penjualan->total_bayar);
                    // update
                    $piutangPenjualan->update([
                        'saldo_piutang_penjualan_id'=>$penjualan->customer_id,
                        'jurnal_set_piutang_awal_id'=>null,
                        'status_bayar'=>'belum', // enum ['lunas', 'belum', 'kurang']
                        'kurang_bayar'=>$penjualan->total_bayar,
                    ]);
                } else {
                    // create
                    $piutangPenjualan = $penjualan->piutangPenjualan()->create([
                        'saldo_piutang_penjualan_id'=>$penjualan->customer_id,
                        'jurnal_set_piutang_awal_id'=>null,
                        'status_bayar'=>'belum', // enum ['lunas', 'belum', 'kurang']
                        'kurang_bayar'=>$penjualan->total_bayar,
                    ]);
                }
                // update saldo piutang penjualan
                $this->saldoPiutangPenjualanRepo->increment($penjualan->customer_id, $penjualan->total_bayar);
            }
            \DB::commit();
            return [
                'status'=>true,
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleGeneratePenjualanRetur()
    {
        \DB::beginTransaction();
        try {
            // get data penjualan retur
            $penjualanReturAll = PenjualanRetur::query()->where('active_cash', session('ClosedCash'))->get();
            // each data retur
            foreach ($penjualanReturAll as $penjualanRetur) {
                $piutangPenjualan = $penjualanRetur->piutangPenjualan()->first();
                if ($piutangPenjualan){
                    // rollback saldo piutang penjualan repo
                    $this->saldoPiutangPenjualanRepo->increment($penjualanRetur->customer_id, $piutangPenjualan->total_bayar);
                    // update
                    $piutangPenjualan->update([
                        'saldo_piutang_penjualan_id'=>$penjualanRetur->customer_id,
                        'jurnal_set_piutang_awal_id'=>null,
                        'status_bayar'=>'belum', // enum ['lunas', 'belum', 'kurang']
                        'kurang_bayar'=>0 - $penjualanRetur->total_bayar,
                    ]);
                } else {
                    $piutangPenjualan = $penjualanRetur->piutangPenjualan()->create([
                        'saldo_piutang_penjualan_id'=>$penjualanRetur->customer_id,
                        'jurnal_set_piutang_awal_id'=>null,
                        'status_bayar'=>'belum', // enum ['lunas', 'belum', 'kurang']
                        'kurang_bayar'=>0 - $penjualanRetur->total_bayar,
                    ]);
                }
                // update saldo piutang penjualan
                $this->saldoPiutangPenjualanRepo->decrement($penjualanRetur->customer_id, $penjualanRetur->total_bayar);
            }
            \DB::commit();
            return [
                'status'=>true,
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleGeneratePenjualanLama()
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
            return [
                'status'=>true,
            ];
        } catch(ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }
}
