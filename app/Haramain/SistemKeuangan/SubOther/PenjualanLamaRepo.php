<?php namespace App\Haramain\SistemKeuangan\SubOther;

use App\Haramain\SistemKeuangan\SubKasir\PiutangPenjualanRepo;
use App\Models\Keuangan\PiutangPenjualanLama;
use App\Models\Keuangan\PiutangPenjualanLamaDetail;
use App\Models\Penjualan\Penjualan;

class PenjualanLamaRepo
{
    protected $piutangPenjualanRepo;

    public function __construct()
    {
        $this->piutangPenjualanRepo = new PiutangPenjualanRepo();
    }

    public function getById($piutangPenjualanLamaId)
    {
        return PiutangPenjualanLama::query()->find($piutangPenjualanLamaId);
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
            ]);
        $this->storeDetail($data, $piutangPenjualanLama->id);
        return $piutangPenjualanLama;
    }

    public function update($data)
    {
        $data = (object) $data;
        $piutangPenjualanLama = $this->getById($data->piutangPenjualanLamaId);
        $piutangPenjualanLama->update([
                'tahun_nota'=>$data->tahunNota,
                'customer_id'=>$data->customerId,
                'user_Id'=>$data->userId,
                'total_piutang'=>$data->totalPiutang,
        ]);
        $piutangPenjualanLama = $this->getById($data->piutangPenjualanLamaId); // refresh
        $this->storeDetail($data, $piutangPenjualanLama);
        return $piutangPenjualanLama;
    }

    public function rollback($piutangPenjualanLamaId)
    {
        $piutangPenjualanLamaDetail = PiutangPenjualanLamaDetail::query()->where('piutang_penjualan_lama_id');
        if ($piutangPenjualanLamaId->count() == 0){
            throw new \Exception('Data Piutang Penjualan Detail tidak ada');
        }
        foreach ($piutangPenjualanLamaId->get() as $item) {
            // piutang penjualan destroy
            $this->piutangPenjualanRepo->destroy(Penjualan::class, $item->penjualan_id);
            // penjualan destroy
            Penjualan::destroy($item->penjualan_id);
        }
        return $piutangPenjualanLamaDetail->delete();
    }

    public function destroy($piutangPenjualanLamaId)
    {
        $this->rollback($piutangPenjualanLamaId);
        return PiutangPenjualanLama::destroy($piutangPenjualanLamaId);
    }

    protected function storeDetail($data, $piutangPenjualanLamaId)
    {
        foreach ($data->dataDetail as $item) {
            $item = (object) $item;
            // store penjualan
            $penjualan = Penjualan::query()
                ->create([
                    'kode'=>$item->kode,
                    'active_cash'=>'old',
                    'customer_id'=>$data->customerId,
                    'gudang_id'=>$item->gudangId,
                    'user_id'=>$data->userId,
                    'tgl_nota'=>tanggalan_database_format($data->tglNota, 'd-M-Y'),
                    'tgl_tempo'=>null,
                    'jenis_bayar'=>$item->jenisBayar,
                    'status_bayar'=>'set_piutang',
                    'total_barang'=>null,
                    'ppn'=>null,
                    'biaya_lain'=>null,
                    'total_bayar'=>$item->totalBayar,
                    'keterangan'=>$item->keterangan,
                    'print'=>1,
                ]);
            // store piutang penjualan
            $this->piutangPenjualanRepo->store(
                [
                    'customerId'=>$data->customerId,
                    'statusBayar'=>'belum',
                    'totalBayar'=>$item->totalBayar
                ],
                $penjualan::class,
                $penjualan->id
            );
            // store piutang penjualan lama detail
            PiutangPenjualanLamaDetail::query()
                ->create([
                    'piutang_penjualan_lama_id'=>$piutangPenjualanLamaId,
                    'penjualan_id'=>$penjualan->id,
                    'total_bayar'=>$item->totalBayar,
                ]);
        }
    }
}
