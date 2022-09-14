<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\PengeluaranPembelian;
use App\Models\Keuangan\PengeluaranPembelianDetail;

class PengeluaranPembelianRepo
{
    protected $hutangPembelianRepo;

    public function __construct()
    {
        $this->hutangPembelianRepo = new HutangPembelianRepo();
    }

    protected function kode()
    {
        return null;
    }

    public function getDataById($id)
    {
        return PengeluaranPembelian::query()->findOrFail($id);
    }

    public function store($data)
    {
        $data = (object) $data;
        $pengeluaranPembelian = PengeluaranPembelian::query()->create([
            'active_cash'=>session('ClosedCash'),
            'kode'=>$this->kode(),
            'jenis'=>$data->jenis,
            'supplier_id'=>$data->supplierId,
            'akun_kas_id'=>$data->akunKasId,
            'user_id'=>$data->userId,
            'total_pengeluaran'=>$data->totalPengeluaran,
            'keterangan'=>$data->keterangan
        ]);
        $this->storeDetail($data->dataDetail, $pengeluaranPembelian->id);
        return $pengeluaranPembelian;
    }

    public function update($data)
    {
        $data = (object) $data;
        $pengeluaranPembelian = $this->getDataById($data['pengeluaranPembelianId']);
        $pengeluaranPembelian->update([
            'jenis'=>$data->jenis,
            'supplier_id'=>$data->supplierId,
            'akun_kas_id'=>$data->akunKasId,
            'user_id'=>$data->userId,
            'total_pengeluaran'=>$data->totalPengeluaran,
            'keterangan'=>$data->keterangan
        ]);
        $pengeluaranPembelian = $this->getDataById($data['pengeluaranPembelianId']); // refresh query
        $this->store($data->dataDetail, $pengeluaranPembelian->id);
        return $pengeluaranPembelian;
    }

    public function destroy($id)
    {
        $this->rollback($id);
        return $this->getDataById($id)->delete();
    }

    protected function storeDetail($dataDetail, $pengeluaranId)
    {
        foreach ($dataDetail as $detail){
            $detail = (object) $detail;
            PengeluaranPembelianDetail::query()
                ->create([
                    'pengeluaran_pembeian_id'=>$pengeluaranId,
                    'hutang_pembelian_id'=>$detail->hutang_pembelian_id,
                    'kurang_bayar'=>$detail->totalBayar
                ]);
            // update status
            $this->hutangPembelianRepo->updateStatusBayar($detail->hutang_pembelian_id, $detail->nominal_dibayar);
        }
    }

    public function rollback($pengeluaranId)
    {
        $pengeluaranDetail = PengeluaranPembelianDetail::query()->where('pengeluaran_pembelian_id', $pengeluaranId);
        foreach ($pengeluaranDetail->get() as $item){
            // rollback status
            $this->hutangPembelianRepo->rollbackStatusBayar($item->hutang_pembelian_id, $item->nominal_dibayar);
        }
        return $pengeluaranDetail->delete();
    }
}
