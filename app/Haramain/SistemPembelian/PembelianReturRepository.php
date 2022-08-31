<?php namespace App\Haramain\SistemPembelian;

use App\Models\Purchase\PembelianRetur;
use App\Models\Purchase\PembelianReturDetail;

class PembelianReturRepository implements PembelianInterface
{
    protected function kode($jenis)
    {
        // query
        $query = PembelianRetur::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $jenis)
            ->latest('kode');

        $kodeJenis = ($jenis == 'INTERNAL') ? 'PBRI' : 'PBRE';

        // check last num
        if ($query->doesntExist()) {
            return '0001/'.$kodeJenis.'/' . date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num) . "/".$kodeJenis."/" . date('Y');
    }

    public function getDataById(int $pembelianId)
    {
        return PembelianRetur::query()->find($pembelianId);
    }

    public function getDataAll(bool $closedCash = true)
    {
        $query = PembelianRetur::query();
        if ($closedCash){
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        return $query;
    }

    public function store(object|array $data)
    {
        $data = (object) $data;
        $pembelianRetur = PembelianRetur::query()
            ->create([
                'kode'=>$this->kode($data->kondisi),
                'active_cash'=>session('ClosedCash'),
                'supplier_id'=>$data->supllierId,
                'gudang_id'=>$data->gudangId,
                'user_id'=>$data->userID,
                'tgl_nota'=>tanggalan_database_format($data->tglNota, 'd-M-Y'),
                'tgl_tempo'=>($data->jenisBayar == 'tempo') ? tanggalan_database_format($data->tgltempo, 'd-M-Y') : null,
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

    public function update(object|array $data)
    {
        $data = (object)$data;
        $pembelianRetur = $this->getDataById($data->pembelianReturId);
        $pembelianRetur->update([
            'supplier_id'=>$data->supllierId,
            'gudang_id'=>$data->gudangId,
            'user_id'=>$data->userID,
            'tgl_nota'=>tanggalan_database_format($data->tglNota, 'd-M-Y'),
            'tgl_tempo'=>($data->jenisBayar == 'tempo') ? tanggalan_database_format($data->tgltempo, 'd-M-Y') : null,
            'jenis_bayar'=>$data->jenisBayar,
            'status_bayar'=>'belum',
            'total_barang'=>$data->totalBarang,
            'ppn'=>$data->ppn,
            'biaya_lain'=>$data->biayaLain,
            'total_bayar'=>$data->totalBayar,
            'keterangan'=>$data->keterangan,
        ]);
        $pembelianRetur->increment('print');
        $this->storeDetail($data->dataDetail, $pembelianRetur->id);
        return $pembelianRetur;
    }

    public function rollback(int $pembelianId)
    {
        return PembelianReturDetail::query()->where('pembelian_id', $pembelianId)->delete();
    }

    public function destroy(int $pembelianId)
    {
        $this->rollback($pembelianId);
        return PembelianRetur::destroy($pembelianId);
    }

    protected function storeDetail($dataDetail, $pembelianReturId)
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
}
