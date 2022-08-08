<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace App\Haramain\Service\SistemKeuangan\Jurnal;

use App\Haramain\Service\SistemKeuangan\Neraca\NeracaSaldoRepository;
use App\Haramain\Service\SistemKeuangan\Neraca\SaldoPiutangPenjualanRepo;
use App\Models\Keuangan\JurnalSetPiutangAwal;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanRetur;
use Auth;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PiutangPenjualanAwalService
{
    public array $handleValidation = [];

    protected $saldoPiutangPenjualanRepo;
    protected $neracaSaldoRepository;
    protected $closedCash;

    public function __construct()
    {
        $this->saldoPiutangPenjualanRepo = new SaldoPiutangPenjualanRepo();
        $this->neracaSaldoRepository = new NeracaSaldoRepository();
        $this->closedCash = session('ClosedCash');
    }

    public function handleStorePenjualan($data): object
    {
        DB::beginTransaction();
        try {
            // insert table jurnal_set_piutang_awal
            // return object after insert
            $piutangSetAwal = JurnalSetPiutangAwal::query()->create($this->setPenjualanData($data));
            // store piutang penjualan
            $this->storePiutangPenjualan($piutangSetAwal, $data);
            DB::commit();
            return (object)['status'=>true, 'keterangan'=>$piutangSetAwal];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)['status'=>true, 'keterangan'=>$e];
        }
    }

    public function handleStoreRetur($data): object
    {
        DB::beginTransaction();
        try {
            // insert table jurnal_set_piutang_awal
            // return object after insert
            $piutangSetAwal = JurnalSetPiutangAwal::query()->create($this->setReturData($data));
            // store data
            $this->storePiutangRetur($piutangSetAwal, $data);
            DB::commit();
            return (object)['status'=>true, 'keterangan'=>$piutangSetAwal];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)['status'=>true, 'keterangan'=>$e];
        }
    }

    public function handleEdit($id)
    {
        return JurnalSetPiutangAwal::query()->find($id);
    }

    public function handleUpdatePenjualan($data): object
    {
        DB::beginTransaction();
        try {
            $piutangSetAwal = JurnalSetPiutangAwal::query()->find($data['piutangSetAwalId']);
            // rollback transaction
            $this->rollbackTransaction($piutangSetAwal, 'penjualan');
            // update piutang awal
            $piutangSetAwal->update($this->setPenjualanUpdateData($data));
            // store
            $this->storePiutangPenjualan($piutangSetAwal, $data);
            DB::commit();
            return (object)['status'=>true, 'keterangan'=>$piutangSetAwal];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)['status'=>true, 'keterangan'=>$e];
        }
    }

    public function handleUpdateRetur($data): object
    {
        DB::beginTransaction();
        try {
            $piutangSetAwal = JurnalSetPiutangAwal::query()->find(['piutangSetAwalId']);
            // rollback transaction
            $this->rollbackTransaction($piutangSetAwal, 'retur');
            // update piutang awal
            $piutangSetAwal->update($this->setReturUpdateData($data));
            // store
            $this->storePiutangRetur($piutangSetAwal, $data);
            DB::commit();
            return (object)['status'=>true, 'keterangan'=>$piutangSetAwal];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return (object)['status'=>true, 'keterangan'=>$e];
        }
    }

    public function handleDelete($id)
    {
        //
    }

    protected function getKode($jenis): string
    {
        $query = JurnalSetPiutangAwal::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        if ($jenis == 'penjualan'){
            $query->where('jenis', 'penjualan');
        } else {
            $query->where('jenis', 'retur');
        }

        $kode = ($jenis == 'penjualan') ? 'PP' : 'PR';

        // check last num
        if ($query->doesntExist()){
            return '0001/'.$kode.'/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num).'/'.$kode.'/'.date('Y');
    }

    protected function setPenjualanData($data)
    {
        $data = (is_array($data)) ? (object) $data : $data;
        return [
            'active_cash'=>$this->closedCash,
            'kode'=>$this->getKode('penjualan'),
            'jenis'=>'penjualan',
            'tgl_jurnal'=>tanggalan_database_format($data->tgl_jurnal, 'd-M-Y'),
            'customer_id'=>$data->customer_id,
            'user_id'=> Auth::id(),
            'total_piutang'=>$data->total_piutang,
            'keterangan'=>$data->keterangan,
            'data_detail'=>$data->data_detail,
        ];
    }

    protected function setPenjualanUpdateData($data)
    {
        $data = (is_array($data)) ? (object) $data : $data;
        return [
            'tgl_jurnal'=>tanggalan_database_format($data->tgl_jurnal, 'd-M-Y'),
            'customer_id'=>$data->customer_id,
            'user_id'=> Auth::id(),
            'total_piutang'=>$data->total_piutang,
            'keterangan'=>$data->keterangan,
            'data_detail'=>$data->data_detail,
        ];
    }

    protected function setReturData($data)
    {
        $data = (is_array($data)) ? (object) $data : $data;
        return [
            'active_cash'=>$this->closedCash,
            'kode'=>$this->getKode('retur'),
            'jenis'=>'penjualan',
            'tgl_jurnal'=>tanggalan_database_format($data->tgl_jurnal, 'd-M-Y'),
            'customer_id'=>$data->customer_id,
            'user_id'=> Auth::id(),
            'total_piutang'=> 0 - (int)$data->total_piutang,
            'keterangan'=>$data->keterangan,
            'data_detail'=>$data->data_detail,
        ];
    }

    protected function setReturUpdateData($data)
    {
        $data = (is_array($data)) ? (object) $data : $data;
        return[
            'tgl_jurnal'=>tanggalan_database_format($data->tgl_jurnal, 'd-M-Y'),
            'customer_id'=>$data->customer_id,
            'user_id'=> Auth::id(),
            'total_piutang'=>$data->total_piutang,
            'keterangan'=>$data->keterangan,
            'data_detail'=>$data->data_detail,
        ];
    }

    protected function setPenjualanDetailData($data, $dataDetail): array
    {
        $data = (is_array($data)) ? (object) $data : $data;
        return [
            'saldo_piutang_penjualan_id'=>$data->customer_id,
            'penjualan_type'=>Penjualan::class,
            'penjualan_id'=>$dataDetail['item_id'],
            'status_bayar'=>'belum', // enum ['lunas', 'belum', 'kurang']
            'kurang_bayar'=>$dataDetail['total_bayar'],
        ];
    }

    protected function setReturDetailData($data, $dataDetail): array
    {
        $data = (is_array($data)) ? (object) $data : $data;
        return [
            'saldo_piutang_penjualan_id'=>$data->customer_id,
            'penjualan_type'=>PenjualanRetur::class,
            'penjualan_id'=>$dataDetail['item_id'],
            'status_bayar'=>'belum', // enum ['lunas', 'belum', 'kurang']
            'kurang_bayar'=>0 - (int) $dataDetail['total_bayar'],
        ];
    }

    protected function storePiutangPenjualan($piutangSetAwal, $data)
    {
        $dataObject = (object) $data;
        // initiate class one to many class piutang_penjualan
        $piutangPenjualan = $piutangSetAwal->piutang_penjualan();
        foreach ($dataObject->data_detail as $item) {
            // insert table piutang_penjualan
            $piutangPenjualan->create($this->setPenjualanDetailData($data, $item));
            // update table penjualan status
            Penjualan::query()->find($item['item_id'])->update(['status_bayar'=>'set_piutang']);
        }
        // update table saldo_piutang_penjualan
        $this->saldoPiutangPenjualanRepo->store($dataObject->customer_id, 'penjualan', $dataObject->total_piutang);
        // insert table jurnal_transaksi
        $this->insertJurnalTransaksiPenjualan($piutangSetAwal->jurnal_transaksi(), $dataObject);
        // update table neraca_saldo
        $this->updateNeracaSaldoPiutangPenjualan($dataObject);
    }

    protected function storePiutangRetur($piutangSetAwal, $data)
    {
        $dataObject = (object) $data;
        // initiate class one to many class piutang_penjualan
        $piutangPenjualan = $piutangSetAwal->piutang_penjualan();
        // store for each data_detail
        foreach ($dataObject->data_detail as $item) {
            // insert table piutang_penjualan
            $piutangPenjualan->create($this->setReturDetailData($data, $item));
            // update table penjualan status
            PenjualanRetur::query()->find($item['item_id'])->update(['status_bayar'=>'set_piutang']);
        }
        // update table saldo_piutang_penjualan
        $this->saldoPiutangPenjualanRepo->store($dataObject->customer_id, 'retur', $dataObject->total_piutang);
        // insert table jurnal_transaksi
        $this->insertJurnalTransaksiRetur($piutangSetAwal->jurnal_transaksi(), $dataObject);
        // update table neraca_saldo
        $this->updateNeracaSaldoPiutangRetur($dataObject);
    }

    protected function insertJurnalTransaksiPenjualan($jurnalTransaksi, $data): void
    {
        // insert for debet
        $jurnalTransaksi->create([
            'active_cash' => session('ClosedCash'),
            'akun_id' => $data->piutang_usaha,
            'nominal_debet' => $data->total_piutang,
            'nominal_kredit' => null,
            'keterangan' => $data->keterangan
        ]);
        // insert for kredit
        $jurnalTransaksi->create([
            'active_cash' => session('ClosedCash'),
            'akun_id' => $data->modal_piutang_awal,
            'nominal_debet' => null,
            'nominal_kredit' => $data->total_piutang,
            'keterangan' => $data->keterangan
        ]);
    }

    protected function insertJurnalTransaksiRetur($jurnalTransaksi, $data): void
    {
        // insert for debet
        $jurnalTransaksi->create([
            'active_cash' => session('ClosedCash'),
            'akun_id' => $data->modal_piutang_awal,
            'nominal_debet' => $data->total_piutang,
            'nominal_kredit' => null,
            'keterangan' => $data->keterangan
        ]);
        // insert for kredit
        $jurnalTransaksi->create([
            'active_cash' => session('ClosedCash'),
            'akun_id' => $data->piutang_usaha,
            'nominal_debet' => null,
            'nominal_kredit' => $data->total_piutang,
            'keterangan' => $data->keterangan
        ]);
    }

    protected function updateNeracaSaldoPiutangPenjualan($data)
    {
        // update table neraca_saldo debet
        $this->neracaSaldoRepository->updateDebet($data->piutang_usaha, $data->total_piutang);
        // update table neraca_saldo kredit
        $this->neracaSaldoRepository->updateDebet($data->modal_piutang_awal, $data->total_piutang);
    }

    protected function updateNeracaSaldoPiutangRetur($data):void
    {
        // update table neraca_saldo debet
        $this->neracaSaldoRepository->updateDebet($data->modal_piutang_awal, $data->total_piutang);
        // update table neraca_saldo kredit
        $this->neracaSaldoRepository->updateDebet($data->piutang_usaha, $data->total_piutang);
    }

    protected function rollbackTransaction($piutangPenjualanAwal, $jenis): void
    {
        // rollback neraca_saldo
        $oldJurnalTransaksi = $piutangPenjualanAwal->jurnal_transaksi;
        foreach ($oldJurnalTransaksi as $item) {
            if ($item->nominal_debet){
                $this->neracaSaldoRepository->rollbackDebet($item->akun_id, $item->nominal_debet);
            } else{
                $this->neracaSaldoRepository->rollbackDebet($item->akun_id, $item->nominal_kredit);
            }
        }
        // delete jurnal_transaksi
        $piutangPenjualanAwal->jurnal_transaksi()->delete();
        // rollback saldo_piutang_penjualan
        $this->saldoPiutangPenjualanRepo->rollback($piutangPenjualanAwal->customer_id, $jenis, $piutangPenjualanAwal->total_piutang);
        // rollback status penjualan or penjualan_retur
        if ($jenis == 'penjualan'){
            foreach ($piutangPenjualanAwal->piutang_penjualan as $item) {
                Penjualan::query()->find($item->penjualan_id)->update(['status_bayar'=>'belum']);
            }
        } else {
            foreach ($piutangPenjualanAwal->piutang_penjualan as $item) {
                PenjualanRetur::query()->find($item->penjualan_id)->update(['status_bayar'=>'belum']);
            }
        }
        // delete piutang_penjualan
        $piutangPenjualanAwal->piutang_penjualan()->delete();
    }
}
