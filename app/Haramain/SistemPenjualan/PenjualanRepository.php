<?php namespace App\Haramain\SistemPenjualan;

use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PenjualanRepository implements PenjualanInterface
{

    protected function kode()
    {
        $query = Penjualan::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()){
            return '0001/PJ/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PJ/".date('Y');
    }

    public function getDataById(int $id)
    {
        return Penjualan::query()->find($id);
    }

    public function getDataAll(bool $closedCash = true)
    {
        $query = Penjualan::query();
        if ($closedCash){
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        return $query;
    }

    public function store(object|array $data)
    {
        $data = (object) $data;
        $penjualan = Penjualan::query()
            ->create([
                'kode'=>$this->kode(),
                'active_cash'=>session('ClosedCash'),
                'customer_id'=>$data->customerId,
                'gudang_id'=>$data->gudangId,
                'user_id'=>$data->userId,
                'tgl_nota'=>tanggalan_database_format($data->tglNota, 'd-M-Y'),
                'tgl_tempo'=>($data->jenisBayar == 'tempo') ? tanggalan_database_format($data->tglTempo, 'd-M-Y') : null,
                'jenis_bayar'=>$data->jenisBayar,
                'status_bayar'=>$data->statusBayar,
                'total_barang'=>$data->totalBayar,
                'ppn'=>$data->ppn,
                'biaya_lain'=>$data->biayaLain,
                'total_bayar'=>$data->totalBayar,
                'keterangan'=>$data->keterangan,
                'print'=>1,
            ]);
        $this->storeDetail($data->dataDetail, $penjualan->id);
        return $penjualan;
    }

    /**
     * @param object|array $data
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function update(object|array $data)
    {
        $data = (object) $data;
        $penjualan = $this->getDataById($data->penjualanId);
        $penjualan->update([
            'customer_id'=>$data->customerId,
            'gudang_id'=>$data->gudangId,
            'user_id'=>$data->userId,
            'tgl_nota'=>tanggalan_database_format($data->tglNota, 'd-M-Y'),
            'tgl_tempo'=>($data->jenisBayar == 'tempo') ? tanggalan_database_format($data->tglTempo, 'd-M-Y') : null,
            'jenis_bayar'=>$data->jenisBayar,
            'status_bayar'=>'belum',
            'total_barang'=>$data->totalBayar,
            'ppn'=>$data->ppn,
            'biaya_lain'=>$data->biayaLain,
            'total_bayar'=>$data->totalBayar,
            'keterangan'=>$data->keterangan,
        ]);
        $penjualan->increment('print');
        $penjualan = $this->getDataById($data->penjualanId);
        $this->storeDetail($data->dataDetail, $penjualan->id);
        return $penjualan;
    }

    public function rollback(int $id)
    {
        return PenjualanDetail::query()->where('penjualan_id', $id)->delete();
    }

    public function destroy(int $id)
    {
        $this->rollback($id);
        return Penjualan::destroy($id);
    }

    protected function storeDetail($dataDetail, $id):void
    {
        foreach ($dataDetail as $item) {
            $item = (object) $item;
            PenjualanDetail::query()
                ->create([
                    'penjualan_id'=>$id,
                    'produk_id'=>$item->produk_id,
                    'harga'=>$item->harga,
                    'jumlah'=>$item->jumlah,
                    'diskon'=>$item->diskon,
                    'sub_total'=>$item->sub_total,
                ]);
        }
    }
}
