<?php namespace App\Haramain\Repository\Penjualan;

use App\Haramain\Repository\Stock\StockInventoryRepo;
use App\Haramain\Repository\StockMasuk\StockMasukRepository;
use App\Models\Penjualan\PenjualanRetur;
use App\Models\Penjualan\PenjualanReturDetail;
use Illuminate\Database\Eloquent\Model;

class PenjualanReturRepo
{
    protected $penjualanRetur;
    protected $penjualanReturDetail;

    public function __construct()
    {
        $this->penjualanRetur = new PenjualanRetur();
        $this->penjualanReturDetail = new PenjualanReturDetail();
    }

    public function kode($kondisi)
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

    public function getData($penjualanId)
    {
        return PenjualanRetur::query()->find($penjualanId);
    }

    public function store($data)
    {
        $penjualanRetur = PenjualanRetur::query()->create([
            'kode'=>$this->kode($data['kondisi']),
            'active_cash'=>session('ClosedCash'),
            'jenis_retur'=>$data['kondisi'],
            'customer_id'=>$data['customerId'],
            'gudang_id'=>$data['gudangId'],
            'user_id'=>\Auth::id(),
            'tgl_nota'=>tanggalan_database_format($data->tglNota, 'd-M-Y'),
            'status_bayar'=>'belum',
            'total_barang'=>$data['totalBarang'],
            'ppn'=>$data['ppn'],
            'biaya_lain'=>$data['biayaLain'],
            'total_bayar'=>$data['totalBayar'],
            'keterangan'=>$data['keterangan'],
        ]);

        // store detail
        $this->storeDetail($data, $penjualanRetur->id);
        return $penjualanRetur;
    }

    protected function storeDetail($data, $penjualanReturId)
    {
        foreach ($data['dataDetail'] as $item) {
            PenjualanReturDetail::query()->create([
                'penjualan_retur_id'=>$penjualanReturId,
                'produk_id'=>$item['produkId'],
                'harga'=>$item['harga'],
                'jumlah'=>$item['jumlah'],
                'diskon'=>$item['diskon'],
                'sub_total'=>$item['subTotal'],
            ]);
        }
    }

    public function update($data)
    {
        // initiate
        $penjualanRetur = PenjualanRetur::query()->find($data->penjualan_retur_id);

        $penjualanRetur->update([
            'customer_id'=>$data['customerId'],
            'gudang_id'=>$data['gudangId'],
            'user_id'=>\Auth::id(),
            'tgl_nota'=>tanggalan_database_format($data->tglNota, 'd-M-Y'),
            'status_bayar'=>'belum',
            'total_barang'=>$data['totalBarang'],
            'ppn'=>$data['ppn'],
            'biaya_lain'=>$data['biayaLain'],
            'total_bayar'=>$data['totalBayar'],
            'keterangan'=>$data['keterangan'],
        ]);

        $this->storeDetail($data, $penjualanRetur->id);

        return $penjualanRetur;
    }

    public function rollback($penjualanReturId)
    {
        $penjualanRetur = PenjualanRetur::query()->find($penjualanReturId);
        $this->penjualanReturDetail->newQuery()->where('penjualan_retur_id', $penjualanRetur->id)->delete();
        return $penjualanRetur;
    }

    public function destroy($penjualanReturId)
    {
        return $this->rollback($penjualanReturId)->delete();
    }


}
