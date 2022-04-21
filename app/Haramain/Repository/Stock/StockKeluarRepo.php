<?php namespace App\Haramain\Repository\Stock;

use App\Models\Stock\StockKeluar;

class StockKeluarRepo
{
    public function kode($kondisi= 'baik')
    {
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

    public function storeFromRelation(object $stockKeluar, $data)
    {
        $tglKeluar = $data->tgl_keluar ?? $data->tgl_nota ?? $data->tgl_mutasi;
        $stockKeluar = $stockKeluar->create([
            'kode'=>$this->kode($data->kondisi),
            'supplier_id'=>$data->supplier_id ?? null,
            'active_cash'=>session('ClosedCash'),
            'kondisi'=>$data->kondisi,
            'gudang_id'=>$data->gudang_id,
            'tgl_keluar'=>tanggalan_database_format($tglKeluar, 'd-M-Y'),
            'user_id'=>\Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        foreach ($data->data_detail as $item) {
            $stockKeluar->stockKeluarDetail()->create([
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah']
            ]);

            (new StockInventoryRepo())->incrementArrayData($item, $data->gudang_id, $data->kondisi, 'stock_masuk');
        }
        return $stockKeluar;
    }

    public function updateFromRelation(object $stockKeluar, $oldData, $data)
    {
        // rollback
        foreach ($stockKeluar->stockKeluarDetaiil as $item) {
            (new StockInventoryRepo())->rollback($item, $oldData->gudang_id, $oldData->kondisi, 'stock_keluar');
        }

        // delete stock detail
        $stockKeluar->stockKeluarDetail()->delete();

        $tglKeluar = $data->tgl_keluar ?? $data->tgl_nota ?? $data->tgl_mutasi;
        $stockKeluar->update([
            'supplier_id'=>$data->supplier_id ?? null,
            'kondisi'=>$data->kondisi,
            'gudang_id'=>$data->gudang_id,
            'tgl_keluar'=>tanggalan_database_format($tglKeluar, 'd-M-Y'),
            'user_id'=>\Auth::id(),
            'keterangan'=>$data->keterangan,
        ]);

        foreach ($data->data_detail as $item) {
            $stockKeluar->stockKeluarDetail()->create([
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah']
            ]);

            (new StockInventoryRepo())->incrementArrayData($item, $data->gudang_id, $data->kondisi, 'stock_masuk');
        }
        return $stockKeluar;
    }
}
