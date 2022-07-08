<?php namespace App\Haramain\Service\SistemStock;

use App\Models\Stock\StockMasuk;
use App\Models\Stock\StockMasukDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class StockMasukRepo
{
    use StockKodeTrait, StockInventoryTrait;

    public function create($stockableMasukType, $stockableMasukId, $data): Model|Builder
    {
        $stockMasuk = StockMasuk::query()
            ->create([
                'kode'=>$this->kodeStockMasuk(),
                'active_cash'=>session('ClosedCash'),
                'stockable_masuk_type'=>$stockableMasukType,
                'stockable_masuk_id'=>$stockableMasukId,
                'kondisi'=>$data->kondisi,
                'gudang_id'=>$data->gudang_id ?? $data->gudang_tujuan_id,
                'supplier_id'=>$data->supplier_id ?? null,
                'tgl_masuk'=>$data->tgl_masuk ?? $data->tgl_mutasi,
                'user_id'=>\Auth::id(),
                'nomor_po'=>$data->nomor_po ?? null,
                'nomor_surat_jalan'=>$data->surat_jalan ?? null,
                'keterangan'=>$data->keterangan,
            ]);

        $this->storeDetail($data, $stockMasuk);
        return $stockMasuk;
    }

    public function update($stockMasukId, $data): Model|Collection|Builder|array|null
    {
        $stockMasuk = StockMasuk::query()->find($stockMasukId);
        // rollback
        // rollback stock inventory
        $stockMasukDetail = $stockMasuk->stockMasukDetail;
        foreach ($stockMasukDetail as $item) {
            $this->stockRollback($stockMasuk->kondisi, $stockMasuk->gudang_id, $item->produk_id, 'stock_masuk', $item->jumlah);
        }
        // delete stock_masuk_detail
        $stockMasuk->stockMasukDetail->delete();
        $update = $stockMasuk->update([
            'kondisi'=>$data->kondisi,
            'gudang_id'=>$data->gudang_id ?? $data->gudang_tujuan_id,
            'supplier_id'=>$data->supplier_id ?? null,
            'tgl_masuk'=>$data->tgl_masuk ?? $data->tgl_mutasi,
            'user_id'=>\Auth::id(),
            'nomor_po'=>$data->nomor_po ?? null,
            'nomor_surat_jalan'=>$data->surat_jalan ?? null,
            'keterangan'=>$data->keterangan,
        ]);
        $this->storeDetail($data, $stockMasuk);
        return $stockMasuk;
    }

    public function destroy($stockMasukId)
    {
        $stockMasuk = StockMasuk::query()->find($stockMasukId);
        // rollback
        // rollback stock inventory
        $stockMasukDetail = $stockMasuk->stockMasukDetail;
        foreach ($stockMasukDetail as $item) {
            $this->stockRollback($stockMasuk->kondisi, $stockMasuk->gudang_id, $item->produk_id, 'stock_masuk', $item->jumlah);
        }
        // delete stock_masuk_detail
        $stockMasuk->stockMasukDetail->delete();
        return $stockMasuk->delete();
    }

    /**
     * @param $data
     * @param Model|Collection|Builder|array|null $stockMasuk
     */
    protected function storeDetail($data, Model|Collection|Builder|array|null $stockMasuk): void
    {
        foreach ($data->detail as $item) {
            StockMasukDetail::query()
                ->create([
                    'stock_masuk_id' => $stockMasuk->id,
                    'produk_id' => $item->produk_id ?? $item['produk_id'],
                    'jumlah' => $item->jumlah ?? $item['jumlah'],
                ]);
            $this->stockIncrement(
                $data->kondisi,
                $data->gudang_id,
                $item->produk_id ?? $item['produk_id'],
                'stock_masuk',
                $item->jumlah ?? $item['jumlah']
            );
        }
    }
}
