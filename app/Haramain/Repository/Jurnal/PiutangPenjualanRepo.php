<?php namespace App\Haramain\Repository\Jurnal;

use App\Haramain\Repository\Neraca\NeracaSaldoRepository;
use App\Haramain\Repository\Saldo\SaldoPiutangPenjualanrepo;
use App\Models\Keuangan\JurnalSetPiutangAwal;
use App\Models\Keuangan\SaldoPiutangPenjualan;
use App\Models\Penjualan\Penjualan;

class PiutangPenjualanRepo
{
    protected function kode()
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
        // initiate
        $biayaLain = 0;
        $ppn = 0;
        $saldoPiutangPenjualan = (new SaldoPiutangPenjualanrepo())->find($data->customer_id); // bisa diganti dengan repository
        // store data jurnal set piutang awal
        $jurnalSetPiutang = JurnalSetPiutangAwal::query()->create([
            'active_cash'=>session('ClosedCash'),
            'kode'=>$this->kode(),
            'tgl_jurnal'=>tanggalan_database_format($data->tgl_jurnal, 'd-M-Y'),
            'customer_id'=>$data->customer_id,
            'user_id'=>\Auth::id(),
            'total_piutang'=>$data->penjualan_sum_total_bayar,
        ]);
        // store piutang penjualan
        return $this->storeDetail($data, $jurnalSetPiutang, $saldoPiutangPenjualan, $biayaLain, $ppn);
    }

    public function update($data)
    {
        // initiate
        $biayaLain = 0;
        $ppn = 0;
        $jurnalSetPiutang = JurnalSetPiutangAwal::query()->find($data->setPiutangId);
        $saldoPiutangPenjualan = (new SaldoPiutangPenjualanrepo())->find($jurnalSetPiutang->customer_id);
        $jurnalTransaksi = $jurnalSetPiutang->jurnal_transaksi();

        // rollback
        foreach ($jurnalSetPiutang->piutang_penjualan as $item){
            // rollback saldo piutang
            $saldoPiutangPenjualan->decrement('saldo', $item->kurang_bayar);
            $penjualan = Penjualan::query()->firstWhere('id', $item->penjualan_id);
            $penjualan->update(['status_bayar'=>'belum']); // set to null
        }

        // rollback neracasaldo
        foreach ($jurnalSetPiutang->jurnal_transaksi as $item){
            (new NeracaSaldoRepository())->rollback($item);
        }

        // delete detail
        $jurnalSetPiutang->piutang_penjualan()->delete();
        $jurnalTransaksi->delete();

        // update begin
        // update jurnal set piutang
        $saldoPiutangPenjualan = (new SaldoPiutangPenjualanrepo())->find($data->customer_id);
        $jurnalSetPiutang->update([
            'tgl_jurnal'=>tanggalan_database_format($data->tgl_jurnal, 'd-M-Y'),
            'customer_id'=>$data->customer_id,
            'user_id'=>\Auth::id(),
            'total_piutang'=>$data->penjualan_sum_total_bayar,
        ]);

        // store piutang penjualan
        return $this->storeDetail($data, $jurnalSetPiutang, $saldoPiutangPenjualan, $biayaLain, $ppn);
    }

    /**
     * @param $data
     * @param \Illuminate\Database\Eloquent\Model|array|null $jurnalSetPiutang
     * @param object|null $saldoPiutangPenjualan
     * @param mixed $biayaLain
     * @param mixed $ppn
     * @return mixed
     */
    protected function storeDetail($data, \Illuminate\Database\Eloquent\Model|array|null $jurnalSetPiutang, object|null $saldoPiutangPenjualan, mixed $biayaLain, mixed $ppn): mixed
    {
        foreach ($data->data_detail as $row) {
            $jurnalSetPiutang->piutang_penjualan()->create([
                'saldo_piutang_penjualan_id' => $data->customer_id,
                'penjualan_id' => $row['penjualan_id'],
                'status_bayar' => 'belum', // enum ['lunas', 'belum', 'kurang']
                'kurang_bayar' => $row['penjualan_total_bayar'],
            ]);
            // update status penjualan to set_piutang
            $penjualan = Penjualan::query()->firstWhere('id', $row['penjualan_id']);
            $penjualan->update(['status_bayar' => 'set_piutang']);
            // update saldo piutang penjualan
            $saldoPiutangPenjualan->increment('saldo', $row['penjualan_total_bayar']);
            $biayaLain += $row['penjualan_biaya_lain'];
            $ppn += $row['penjualan_ppn'];
        }
        // store jurnal transaksi
        $jurnalTransaksi = $jurnalSetPiutang->jurnal_transaksi();

        // store debet (piutang)
        $jurnalTransaksi->create([
            'active_cash' => session('ClosedCash'),
            'akun_id' => $data->piutang_usaha,
            'nominal_debet' => $data->penjualan_sum_total_bayar,
            'nominal_kredit' => null,
            'keterangan' => null
        ]);
        // store kredit (pendapatan periode lalu -- prive)
        $jurnalTransaksi->create([
            'active_cash' => session('ClosedCash'),
            'akun_id' => $data->modal_piutang_awal,
            'nominal_debet' => null,
            'nominal_kredit' => $data->penjualan_sum_total_bayar,
            'keterangan' => null
        ]);
        // update neraca saldo awal (kurang)
        (new NeracaSaldoRepository())->updateOneRow($data->piutang_usaha, $data->penjualan_sum_total_bayar, null);
        (new NeracaSaldoRepository())->updateOneRow($data->modal_piutang_awal, null, $data->penjualan_sum_total_bayar);
        return $jurnalSetPiutang->id;
    }
}
