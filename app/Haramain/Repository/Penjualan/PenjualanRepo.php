<?php namespace App\Haramain\Repository\Penjualan;

use App\Haramain\Repository\Stock\StockInventoryRepo;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanDetail;

class PenjualanRepo
{
    protected $penjualan;
    protected $penjualanDetail;

    public function __construct()
    {
        $this->penjualan = new Penjualan();
        $this->penjualanDetail = new PenjualanDetail();
    }

    public function kode()
    {
        $query = $this->penjualan::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()){
            return '0001/PJ/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PJ/".date('Y');
    }

    public function getDataById($penjualanId)
    {
        return $this->penjualan->newQuery()->find($penjualanId);
    }

    public function store($data)
    {
        // simpan penjualan
        $penjualan = $this->penjualan->newQuery()->create([
            'kode'=>$this->kode(),
            'active_cash'=>session('ClosedCash'),
            'customer_id'=>$data['customerId'],
            'gudang_id'=>$data['gudangId'],
            'user_id'=>$data['userId'],
            'tgl_nota'=>tanggalan_database_format($data['tglNota'],'d-M-Y'),
            'tgl_tempo'=>($data['jenisBayar'] == 'tempo') ? tanggalan_database_format($data['tglTempo'], 'd-M-Y') : null,
            'jenis_bayar'=>$data['jenisBayar'],
            'status_bayar'=>'belum',
            'total_barang'=>$data['totalBarang'],
            'ppn'=>$data['ppn'],
            'biaya_lain'=>$data['biayaLain'],
            'total_bayar'=>$data['totalBayar'],
            'keterangan'=>$data['keterangan'],
            'print'=>1,
        ]);
        $this->storeDetail($data['dataDetail'], $penjualan->id);
        return $penjualan;
    }

    protected function storeDetail($dataItem, $penjualanId)
    {
        foreach ($dataItem as $item) {
            $this->penjualanDetail->newQuery()
                ->create([
                    'penjualan_id'=>$penjualanId,
                    'produk_id'=>$item['produk_id'],
                    'harga'=>$item['harga'],
                    'jumlah'=>$item['jumlah'],
                    'diskon'=>$item['diskon'],
                    'sub_total'=>$item['sub_total'],
                ]);
        }
    }

    public function update($data)
    {
        $penjualan = $this->penjualan->newQuery()->find($data['penjualanId']);
        $update = $penjualan->update([
            'customer_id'=>$data['customerId'],
            'gudang_id'=>$data['gudangId'],
            'user_id'=>$data['userId'],
            'tgl_nota'=>tanggalan_database_format($data['tglNota'],'d-M-Y'),
            'tgl_tempo'=>($data['jenisBayar'] == 'tempo') ? tanggalan_database_format($data['tglTempo'], 'd-M-Y') : null,
            'jenis_bayar'=>$data['jenisBayar'],
            'status_bayar'=>'belum',
            'total_barang'=>$data['totalBarang'],
            'ppn'=>$data['ppn'],
            'biaya_lain'=>$data['biayaLain'],
            'total_bayar'=>$data['totalBayar'],
            'keterangan'=>$data['keterangan'],
        ]);
        $penjualan->increment('print');
        $this->storeDetail($data['dataDetail'], $penjualan->id);
        return $penjualan;
    }

    public function rollback($penjualanId)
    {
        return $this->penjualanDetail->newQuery()->where('penjualan_id', $penjualanId)->delete();
    }

    public function destroy($penjualanId)
    {
        $this->rollback($penjualanId);
        return $this->penjualan->newQuery()->find($penjualanId)->delete();
    }
}
