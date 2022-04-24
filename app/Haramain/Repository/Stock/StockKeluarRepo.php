<?php namespace App\Haramain\Repository\Stock;

use App\Models\Stock\StockKeluar;
use Illuminate\Support\Facades\Auth;

class StockKeluarRepo
{
    public function kode($kondisi= 'baik', $jenisMutasi = null)
    {
        if ($jenisMutasi){
            $kondisi = $this->setKondisi($jenisMutasi);
        }

        // query
        $query = StockKeluar::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('kondisi', $kondisi)
            ->latest('kode');

        $kodeKondisi = ($kondisi == 'baik') ? 'SK' : 'SKR';

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kodeKondisi}/".date('Y');
        }

        $num = (int) $query->first()->last_num_trans + 1;
        return sprintf("%04s", $num)."/{$kodeKondisi}/".date('Y');
    }

    public function setKondisi($kondisi)
    {
        if ($kondisi == 'baik_baik'|| 'baik_rusak'){
            return 'baik';
        }

        return 'rusak';
    }

    public function storeFromRelation(object $stockKeluar, $data)
    {
        $tglKeluar = $data->tgl_keluar ?? $data->tgl_nota ?? $data->tgl_mutasi;

        if (isset($data->jenis_mutasi)){
            $kondisi = $this->setKondisi($data->jenis_mutasi);
        }

        $stockKeluar = $stockKeluar->create([
            'kode'=>$this->kode($data->kondisi ?? null, $data->jenis_mutasi),
            'supplier_id'=>$data->supplier_id ?? null,
            'active_cash'=>session('ClosedCash'),
            'kondisi'=> $kondisi ?? $data->kondisi,
            'gudang_id'=>$data->gudang_id ?? $data->gudang_asal_id,
            'tgl_keluar'=>tanggalan_database_format($tglKeluar, 'd-M-Y'),
            'user_id'=>Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        foreach ($data->data_detail as $item) {
            $stockKeluar->stockKeluarDetail()->create([
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah']
            ]);

            (new StockInventoryRepo())->incrementArrayData($item, $data->gudang_id ?? $data->gudang_asal_id, $kondisi ?? $data->kondisi, 'stock_masuk');
        }
        return $stockKeluar;
    }

    public function updateFromRelation(object $stockKeluar, $data)
    {
        // initiate
        $stockKeluar = $stockKeluar->first();
        // rollback
        foreach ($stockKeluar->stockKeluarDetaiil as $item) {
            (new StockInventoryRepo())->rollback($item, $stockKeluar->gudang_id, $stockKeluar->kondisi, 'stock_keluar');
        }

        // delete stock detail
        $stockKeluar->stockKeluarDetail()->delete();

        $tglKeluar = $data->tgl_keluar ?? $data->tgl_nota ?? $data->tgl_mutasi;

        if (isset($data->jenis_mutasi)){
            $kondisi = $this->setKondisi($data->jenis_mutasi);
        }

        $stockKeluar->update([
            'supplier_id'=>$data->supplier_id ?? null,
            'kondisi'=>$kondisi ?? $data->kondisi,
            'gudang_id'=>$data->gudang_id,
            'tgl_keluar'=>tanggalan_database_format($tglKeluar, 'd-M-Y'),
            'user_id'=>Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        foreach ($data->data_detail as $item) {
            $stockKeluar->stockKeluarDetail()->create([
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah']
            ]);

            (new StockInventoryRepo())->incrementArrayData($item, $data->gudang_id ?? $data->gudang_asal_id, $kondisi ?? $data->kondisi, 'stock_keluar');
        }
        return $stockKeluar;
    }
}
