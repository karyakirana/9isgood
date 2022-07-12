<?php namespace App\Haramain\Service\SistemKeuangan\Kasir;

use App\Models\Keuangan\PenerimaanPenjualan;
use App\Models\Keuangan\PiutangPenjualan;
use App\Models\Keuangan\PiutangPenjualanLama;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PenerimaanPenjualanRepo
{
    protected function kode()
    {
        $query = PenerimaanPenjualan::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('type', $type)
            ->latest('kode');
        $num = (int)$query->first()->last_num_char + 1 ;
        return sprintf("%05s", $num) . "/PP/" . date('Y');
    }

    /**
     * @param $data
     * @return Builder|Model
     */
    public function store($data): Model|Builder
    {
        $penerimaanPenjualan = PenerimaanPenjualan::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode(),
                'customer_id'=>$data->customer_id,
                'akun_kas_id'=>$data->akun_kas_id,
                'nominal_kas'=>$data->nominal_kas,
                'akun_piutang_id'=>$data->akun_piutang_id,
                'nominal_piutang'=>$data->nominal_piutang,
            ]);
        $penerimaanPenjualanDetail = $penerimaanPenjualan->penerimaanPenjualanDetail();
        foreach ($data->detail as $item) {
            $item = (is_array($item)) ? (object) $item : $item;
            $penerimaanPenjualanDetail->create([
                'piutang_penjualan_id'=>$item->piutang_id,
                'piutang_penjualan_type'=>$item->piutang_type,
                'nominal_dibayar'=>$item->nominal_bayar,
                'kurang_bayar'=>$item->kurang_bayar,
            ]);
        }
        return $penerimaanPenjualan;
    }

    public function update($data): Model|Collection|Builder|array|null
    {
        $penerimaanPenjualan = PenerimaanPenjualan::query()->find($data->penerimaan_penjualan_id);
        $penerimaanPenjualanDetail = $penerimaanPenjualan->penerimaanPenjualanDetail();
        $penerimaanPenjualan->update([
            'customer_id'=>$data->customer_id,
            'akun_kas_id'=>$data->akun_kas_id,
            'nominal_kas'=>$data->nominal_kas,
            'akun_piutang_id'=>$data->akun_piutang_id,
            'nominal_piutang'=>$data->nominal_piutang,
        ]);
        $penerimaanPenjualanDetail->delete();
        foreach ($data->detail as $item) {
            $item = (is_array($item)) ? (object) $item : $item;
            $penerimaanPenjualanDetail->create([
                'piutang_penjualan_id'=>$item->piutang_id,
                'piutang_penjualan_type'=>$item->piutang_type,
                'nominal_dibayar'=>$item->nominal_bayar,
                'kurang_bayar'=>$item->kurang_bayar,
            ]);
        }
        return $penerimaanPenjualan;
    }

    public function destroy($penerimaanPenjualanId)
    {
        $penerimaan = PenerimaanPenjualan::query()->find($penerimaanPenjualanId);
        $penerimaan->penerimaanPenjualanDetail()->delete();
        return $penerimaan->delete();
    }
}
