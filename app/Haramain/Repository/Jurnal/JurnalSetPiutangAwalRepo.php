<?php namespace App\Haramain\Repository\Jurnal;

use App\Models\Keuangan\JurnalSetPiutangAwal;
use App\Models\Keuangan\PiutangPenjualan;

class JurnalSetPiutangAwalRepo
{
    protected $jurnalSetPiutangAwal;

    public function __construct()
    {
        $this->jurnalSetPiutangAwal = new JurnalSetPiutangAwal();
    }

    public function kode()
    {
        $query = JurnalSetPiutangAwal::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()){
            return '0001/PP/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PP/".date('Y');
    }

    public function store($data)
    {
        $data = (object)$data;
        $jurnalSetPiutangAwal = JurnalSetPiutangAwal::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode(),
                'jenis'=>$data->jenis,
                'tgl_jurnal'=>$data->tglJurnal,
                'customer_id'=>$data->customerId,
                'user_id'=>$data->userId,
                'total_piutang'=>$data->totalPiutang,
                'keterangan'=>$data->keterangan,
            ]);
        $this->storeDetail($data, $jurnalSetPiutangAwal->id);
        return $jurnalSetPiutangAwal;
    }

    public function storeDetail($data, $jurnalSetPiutangAwalId)
    {
        foreach ($data->dataDetail as $item) {
            $item = (object)$item;
            PiutangPenjualan::query()->create([
                'saldo_piutang_penjualan_id'=>$item->customer_id,
                'jurnal_set_piutang_awal_id'=>$jurnalSetPiutangAwalId,
                'penjualan_type'=>$item->penjualanType,
                'penjualan_id'=>$item->penjualanId,
                'status_bayar'=>'belum', // enum ['lunas', 'belum', 'kurang']
                'kurang_bayar'=>$item->totalBayar,
            ]);
        }
    }

    public function update($data)
    {
        $data = (object)$data;
        $jurnalSetPiutangAwal = JurnalSetPiutangAwal::query()->find($data->jurnalSetPiutangAwalId);
        $jurnalSetPiutangAwal->update([
            'jenis'=>$data->jenis,
            'tgl_jurnal'=>$data->tglJurnal,
            'customer_id'=>$data->customerId,
            'user_id'=>$data->userId,
            'total_piutang'=>$data->totalPiutang,
            'keterangan'=>$data->keterangan,
        ]);
        $this->storeDetail($data, $jurnalSetPiutangAwal->id);
        return $jurnalSetPiutangAwal;
    }

    public function rollback($jurnalSetPiutangAwalId)
    {
        return PiutangPenjualan::query()->where('jurnal_set_piutang_awal_id', $jurnalSetPiutangAwalId)->delete();
    }

    public function destroy($jurnalSetPiutangAwalid)
    {
        $this->rollback($jurnalSetPiutangAwalid);
        return JurnalSetPiutangAwal::destroy($jurnalSetPiutangAwalid);
    }
}
