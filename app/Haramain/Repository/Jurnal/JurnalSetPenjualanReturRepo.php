<?php namespace App\Haramain\Repository\Jurnal;

use App\Haramain\Repository\Neraca\NeracaSaldoRepository;
use App\Haramain\Repository\Saldo\SaldoPiutangPenjualanReturRepo;
use App\Models\Keuangan\JurnalSetReturPenjualanAwal;
use App\Models\Penjualan\PenjualanRetur;

class JurnalSetPenjualanReturRepo
{
    public function kode()
    {
        $query = JurnalSetReturPenjualanAwal::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()){
            return '0001/PPR/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PPR/".date('Y');
    }

    public function store($data)
    {
        // initiateW
        $saldoPiutangPenjualanRetur = (new SaldoPiutangPenjualanReturRepo())->find($data->customer_id);
        // store data jurnal set piutang penjualan retur awal
        $jurnalSetPiutangRetur = JurnalSetReturPenjualanAwal::query()
                        ->create([
                            'active_cash'=>session('ClosedCash'),
                            'kode'=>$this->kode(),
                            'tgl_jurnal'=>tanggalan_database_format($data->tgl_jurnal, 'd-M-Y'),
                            'customer_id'=>$data->customer->id,
                            'user_id'=>\Auth::id(),
                            'total_piutang'=>$data->return_sum_total_bayar,
                        ]);

        // simpan data detail
        return $this->storeDetail($data, $jurnalSetPiutangRetur, $saldoPiutangPenjualanRetur);
    }

    public function update($data)
    {
        // initiate
        $jurnalSetPiutangRetur = JurnalSetReturPenjualanAwal::query()->find($data->jurnal_set_piutang_retur_id);
        $saldoPiutangPenjualanRetur = (new SaldoPiutangPenjualanReturRepo())->find($jurnalSetPiutangRetur->customer_id);
        $jurnalTransaksi = $jurnalSetPiutangRetur->jurnal_transaksi();

        // rollback saldo piutang retur
        foreach ($jurnalSetPiutangRetur->penjualan_piutang_retur as $item) {
            $saldoPiutangPenjualanRetur->decrement('saldo'. $item->kurang_bayar);
            $penjualanRetur = PenjualanRetur::query()->firstWhere('id', $item->penjualan_retur_id);
            $penjualanRetur->update(['status_bayar'=>'belum']);
        }

        // rollback neracasaldo
        foreach ($jurnalSetPiutangRetur->jurnal_transaksi as $item) {
            (new NeracaSaldoRepository())->rollback($item);
        }

        // delete detail
        $jurnalSetPiutangRetur->penjualan_piutang_retur()->delete();
        $jurnalTransaksi->delete();

        // reinitiate
        $saldoPiutangPenjualanRetur = (new SaldoPiutangPenjualanReturRepo())->find($data->customer_id);

        // update jurnalsetpiutangretur
        $jurnalSetPiutangRetur->update([
            'tgl_jurnal'=>tanggalan_database_format($data->tgl_jurnal, 'd-M-Y'),
            'customer_id'=>$data->customer->id,
            'user_id'=>\Auth::id(),
            'total_piutang'=>$data->return_sum_total_bayar,
        ]);

        // simpan data detail
        return $this->storeDetail($data, $jurnalSetPiutangRetur, $saldoPiutangPenjualanRetur);
    }

    /**
     * @param $data
     * @param array|null $jurnalSetPiutangRetur
     * @param object|null $saldoPiutangPenjualanRetur
     * @return mixed
     */
    public function storeDetail($data, array|null $jurnalSetPiutangRetur, object|null $saldoPiutangPenjualanRetur): mixed
    {
        foreach ($data->data_detail as $item) {
            // store penjualan_piutang_retur
            $jurnalSetPiutangRetur->penjualan_piutang_retur()->create([
                'saldo_piutang_penjualan_retur_id' => $data->customer_id,
                'status_bayar' => 'belum',
                'kurang_bayar' => $item['retur_total_bayar']
            ]);

            // update status penjualan to set_piutang
            $penjualanRetur = PenjualanRetur::query()->firstWhere('id', $item['penjualan_retur_id']);
            $penjualanRetur->update(['status_bayar' => 'set_piutang']);
            // update saldo piutang penjualan retur
            $saldoPiutangPenjualanRetur->increment('saldo', $item['retur_total_bayar']);
        }

        // store jurnal transaksi
        $jurnalTransaksi = $jurnalSetPiutangRetur->jurnal_transaksi();

        // store debet (modal awal debet) karena mengurangi keuntungan
        $jurnalTransaksi->create([
            'active_cash' => session('ClosedCash'),
            'akun_id' => $data->modal_piutang_awal,
            'nominal_debet' => $data->retur_sum_total_bayar,
            'nominal_kredit' => null,
            'keterangan' => null
        ]);
        // store kredit (hutang retur penjualan)
        $jurnalTransaksi->create([
            'active_cash' => session('ClosedCash'),
            'akun_id' => $data->piutang_usaha,
            'nominal_debet' => null,
            'nominal_kredit' => $data->penjualan_sum_total_bayar,
            'keterangan' => null
        ]);

        // update neraca saldo
        (new NeracaSaldoRepository())->updateOneRow($data->modal_piutang_awal, $data->penjualan_sum_total_bayar, null);
        (new NeracaSaldoRepository())->updateOneRow($data->piutang_usaha, null, $data->penjualan_sum_total_bayar);

        return $jurnalSetPiutangRetur->id;
    }
}
