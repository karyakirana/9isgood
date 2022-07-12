<?php namespace App\Haramain\Service\SistemPenjualan\SubReturPenjualan;

use App\Models\Penjualan\PenjualanRetur;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ReturPenjualanRepo
{
    public function kode($kondisi = 'baik')
    {
        // query
        $query = PenjualanRetur::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis_retur', $kondisi)
            ->latest('kode');

        $kode = ($kondisi == 'baik') ? 'RB' : 'RR';

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kode}/".date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/{$kode}/".date('Y');
    }

    public function getData($penjualanReturId)
    {
        return PenjualanRetur::query()->find($penjualanReturId);
    }

    public function create($data): Model|Builder
    {
        $penjualanRetur = PenjualanRetur::query()
            ->create([
                'kode'=>$this->kode($data->kondisi),
                'active_cash'=>session('ClosedCash'),
                'jenis_retur'=>$data->kondisi,
                'customer_id'=>$data->customer_id,
                'gudang_id'=>$data->gudang_id,
                'user_id'=>\Auth::id(),
                'tgl_nota'=>tanggalan_database_format($data->tgl_nota, 'd-M-Y'),
                'status_bayar'=>'belum',
                'total_barang'=>$data->total_barang,
                'ppn'=>$data->ppn,
                'biaya_lain'=>$data->biaya_lain,
                'total_bayar'=>$data->total_bayar,
                'keterangan'=>$data->keterangan,
            ]);
        return $this->storeDetail($penjualanRetur, $data);
    }

    public function update($penjualanReturId, $data)
    {
        $penjualanRetur = PenjualanRetur::query()->find($penjualanReturId);
        // delete detail
        $penjualanRetur->returDetail()->delete();
        return $this->storeDetail($penjualanRetur, $data);
    }

    public function destroy($penjualanReturId)
    {
        $penjualanRetur = PenjualanRetur::query()->find($penjualanReturId);
        $penjualanRetur->returDetail()->delete();
        return $penjualanRetur->delete();
    }

    /**
     * @param Model|Collection|Builder|array|null $penjualanRetur
     * @param $data
     * @return array|Builder|Collection|Model|null
     */
    protected function storeDetail(Model|Collection|Builder|array|null $penjualanRetur, $data): array|null|Builder|Collection|Model
    {
        $penjualanReturDetail = $penjualanRetur->returDetail();
        foreach ($data->detail as $item) {
            $item = (is_array($item)) ? (object)$item : $item;
            // store penjualan retur detail
            $penjualanReturDetail->create([
                'produk_id' => $item->produk_id,
                'harga' => $item->harga,
                'jumlah' => $item->jumlah,
                'diskon' => $item->diskon,
                'sub_total' => $item->sub_total,
            ]);
        }
        return $penjualanRetur;
    }

    public function updateStatus($penjualanReturId, $status): bool|int
    {
        $penjualan = $this->getById($penjualanReturId);
        return $penjualan->update(['status_bayar'=>$status]);
    }

    public function getById($penjualanReturId): Model|Collection|Builder|array|null
    {
        return PenjualanRetur::query()
            ->find($penjualanReturId);
    }
}
