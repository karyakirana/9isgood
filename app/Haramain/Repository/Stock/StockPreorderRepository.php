<?php namespace App\Haramain\Repository\Stock;

use App\Models\Stock\Preorder;
use App\Models\Stock\PreorderDetail;

class StockPreorderRepository
{
    protected $stockPreorder;
    protected $stockPreorderDetail;

    public function __construct()
    {
        $this->stockPreorder = new Preorder();
        $this->stockPreorderDetail = new PreorderDetail();
    }

    public function kode()
    {
        return null;
    }

    public function store($data)
    {
        $preorder = $this->stockPreorder->newQuery()
            ->create([
                'kode'=>$this->kode(),
                'active_cash'=>session('ClosedCash'),
                'tgl_preorder'=>tanggalan_database_format($data['tglPreorder'], 'd-M-Y'),
                'tgl_selesai'=>null,
                'status'=>'belum',
                'supplier_id'=>$data['supplierId'],
                'user_id'=>$data['userId'],
                'total_barang'=>$data['totalBarang'],
                'keterangan'=>$data['keterangan'],
            ]);
        $this->storeDetail($data['dataDetail'], $preorder->id);
        return $preorder;
    }

    protected function storeDetail($dataDetail, $preorderId)
    {
        foreach ($dataDetail as $item) {
            $this->stockPreorderDetail->newQuery()->create([
                'stock_preorder_id'=>$preorderId,
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah'],
            ]);
        }
    }

    public function update($data)
    {
        $preorder = $this->stockPreorder->newQuery()->find($data['preorderId']);
        $preorder->update([
            'tgl_preorder'=>tanggalan_database_format($data['tglPreorder'], 'd-M-Y'),
            'tgl_selesai'=>null,
            'status'=>'belum',
            'supplier_id'=>$data['supplierId'],
            'user_id'=>$data['userId'],
            'total_barang'=>$data['totalBarang'],
            'keterangan'=>$data['keterangan'],
        ]);
        $this->storeDetail($data['dataDetail'], $preorder->id);
        return $preorder;
    }

    public function rollback($preorderId)
    {
        $preorder = $this->stockPreorder->newQuery()->find($preorderId);
        $this->stockPreorderDetail->newQuery()->where('stock_preorder_id', $preorderId)->delete();
        return $preorder;
    }

    public function destroy($preorderId)
    {
        return $this->rollback($preorderId)->delete();
    }
}
