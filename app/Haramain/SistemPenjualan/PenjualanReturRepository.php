<?php namespace App\Haramain\SistemPenjualan;

use App\Models\Penjualan\PenjualanRetur;
use App\Models\Penjualan\PenjualanReturDetail;

class PenjualanReturRepository implements PenjualanInterface
{
    protected function kode($kondisi)
    {
        return null;
    }

    public function getDataById(int $id)
    {
        return PenjualanRetur::query()->find($id);
    }

    public function getDataAll(bool $closedCash = true)
    {
        $query = PenjualanRetur::query();
        if ($closedCash){
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        return $query;
    }

    public function store(object|array $data)
    {
        $data = (object) $data;
        $penjualanRetur = PenjualanRetur::query()
            ->create([
                'kode'=>$this->kode($data->kondisi),
                'active_cash'=>session('ClosedCash'),
                'jenis_retur'=>$data->kondisi,
                'customer_id'=>$data->customerId,
                'gudang_id'=>$data->gudangId,
                'user_id'=>$data->userId,
                'tgl_nota'=>tanggalan_database_format($data->tglNota, 'd-M-Y'),
                'tgl_tempo'=>($data->statusBayar == 'tempo') ? tanggalan_database_format($data->tglTempo, 'd-M-Y') : null,
                'status_bayar'=>'belum',
                'total_barang'=>$data->totalBarang,
                'ppn'=>$data->ppn,
                'biaya_lain'=>$data->biayaLain,
                'total_bayar'=>$data->totalbayar,
                'keterangan'=>$data->keterangan,
            ]);
        $this->storeDetail($data->dataDetail, $penjualanRetur->id);
        return $penjualanRetur;
    }

    public function update(object|array $data)
    {
        $data = (object) $data;
        $penjualanRetur = PenjualanRetur::query()->find($data->penjualanReturId);
        $penjualanRetur->update([
            'jenis_retur'=>$data->kondisi,
            'customer_id'=>$data->customerId,
            'gudang_id'=>$data->gudangId,
            'user_id'=>$data->userId,
            'tgl_nota'=>tanggalan_database_format($data->tglNota, 'd-M-Y'),
            'tgl_tempo'=>($data->statusBayar == 'tempo') ? tanggalan_database_format($data->tglTempo, 'd-M-Y') : null,
            'status_bayar'=>'belum',
            'total_barang'=>$data->totalBarang,
            'ppn'=>$data->ppn,
            'biaya_lain'=>$data->biayaLain,
            'total_bayar'=>$data->totalbayar,
            'keterangan'=>$data->keterangan,
        ]);
        $this->storeDetail($data->dataDetail, $penjualanRetur->id);
        return $penjualanRetur;
    }

    public function rollback(int $id)
    {
        return PenjualanRetur::query()->where('penjualan_retur_id', $id)->delete();
    }

    public function destroy(int $id)
    {
        $this->rollback($id);
        return PenjualanRetur::destroy($id);
    }

    protected function storeDetail($dataDetail, $penjualanReturId)
    {
        foreach ($dataDetail as $item) {
            $item = (object) $item;
            PenjualanReturDetail::query()
                ->create([
                    'penjualan_retur_id'=>$penjualanReturId,
                    'produk_id'=>$item->produk_id,
                    'harga'=>$item->harga,
                    'jumlah'=>$item->jumlah,
                    'diskon'=>$item->diskomn,
                    'sub_total'=>$item->sub_total,
                ]);
        }
    }
}
