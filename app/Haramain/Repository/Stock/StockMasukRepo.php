<?php namespace App\Haramain\Repository\Stock;

use App\Models\Stock\StockMasuk;
use Illuminate\Support\Facades\Auth;

class StockMasukRepo
{
    protected $stockInventory;

    public function __construct()
    {
        $this->stockInventory = new StockInventoryRepo();
    }

    public function kode($kondisi = 'baik', $jenisMutasi = null)
    {
        if ($jenisMutasi){
            $kondisi = $this->setKondisi($jenisMutasi);
        }

        // query
        $query = StockMasuk::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('kondisi', $kondisi)
            ->latest('kode');

        $kodeKondisi = ($kondisi == 'baik') ? 'SM' : 'SMR';

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kodeKondisi}/".date('Y');
        }

        $num = (int) $query->first()->last_num_trans + 1;
        return sprintf("%04s", $num)."/{$kodeKondisi}/".date('Y');
    }

    public function setKondisi($kondisi)
    {
        if ($kondisi == 'baik_rusak'|| 'rusak_rusak'){
            return 'rusak';
        }

        return 'baik';
    }

    public function storeFromRelation(object $stockMasuk, $data)
    {
        $tglMasuk = $data->tgl_masuk ?? $data->tgl_nota ?? $data->tgl_mutasi;
        // store stock masuk
        $stockMasuk = $stockMasuk->create([
            'kode'=>$this->kode($data->kondisi ?? null, $data->jenis_mutasi),
            'active_cash'=>session('ClosedCash'),
            'kondisi'=>$data->kondisi,
            'gudang_id'=>$data->gudang_id ?? $data->gudang_tujuan_id,
            'supplier_id'=>$data->supplier_id,
            'tgl_masuk'=>tanggalan_database_format($tglMasuk, 'd-M-Y'),
            'user_id'=>Auth::id(),
            'nomor_po'=>null,
            'nomor_surat_jalan'=>$data->nomor_surat_jalan,
            'keterangan'=>$data->keterangan,
        ]);
        // store detail
        foreach ($data->data_detail as $item)
        {
            $stockMasuk->stockMasukDetail()->create([
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah'],
            ]);
            // stock inventory
            $this->stockInventory->incrementArrayData($item, $data->gudang_id, $data->kondisi, 'stock_masuk');
        }

        return $stockMasuk;
    }

    public function updateFromRelation(object $stockMasuk, $data)
    {
        $stockMasuk = $stockMasuk->first();
        // rollback
        foreach ($stockMasuk->stockMasukDetail as $item) {
            $this->stockInventory->rollback($item, $stockMasuk->gudang_id, $stockMasuk->kondisi, 'stock_masuk');
        }

        // delete detail
        $stockMasuk->stockMasukDetail()->delete();

        $tglMasuk = $data->tgl_masuk ?? $data->tgl_nota ?? $data->tgl_mutasi;
        $stockMasuk->update([
            'kondisi'=>$data->kondisi,
            'gudang_id'=>$data->gudang_id,
            'supplier_id'=>$data->supplier_id,
            'tgl_masuk'=>tanggalan_database_format($tglMasuk, 'd-M-Y'),
            'user_id'=>Auth::id(),
            'nomor_po'=>null,
            'nomor_surat_jalan'=>$data->nomor_surat_jalan,
            'keterangan'=>$data->keterangan,
        ]);

        // store detail
        foreach ($data->data_detail as $item)
        {
            $stockMasuk->stockMasukDetail()->create([
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah'],
            ]);
            // stock inventory
            $this->stockInventory->incrementArrayData($item, $data->gudang_id, $data->kondisi, 'stock_masuk');
        }

        return $stockMasuk;
    }
}
