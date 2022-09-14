<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Haramain\SistemKeuangan\SubKasir\PiutangPenjualanRepo;
use App\Models\Keuangan\JurnalSetPiutangAwal;

/**
 * class untuk mencatatat piutang awal atas penjualan pada periode sebelumnya
 * penjualan periode lalu akan disimpan pada piutang penjualan berdasarkan customer
 * penjualan atau retur penjualan akan disimpan pada piutang penjualan
 * sedangkan per transaksinya akan disimpan pada jurnal set piutang awal
 * kemungkinan fitur ini digunakan hanya sekali saja
 */
class JurnalSetPiutangAwalRepo
{
    protected $piutangPenjualanRepo;

    public function __construct()
    {
        $this->piutangPenjualanRepo = new PiutangPenjualanRepo();
    }

    protected function kode()
    {
        return null;
    }

    public function getDataById($id)
    {
        return JurnalSetPiutangAwal::query()->findOrFail($id);
    }

    public function store($data)
    {
        $data = (object) $data;
        $jurnal = JurnalSetPiutangAwal::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode(),
                'jenis'=>$data->jenisSetPiutangAwal,
                'tgl_jurnal'=>tanggalan_database_format($data->tglJurnal, 'd-M-Y'),
                'customer_id'=>$data->customerId,
                'user_id'=>$data->userId,
                'total_piutang'=>$data->totalPiutang,
                'keterangan'=>$data->keterangan,
            ]);
        $this->storeDetail($data, $jurnal->id);
        return $jurnal;
    }

    public function update($data)
    {
        $data = (object) $data;
        $this->getDataById($data->jurnalSetPiutangAwalId)->update([
            'jenis'=>$data->jenisSetPiutangAwal,
            'tgl_jurnal'=>tanggalan_database_format($data->tglJurnal, 'd-M-Y'),
            'customer_id'=>$data->customerId,
            'user_id'=>$data->userId,
            'total_piutang'=>$data->totalPiutang,
            'keterangan'=>$data->keterangan,
        ]);
        $jurnal = $this->getDataById($data->jurnalSetPiutangAwalId);
        $this->storeDetail($data, $jurnal->id);
        return $jurnal;
    }

    public function destroy($id)
    {
        $this->rollback($id);
        return JurnalSetPiutangAwal::destroy($id);
    }

    protected function storeDetail($data, $jurnalSetPiutangAwalId)
    {
        foreach ($data->dataDetail as $item) {
            $item = (object) $item;
            $this->piutangPenjualanRepo->store($item, $item->class, $item->classId, $jurnalSetPiutangAwalId);
        }
    }

    public function rollback($id)
    {
        $jurnal = $this->getDataById($id);
        foreach ($jurnal->piutang_penjualan as $item) {
            $this->piutangPenjualanRepo->destroy($item->penjualan_type, $item->penjualan_id);
        }
    }
}
