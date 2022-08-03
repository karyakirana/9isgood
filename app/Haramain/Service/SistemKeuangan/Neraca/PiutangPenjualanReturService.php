<?php namespace App\Haramain\Service\SistemKeuangan\Neraca;

use App\Haramain\Service\SistemKeuangan\Kasir\PiutangPenjualanRepo;
use App\Models\Keuangan\JurnalSetPiutangAwal;
use App\Models\Penjualan\PenjualanRetur;
use JetBrains\PhpStorm\Pure;

class PiutangPenjualanReturService
{
    protected SaldoPiutangPenjualanRepo $saldoPiutangPenjualanRepo;
    protected NeracaSaldoRepository $neracaSaldoRepository;
    public array $handleValidation = [
        'piutangReturId'=>'nullable|int',
        'customer_id'=>'required|int',
        'tgl_jurnal'=>'required',
        'modal_piutang_awal'=>'required',
        'piutang_usaha'=>'required',
        'ppn_penjualan'=>'nullable',
        'biaya_penjualan'=>'nullable',
        'data_detail'=>'required|array',
        'total_bayar'=>'required'
    ];

    public function __construct()
    {
        //
    }

    public function handleStore($data): object
    {
        \DB::beginTransaction();
        try {
            // store piutang awal
            $piutangReturAwal = JurnalSetPiutangAwal::query()
                ->create([
                    'active_cash'=>session('ClosedCash'),
                    'kode'=>$this->kode(),
                    'tgl_jurnal'=>$data->tgl_jurnal,
                    'customer_id'=>$data->customer_id,
                    'user_id'=>\Auth::id(),
                    'total_piutang'=>$data->total_bayar,
                    'keterangan'=>$data->keterangan,
                ]);
            $piutangPenjualan = $piutangReturAwal->piutang_penjualan();
            foreach ($piutangReturAwal as $item) {
                // store piutang penjualan
                $piutangPenjualan->create([
                    'penjualan_type'=>PenjualanRetur::class,
                    'penjualan_id'=>$item['retur_id'],
                    'status_bayar'=>'belum', // enum ['lunas', 'belum', 'kurang']
                    'kurang_bayar'=>$item['total_bayar'],
                ]);
                // update retur status
                PenjualanRetur::query()->find($item['retur_id'])->update(['status_bayar'=>'set_piutang']);
            }
            // update piutang saldo penjualan
            $this->saldoPiutangPenjualanRepo->store($data->customer_id, 'retur', $data->total_bayar);
            // store jurnal transaksi
            $jurnalTransaksi = $piutangReturAwal->jurnal_transaksi();
            // store jurnal transaksi debet
            $this->storeJurnalTransaksi($jurnalTransaksi, $data);
            \DB::commit();
            return (object)['status'=>true, 'keterangan'=>$piutangReturAwal];
        } catch (\Exception $e){
            \DB::rollBack();
            return (object)['status'=>true, 'keterangan'=>$e];
        }
    }

    public function handleEdit($id)
    {
        //
    }

