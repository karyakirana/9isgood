<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\SistemKeuangan\SubNeraca\SaldoHutangPembelianRepo;
use App\Models\Keuangan\HutangPembelian;

class HutangPembelianRepo
{
    protected $saldoHutangPembelianRepo;

    public function __construct()
    {
        $this->saldoHutangPembelianRepo = new SaldoHutangPembelianRepo();
    }

    public function getDataById($hutangableType, $hutangableId)
    {
        return HutangPembelian::query()
            ->where('pembelian_type', $hutangableType)
            ->where('pembelian_id', $hutangableId)
            ->first();
    }

    public function getDataAll()
    {
        $query = HutangPembelian::all();
    }

    public function store($data, $hutangableType, $hutangableId)
    {
        $data = (object) $data;
        $hutangPembelian = HutangPembelian::query()
            ->create([
                'saldo_hutang_pembelian_id'=>$data->supplierId,
                'pembelian_type'=>$hutangableType,
                'pembelian_id'=>$hutangableId,
                'status_bayar'=>$data->statusBayar, // lunas, belum, kurang
                'total_bayar'=>$data->totalBayar,
                'kurang_bayar'=>$data->totalBayar,
            ]);
        // update saldo hutang pembelian
        $this->saldoHutangPembelianRepo->saldoIncrement($data->supplierId, $data->totalBayar);
        return $hutangPembelian;
    }

    public function update($data, $hutangableType, $hutangableId)
    {
        $data = (object) $data;
        $this->getDataById($hutangableType, $hutangableId)->update([
            'saldo_hutang_pembelian_id'=>$data->supplierId,
            'pembelian_type'=>$hutangableType,
            'pembelian_id'=>$hutangableId,
            'status_bayar'=>$data->statusBayar, // lunas, belum, kurang
            'total_bayar'=>$data->totalBayar,
            'kurang_bayar'=>$data->totalBayar,
        ]);
        // update saldo hutang pembelian
        $this->saldoHutangPembelianRepo->saldoIncrement($data->supplierId, $data->totalBayar);
        return $this->getDataById($hutangableType, $hutangableId);
    }

    public function rollback($hutangableType, $hutangableId)
    {
        $hutangPembelian = $this->getDataById($hutangableType, $hutangableId);
        $this->saldoHutangPembelianRepo->saldoDecrement($hutangPembelian->saldo_hutang_pembelian_id, $hutangPembelian->kurang_bayar);
        return $hutangPembelian;
    }

    public function destroy($hutangableType, $hutangableId)
    {
        return $this->rollback($hutangableType, $hutangableId)->delete();
    }

    /** Proses from pengeluaran pembelian */

    public function updateStatusBayar($id, $nominalBayar)
    {
        $hutangPembelian = HutangPembelian::query()->findOrFail($id);
        $hasilBayar = $hutangPembelian->kurang_bayar - $nominalBayar;
        $statusBayar = ($hasilBayar === 0) ? 'lunas' : 'kurang';
        // update pembelian or pembelian retur
        $hutangPembelian->hutangablePembelian()->update([
            'status_bayar'=>$statusBayar
        ]);
        $type = class_basename($hutangPembelian->pembelian_type);
        if ($type === 'Pembelian'){
            // update saldo hutang pembelian
            $this->saldoHutangPembelianRepo->saldoIncrement($hutangPembelian->saldo_hutang_pembelian_id, $nominalBayar);
        } else {
            // update saldo hutang pembelian retur
            $this->saldoHutangPembelianRepo->saldoDecrement($hutangPembelian->saldo_hutang_pembelian_id, $nominalBayar);
        }
        // update piutang penjualan
        return $hutangPembelian->update([
            'status_bayar'=>$statusBayar, // enum ['lunas', 'belum', 'kurang']
            'kurang_bayar'=>$hasilBayar,
        ]);
    }

    public function rollbackStatusBayar($id, $nominalBayar)
    {
        $hutangPembelian = HutangPembelian::query()->findOrFail($id);
        $hasilBayar = $hutangPembelian->kurang_bayar + $nominalBayar;
        $hutangablePembelian = $hutangPembelian->hutangablePembelian;
        $totalBayar = $hutangablePembelian->total_bayar;
        $statusBayar = (abs($hasilBayar) === $totalBayar) ? 'belum' : 'kurang';
        // update pembelian or pembelian retur
        $hutangPembelian->hutangablePembelian()->update([
            'status_bayar'=>$statusBayar
        ]);
        $type = class_basename($hutangPembelian->pembelian_type);
        if ($type === 'Pembelian'){
            // update saldo hutang pembelian
            $this->saldoHutangPembelianRepo->saldoDecrement($hutangPembelian->saldo_hutang_pembelian_id, $nominalBayar);
        } else {
            // update saldo hutang pembelian retur
            $this->saldoHutangPembelianRepo->saldoIncrement($hutangPembelian->saldo_hutang_pembelian_id, $nominalBayar);
        }
        // update piutang penjualan
        return $hutangPembelian->update([
            'status_bayar'=>$statusBayar, // enum ['lunas', 'belum', 'kurang']
            'kurang_bayar'=>$hasilBayar,
        ]);
    }
}
