<?php namespace App\Haramain\Repository\Penjualan;

use App\Models\Keuangan\PiutangPenjualanLama;
use App\Models\Keuangan\PiutangPenjualanLamaDetail;

class PenjualanLamaRepository
{
    public function getData($penjualanLamaId)
    {
        return PiutangPenjualanLama::query()->find($penjualanLamaId);
    }

    public function store($data)
    {
        $data = (object) $data;
        $piutangPenjualanLama = PiutangPenjualanLama::query()
            ->create([
                'tahun_nota'=>$data->tahunNota,
                'customer_id'=>$data->customerId,
                'user_Id'=>$data->userId,
                'total_piutang'=>$data->totalPiutang,
                'keterangan'=>$data->keterangan,
            ]);
        $this->storeDetail($data->dataDetail, $piutangPenjualanLama->id);
        return $piutangPenjualanLama;
    }

    protected function storeDetail($dataDetail, $piutangPenjualanLamaId)
    {
        foreach ($dataDetail as $item) {
            $item = (object) $item;
            PiutangPenjualanLamaDetail::query()
                ->create([
                    'piutang_penjualan_lama_id'=>$piutangPenjualanLamaId,
                    'penjualan_id'=>$item->penjualan_id,
                    'total_bayar'=>$item->total_bayar,
                ]);
        }
    }

    public function update($data)
    {
        $data = (object) $data;
        $piutangPenjualanLama = PiutangPenjualanLama::query()->find($data->piutangPenjualanlamaId);
        $piutangPenjualanLama->update([
                'tahun_nota'=>$data->tahunNota,
                'customer_id'=>$data->customerId,
                'user_Id'=>$data->userId,
                'total_piutang'=>$data->totalPiutang,
                'keterangan'=>$data->keterangan,
        ]);
        $this->storeDetail($data->dataDetail, $data->piutangPenjualanLamaId);
        return $piutangPenjualanLama;
    }

    public function rollback($piutangPenjualanLamaId)
    {
        return PiutangPenjualanLamaDetail::query()->where('piutang_penjualan_lama_id', $piutangPenjualanLamaId)->delete();
    }

    public function destroy($piutangPenjualanLamaId)
    {
        $this->rollback($piutangPenjualanLamaId);
        return PiutangPenjualanLama::destroy($piutangPenjualanLamaId);
    }
}
