<?php namespace App\Haramain\Service\SistemKeuangan\Kasir;

use App\Haramain\Service\SistemPenjualan\SubPenjualan\PenjualanRepo;
use App\Haramain\Service\SistemPenjualan\SubReturPenjualan\ReturPenjualanRepo;
use App\Models\Keuangan\PiutangPenjualan;
use App\Models\Keuangan\SaldoPiutangPenjualan;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanRetur;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PiutangPenjualanRepo
{
    protected PenjualanRepo $penjualanRepo;
    protected ReturPenjualanRepo $returPenjualanRepo;
    /**
     * Piutang Penjualan
     */
    public function storePenjualan($data): Model|Builder
    {
        return PiutangPenjualan::query()
            ->create([
                'saldo_piutang_penjualan_id'=>$data->customer_id,
                'penjualan_type'=>Penjualan::class,
                'penjualan_id'=>$data->id,
                'status_bayar'=>$data->status_bayar,
                'kurang_bayar'=>$data->total_bayar
            ]);
    }

    public function updatePenjualan($penjualanId, $data): bool|int
    {
        $piutangPenjualanRetur = PiutangPenjualan::query()
            ->where('penjualan_type', Penjualan::class)
            ->where('penjualan_id', $penjualanId)
            ->first();
        return $piutangPenjualanRetur->update([
            'saldo_piutang_penjualan_id'=>$data->customer_id,
            'status_bayar'=>$data->status_bayar,
            'kurang_bayar'=>0 - $data->total_bayar
        ]);
    }

    public function updateSaldoPenjualan($customer_id, $total_bayar): Model|Builder|int
    {
        $builder = $this->builderUpdateSaldo($customer_id);
        if ($builder->doesntExist()){
            return $builder->create([
                'customer_id'=>$customer_id,
                'saldo'=>$total_bayar
            ]);
        }
        return $builder->increment('saldo', $total_bayar);
    }

    public function rollbackPenjualan($dataPenjualan): int
    {
        return SaldoPiutangPenjualan::query()
            ->where('customer_id', $dataPenjualan->customer_id)
            ->increment('saldo', $dataPenjualan->total_bayar);
    }

    /**
     * Piutang Penjualan Retur
     */
    public function storeRetur($dataRetur): Model|Builder
    {
        return PiutangPenjualan::query()
            ->create([
                'saldo_piutang_penjualan_id'=>$dataRetur->customer_id,
                'penjualan_type'=>PenjualanRetur::class,
                'penjualan_id'=>$dataRetur->id,
                'status_bayar'=>$dataRetur->status_bayar,
                'kurang_bayar'=>0 - $dataRetur->total_bayar
            ]);
    }

    public function updateRetur($penjualanReturId, $dataPenjualanRetur): bool|int
    {
        $piutangPenjualanRetur = PiutangPenjualan::query()
            ->where('penjualan_type', PenjualanRetur::class)
            ->where('penjualan_id', $penjualanReturId)
            ->first();
        return $piutangPenjualanRetur->update([
            'saldo_piutang_penjualan_id'=>$dataPenjualanRetur->customer_id,
            'status_bayar'=>$dataPenjualanRetur->status_bayar,
            'kurang_bayar'=>0 - $dataPenjualanRetur->total_bayar
        ]);
    }

    public function updateSaldoRetur($customer_id, $total_bayar): Model|Builder|int
    {
        $builder = $this->builderUpdateSaldo($customer_id);
        if ($builder->doesntExist()){
            return $builder->create([
                'customer_id'=>$customer_id,
                'saldo'=>0 - $total_bayar
            ]);
        }
        return $builder->decrement('saldo', $total_bayar);
    }

    public function rollbackPiutangRetur($dataPenjualanRetur): int
    {
        return SaldoPiutangPenjualan::query()
            ->where('customer_id', $dataPenjualanRetur->customer_id)
            ->increment('saldo', $dataPenjualanRetur->total_bayar);
    }

    /**
     * Builder for this
     */
    private function builderUpdateSaldo($customer_id): Builder
    {
        return SaldoPiutangPenjualan::query()
            ->where('customer_id', $customer_id);
    }

    public function updateStatusPenjualan($piutangPenjualanId, $status, $kurangBayar): int
    {
        $piutangPenjualan = $this->builderUpdateStatus($piutangPenjualanId);
        if($piutangPenjualan->penjualan_type == Penjualan::class){
            // update penjualan
            $this->penjualanRepo->updateStatus($piutangPenjualan->penjualan_id, $status);
        } elseif ($piutangPenjualan->penjualan_type == PenjualanRetur::class){
            // update status penjualan_retur
            $this->returPenjualanRepo->updateStatus($piutangPenjualan->penjualan_id, $status);
        }
        return $piutangPenjualan->update([
                'status_bayar'=>$status,
                'kurang_bayar'=>$kurangBayar
            ]);
    }

    public function rollbackStatus($piutangPenjualanId, $oldData)
    {
        //
    }

    private function builderUpdateStatus($piutangPenjualanId): Model|Collection|Builder|array|null
    {
        return PiutangPenjualan::query()
            ->find($piutangPenjualanId);
    }
}
