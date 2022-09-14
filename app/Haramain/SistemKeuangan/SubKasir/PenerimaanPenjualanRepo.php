<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\SistemKeuangan\SubNeraca\SaldoPiutangPenjualanRepo;
use App\Models\Keuangan\PenerimaanPenjualan;
use App\Models\Keuangan\PenerimaanPenjualanDetail;

class PenerimaanPenjualanRepo
{
    protected $piutangPenjualanRepo;
    protected $saldoPiutangPenjualan;

    public function __construct()
    {
        $this->piutangPenjualanRepo = new PiutangPenjualanRepo();
        $this->saldoPiutangPenjualan = new SaldoPiutangPenjualanRepo();
    }

    protected function kode()
    {
        return null;
    }

    public function getDataById($id)
    {
        return PenerimaanPenjualan::query()->findOrFail($id);
    }

    public function store($data)
    {
        $data = (object) $data;
        $penerimaan = PenerimaanPenjualan::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode(),
                'tgl_penerimaan'=>tanggalan_database_format($data->tgl_penerimaan, 'd-M-Y'),
                'customer_id'=>$data->customerId,
                'akun_kas_id'=>$data->akunKasId,
                'nominal_kas'=>$data->totalBayar,
                'akun_piutang_id'=>$data->akunPiutangId,
                'nominal_piutang'=>$data->totalBayar
            ]);
        $this->storeDetail($data->dataDetail, $penerimaan->id);
        return $penerimaan;
    }

    public function update($data)
    {
        $data = (object) $data;
        $this->getDataById($data->penerimaan_penjualan_id)->update([
                'tgl_penerimaan'=>tanggalan_database_format($data->tgl_penerimaan, 'd-M-Y'),
                'customer_id'=>$data->customerId,
                'akun_kas_id'=>$data->akunKasId,
                'nominal_kas'=>$data->totalBayar,
                'akun_piutang_id'=>$data->akunPiutangId,
                'nominal_piutang'=>$data->totalBayar
        ]);
        $penerimaan = $this->getDataById($data->penerimaan_penjualan_id);
        $this->storeDetail($data->dataDetail, $penerimaan->id);
        return $penerimaan;
    }

    public function rollback($id)
    {
        $penerimaanDetail = PenerimaanPenjualanDetail::query()->where('penerimaan_penjualan_id', $id);
        foreach ($penerimaanDetail->get() as $item) {
            // rollback status
            $this->piutangPenjualanRepo->rollbackStatusBayar($item->piutang_penjualan_id, $item->nominal_dibayar);
        }
        return $penerimaanDetail->delete();
    }

    public function destroy($id)
    {
        $this->rollback($id);
        return $this->getDataById($id)->delete();
    }

    protected function storeDetail($dataDetail, $id)
    {
        foreach ($dataDetail as $detail) {
            $detail = (object) $detail;
            PenerimaanPenjualanDetail::query()->create([
                'penerimaan_penjualan_id'=>$id,
                'piutang_penjualan_id'=>$detail->piutang_penjualan_id,
                'nominal_dibayar'=>$detail->nominal_dibayar,
                'kurang_bayar'=>$detail->kurang_bayar,
            ]);
            // update status
            $this->piutangPenjualanRepo->updateStatusBayar($detail->piutang_penjualan_id, $detail->nominal_dibayar);
        }
    }
}