    public function handleUpdate($data): object
    {
        /**
         * initiate
         */
        $piutangReturAwal = JurnalSetPiutangAwal::query()->find($data->piutangReturId);
        $oldJurnalTransaksi = $piutangReturAwal->jurnal_transaksi;
        $oldPiutangPenjualan = $piutangReturAwal->piutang_penjualan;

        $jurnalTransaksi = $piutangReturAwal->jurnal_transaksi();
        $piutangPenjualan = $piutangReturAwal->piutang_penjualan();

        \DB::beginTransaction();
        try {
            /**
             * rollback
             */
            // rollback neraca saldo
            foreach ($oldJurnalTransaksi as $item) {
                if ($item->nominal_debet){
                    $this->neracaSaldoRepository->rollbackDebet($item->akun_id, $item->nominal_debet);
                } else{
                    $this->neracaSaldoRepository->rollbackDebet($item->akun_id, $item->nominal_kredit);
                }
            }
            // rollback saldo piutang penjualan
            $this->saldoPiutangPenjualanRepo->rollback($piutangReturAwal->customer_id, 'retur', $piutangReturAwal->kurang_bayar);
            // delete jurnal transaksi
            $jurnalTransaksi->delete();
            // rollback status retur penjualan
            foreach ($oldPiutangPenjualan as $item) {
                PenjualanRetur::query()->find($item->penjualan_id)->update(['status_bayar'=>'belum']);
            }
            // rollback piutang penjualan
            $piutangPenjualan->delete();

            /**
             * update
             */
            // update piutang awal
            $piutangReturAwal->update([
                'tgl_jurnal'=>$data->tgl_jurnal,
                'customer_id'=>$data->customer_id,
                'user_id'=>\Auth::id(),
                'total_piutang'=>$data->total_bayar,
                'keterangan'=>$data->keterangan,
            ]);
            // update saldo piutang penjualan
            $this->saldoPiutangPenjualanRepo->store($data->customer_id, 'retur', $data->total_bayar);

            /**
             * store
             */
            // store piutang penjualan
            foreach ($piutangReturAwal as $item) {
                // store piutang penjualan
                $piutangPenjualan->create([
                    'penjualan_type'=>PenjualanRetur::class,
                    'penjualan_id'=>$item['retur_id'],
                    'status_bayar'=>'belum', // enum ['lunas', 'belum', 'kurang']
                    'kurang_bayar'=>$item['total_bayar'],
                ]);
                // update retur status
                PenjualanRetur::query()->find($item->penjualan_id)->update(['status_bayar'=>'set_piutang']);
            }
            // store jurnal transaksi
            $this->storeJurnalTransaksi($jurnalTransaksi, $data);
            \DB::commit();
            return (object)['status'=>true, 'keterangan'=>$piutangReturAwal];
        } catch (\Exception $e){
            \DB::rollBack();
            return (object)['status'=>true, 'keterangan'=>$e];
        }
    }

    public function handleDestroy($id)
    {
        // initiate
        $piutangReturAwal = JurnalSetPiutangAwal::query()->find($id);
        $oldJurnalTransaksi = $piutangReturAwal->jurnal_transaksi;
        $oldPiutangPenjualan = $piutangReturAwal->piutang_penjualan;

        $jurnalTransaksi = $piutangReturAwal->jurnal_transaksi();
        $piutangPenjualan = $piutangReturAwal->piutang_penjualan();

        /**
         * rollback
         */
        // rollback neraca saldo
        foreach ($oldJurnalTransaksi as $item) {
            if ($item->nominal_debet){
                $this->neracaSaldoRepository->rollbackDebet($item->akun_id, $item->nominal_debet);
            } else{
                $this->neracaSaldoRepository->rollbackDebet($item->akun_id, $item->nominal_kredit);
            }
        }
        // rollback saldo piutang penjualan
        $this->saldoPiutangPenjualanRepo->rollback($piutangReturAwal->customer_id, 'retur', $piutangReturAwal->kurang_bayar);
        // delete jurnal transaksi
        $jurnalTransaksi->delete();
        // rollback status retur penjualan
        foreach ($oldPiutangPenjualan as $item) {
            PenjualanRetur::query()->find($item->penjualan_id)->update();
        }
        // rollback piutang penjualan
        return $piutangPenjualan->delete();
    }

    protected function kode(): string
    {
        $query = JurnalSetPiutangAwal::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()){
            return '0001/PR/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PR/".date('Y');
    }

    /**
     * @param $jurnalTransaksi
     * @param $data
     */
    protected function storeJurnalTransaksi($jurnalTransaksi, $data): void
    {
        $jurnalTransaksi->create([
            'active_cash' => session('ClosedCash'),
            'akun_id' => $data->modal_piutang_awal,
            'nominal_debet' => $data->total_bayar,
            'nominal_kredit' => null,
            'keterangan' => $data->keterangan
        ]);
        // store jurnal transaksi kredit
        $jurnalTransaksi->create([
            'active_cash' => session('ClosedCash'),
            'akun_id' => $data->piutang_usaha,
            'nominal_debet' => null,
            'nominal_kredit' => $data->total_bayar,
            'keterangan' => $data->keterangan
        ]);
        // update neraca saldo debet
        $this->neracaSaldoRepository->updateDebet($data->modal_piutang_awal, $data->total_bayar);
        // update neraca saldo kredit
        $this->neracaSaldoRepository->updateKredit($data->piutang_usaha, $data->total_bayar);
    }
}
