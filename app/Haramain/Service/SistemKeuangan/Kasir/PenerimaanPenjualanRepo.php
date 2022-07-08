<?php namespace App\Haramain\Service\SistemKeuangan\Kasir;

use App\Models\Keuangan\PenerimaanPenjualan;
use App\Models\Keuangan\PiutangPenjualan;
use App\Models\Keuangan\PiutangPenjualanLama;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PenerimaanPenjualanRepo
{
    protected function kode()
    {
        return null;
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
            $type = ($item->type == 'piutang_lama') ? PiutangPenjualan::class : PiutangPenjualanLama::class;
            $penerimaanPenjualanDetail->create([
                'piutang_penjualan_id'=>$item->piutang_id,
                'piutang_penjualan_type'=>$item->piutang_type,
                'nominal_dibayar'=>$item->nominal_bayar,
                'kurang_bayar'=>$item->kurang_bayar,
            ]);
        }
        return $penerimaanPenjualan;
    }

    public function update($data)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
