<?php namespace App\Haramain\Repository\Penjualan;

use App\Haramain\Repository\Stock\StockInventoryRepo;
use App\Haramain\Repository\StockKeluarRepository;
use App\Models\Penjualan\Penjualan;

class PenjualanPureRepo
{
    public $closedCash;

    public function __construct()
    {
        $this->closedCash = session('ClosedCash');
    }

    public function getByDate($tglAwal, $tglAkhir)
    {
        $query = Penjualan::query()
            ->where('active_cash', session('ClosedCash'))
            ->oldest('kode');
        if ($tglAwal && $tglAkhir){
            return $query->whereBetween('tgl_nota', [tanggalan_database_format($tglAwal, 'd-M-Y'), tanggalan_database_format($tglAkhir, 'd-M-Y')]);
        }
        return $query;
    }

    public function getByMonth($month)
    {
        return Penjualan::query()
            ->whereMonth('tgl_nota', $month)
            ->where('active_cash', session('ClosedCash'))
            ->get();
    }


    public function kode(): string
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

    public function store($data)
    {
        //dd($data);
        $penjualan = Penjualan::query()->create([
            'kode'=>$this->kode(),
            'active_cash'=>$this->closedCash,
            'customer_id'=>$data->customer_id,
            'gudang_id'=>$data->gudang_id,
            'user_id'=>\Auth::id(),
            'tgl_nota'=>tanggalan_database_format($data->tgl_nota, 'd-M-Y'),
            'tgl_tempo'=>($data->jenis_bayar == 'tempo') ? tanggalan_database_format($data->tgl_tempo, 'd-M-Y') : null,
            'jenis_bayar'=>$data->jenis_bayar,
            'status_bayar'=>'belum',
            'total_barang'=>$data->total_barang,
            'ppn'=>$data->ppn,
            'biaya_lain'=>$data->biaya_lain,
            'total_bayar'=>$data->total_bayar,
            'keterangan'=>$data->keterangan,
            'print'=>null,
        ]);

        $stockKeluar = $penjualan->stockKeluarMorph()->create([
            'kode'=>StockKeluarRepository::kode(),
            'active_cash'=>$this->closedCash,
            'kondisi'=>'baik',
            'gudang_id'=>$data->gudang_id,
            'tgl_keluar'=>tanggalan_database_format($data->tgl_nota, 'd-M-Y'),
            'user_id'=>\Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        return $this->storeDetail($data, $penjualan, $stockKeluar);
    }

    public function update($data)
    {
        //dd($data);
        $penjualan = Penjualan::query()->find($data->penjualan_id);

        $penjualanDetail = $penjualan->penjualanDetail();

        $stockKeluar = $penjualan->stockKeluarMorph()->first();

        $stockKeluarDetail = $stockKeluar->stockKeluarDetail();

        // rollback stock inventory
        foreach ($stockKeluar->stockKeluarDetail as $item){
            (new StockInventoryRepo())->rollback($item, $stockKeluar->gudang_id, $stockKeluar->kondisi, 'stock_keluar');
        }
        // delete penjualan detail
        $penjualanDetail->delete();
        // delete stock keluar detail
        $stockKeluarDetail->delete();

        // update penjualan
        $penjualan->update([
            'customer_id'=>$data->customer_id,
            'gudang_id'=>$data->gudang_id,
            'user_id'=>\Auth::id(),
            'tgl_nota'=>tanggalan_database_format($data->tgl_nota, 'd-M-Y'),
            'tgl_tempo'=>($data->jenis_bayar == 'tempo') ? tanggalan_database_format($data->tgl_tempo, 'd-M-Y') : null,
            'jenis_bayar'=>$data->jenis_bayar,
            'status_bayar'=>'belum',
            'total_barang'=>$data->total_barang,
            'ppn'=>$data->ppn,
            'biaya_lain'=>$data->biaya_lain,
            'total_bayar'=>$data->total_bayar,
            'keterangan'=>$data->keterangan,
        ]);
        // update stock keluar
        $stockKeluar->update([
            'gudang_id'=>$data->gudang_id,
            'tgl_keluar'=>tanggalan_database_format($data->tgl_nota, 'd-M-Y'),
            'user_id'=>\Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        return $this->storeDetail($data, $penjualan, $stockKeluar);
    }

    /**
     * @param $data
     * @param object $penjualan
     * @param object $stockKeluar
     * @return mixed
     */
    protected function storeDetail($data,object $penjualan,object $stockKeluar): mixed
    {
        foreach ($data->data_detail as $item) {
            // simpan penjualan detail
            $penjualan->penjualanDetail()->create([
                'produk_id' => $item['produk_id'],
                'harga' => $item['harga'],
                'jumlah' => $item['jumlah'],
                'diskon' => $item['diskon'],
                'sub_total' => $item['sub_total'],
            ]);
            // simpan stock keluar detail
            $stockKeluar->stockKeluarDetail()->create([
                'produk_id' => $item['produk_id'],
                'jumlah' => $item['jumlah'],
            ]);
            // update stock inventory
            (new StockInventoryRepo())->incrementArrayData($item, $data->gudang_id, 'baik', 'stock_keluar');
        }

        return $penjualan->id;
    }
}
