<?php namespace App\Haramain\Service\SistemKeuangan\Neraca;

use App\Models\Keuangan\JurnalSetPiutangAwal;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanRetur;

class PiutangPenjualanAwalRepo
{
    protected function kode($mode = 'penjualan')
    {
        //
    }
    public function store($data)
    {
        if ($data->type == 'penjualan'){
            $penjualan_type = Penjualan::class;
            $total_piutang = (int) $data->total_piutang;
        } else{
            $penjualan_type = PenjualanRetur::class;
            $total_piutang = 0 - (int)$data->total_piutang;
        }

        $piutangPenjualanAwal = JurnalSetPiutangAwal::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode',
                'tgl_jurnal'=>$data->tanggal,
                'customer_id'=>$data->customer_id,
                'user_id'=>\Auth::id(),
                'total_piutang'=>$total_piutang,
                'keterangan'=>$data->keterangan,
            ]);

        $piutangPenjualan = $piutangPenjualanAwal->piutang_penjualan();

        foreach ($data->data_detail as $item) {
            $piutangPenjualan->create([
                'saldo_piutang_penjualan_id'=>$data->customer_id,
                'penjualan_type'=>$penjualan_type,
                'penjualan_id'=>$item->retur_id,
                'status_bayar' =>'belum', // enum ['lunas', 'belum', 'kurang']
                'kurang_bayar'=> ($data->type == 'penjualan') ? $item->kurang_bayar : 0 - $item->kurang_bayar,
            ]);
        }

        // jurnal transaksi
        // neraca saldo
    }

    public function update($data)
    {
        // initiation
        if ($data->type == 'penjualan'){
            $penjualan_type = Penjualan::class;
            $total_piutang = (int) $data->total_piutang;
        } else{
            $penjualan_type = PenjualanRetur::class;
            $total_piutang = 0 - (int)$data->total_piutang;
        }
        $piutangPenjualanAwal = JurnalSetPiutangAwal::query()->find($data->piutangAwalId);
        $piutangPenjualan = $piutangPenjualanAwal->piutang_penjualan();
        // rollback
        $piutangPenjualan->delete();
        // update
        $piutangPenjualanAwal->update([
            'tgl_jurnal'=>$data->tanggal,
            'customer_id'=>$data->customer_id,
            'user_id'=>\Auth::id(),
            'total_piutang'=>$total_piutang,
            'keterangan'=>$data->keterangan,
        ]);
        // store
        foreach ($data->data_detail as $item) {
            $piutangPenjualan->create([
                'saldo_piutang_penjualan_id'=>$data->customer_id,
                'penjualan_type'=>$penjualan_type,
                'penjualan_id'=>$item->retur_id,
                'status_bayar' =>'belum', // enum ['lunas', 'belum', 'kurang']
                'kurang_bayar'=> ($data->type == 'penjualan') ? $item->kurang_bayar : 0 - $item->kurang_bayar,
            ]);
        }
    }

    public function destroy($id)
    {
        // initiation
        $piutangPenjualanAwal = JurnalSetPiutangAwal::query()->find($id);
        $piutangPenjualan = $piutangPenjualanAwal->piutang_penjualan();
        // delete detail
        $piutangPenjualan->delete();
        // delete master
        return $piutangPenjualan->delete();
    }
}
