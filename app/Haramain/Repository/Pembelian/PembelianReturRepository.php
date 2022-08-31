<?php namespace App\Haramain\Repository\Pembelian;

use App\Haramain\Repository\PersediaanPerpetualRepo;
use App\Haramain\Repository\PersediaanTransaksiRepo;
use App\Haramain\Repository\StockInventoryRepository;
use App\Haramain\Repository\StockKeluarRepository;
use App\Haramain\Repository\TransaksiRepositoryInterface;
use App\Models\Keuangan\HutangPembelian;
use App\Models\Purchase\PembelianRetur;
use App\Models\Purchase\PembelianReturDetail;

class PembelianReturRepository
{

    protected function kode(): ?string
    {
        // query
        $query = PembelianRetur::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()) {
            return '0001/PBR/' . date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num) . "/PBR/" . date('Y');
    }

    public function store($data):PembelianRetur
    {
        $data = (object)$data;
        $pembelianRetur = PembelianRetur::query()
            ->create([
                'kode'=>$this->kode(),
                'active_cash'=>session('ClosedCash'),
                'supplier_id'=>$data->supplierId,
                'gudang_id'=>$data->gudangId,
                'user_id'=>$data->userId,
                'tgl_nota'=>$data->tglNota,
                'tgl_tempo'=>($data->jenisBayar == 'tempo') ? tanggalan_database_format($data->tglTempo) : null,
                'jenis_bayar'=>$data->jenisBayar,
                'status_bayar'=>'belum',
                'total_barang'=>$data->totalBarang,
                'ppn'=>$data->ppn,
                'biaya_lain'=>$data->biayaLain,
                'total_bayar'=>$data->totalBayar,
                'keterangan'=>$data->keterangan,
                'print'=>1,
            ]);
        $this->storeDetail($data->dataDetail, $pembelianRetur->id);
        return $pembelianRetur;
    }

    public function update($data)
    {
        $data = (object)$data;
        $pembelianRetur = PembelianRetur::query()->find($data->pembelianReturId);
        $pembelianRetur->update([
            'supplier_id'=>$data->supplierId,
            'gudang_id'=>$data->gudangId,
            'user_id'=>$data->userId,
            'tgl_nota'=>$data->tglNota,
            'tgl_tempo'=>($data->jenisBayar == 'tempo') ? tanggalan_database_format($data->tglTempo) : null,
            'jenis_bayar'=>$data->jenisBayar,
            'status_bayar'=>'belum',
            'total_barang'=>$data->totalBarang,
            'ppn'=>$data->ppn,
            'biaya_lain'=>$data->biayaLain,
            'total_bayar'=>$data->totalBayar,
            'keterangan'=>$data->keterangan,
        ]);
        $pembelianRetur->increment('print');
        $this->storeDetail($data->dataDetail, $data->pembelianReturId);
        return $pembelianRetur;
    }

    protected function storeDetail($dataDetail, $pembelianReturId):void
    {
        foreach ($dataDetail as $item) {
            $item = (object)$item;
            PembelianReturDetail::query()
                ->create([
                    'pembelian_retur_id'=>$pembelianReturId,
                    'produk_id'=>$item->produk_id,
                    'harga'=>$item->harga,
                    'jumlah'=>$item->jumlah,
                    'diskon'=>$item->diskon,
                    'sub_total'=>$item->sub_total,
                ]);
        }
    }

    public function rollback($pembelianReturId)
    {
        return PembelianReturDetail::query()->where('pembelian_retur_id', $pembelianReturId)->delete();
    }

    public function destroy($pembelianReturId)
    {
        $this->rollback($pembelianReturId);
        return PembelianRetur::destroy($pembelianReturId);
    }
}
