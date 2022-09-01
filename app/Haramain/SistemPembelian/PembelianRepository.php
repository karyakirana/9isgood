<?php namespace App\Haramain\SistemPembelian;

use App\Models\Purchase\Pembelian;
use App\Models\Purchase\PembelianDetail;

class PembelianRepository implements PembelianInterface
{
    private function kode($jenisPembelian)
    {
        $query = Pembelian::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $jenisPembelian)
            ->latest('kode');

        $kode = ($jenisPembelian == 'INTERNAL') ? 'PBI' : 'PB';

        // check last num
        if ($query->doesntExist()) {
            return '0001/' .$kode.'/'. date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num) .'/'.$kode.'/'. date('Y');
    }

    public function getDataById($pembelianId)
    {
        return Pembelian::query()->find($pembelianId);
    }

    public function getDataAll($closedCash = true)
    {
        $query = Pembelian::query();
        if ($closedCash){
            $query = $query->where('active_cash', session('ClosedCash'));
        }
        return $query->get();
    }

    public function store($data)
    {
        $data = (object)$data;
        $pembelian = Pembelian::query()->create([
            'kode'=>$this->kode($data->jenis),
            'nomor_nota'=>$data->nomorNota,
            'nomor_surat_jalan'=>$data->suratJalan,
            'jenis'=>$data->jenis,
            'active_cash'=>session('ClosedCash'),
            'supplier_id'=>$data->supplierId,
            'gudang_id'=>$data->gudangId,
            'user_id'=>auth()->id(),
            'tgl_nota'=>tanggalan_database_format($data->tglNota, 'd-M-Y'),
            'tgl_tempo'=>($data->jenisBayar == 'tempo') ? tanggalan_database_format($data->tglTempo, 'd-M-Y') : null,
            'jenis_bayar'=>$data->jenisBayar,
            'status_bayar'=>'belum',
            'total_barang'=>$data->totalBarang,
            'ppn'=>$data->ppn,
            'biaya_lain'=>$data->biayaLain,
            'total_bayar'=>$data->totalBayar,
            'keterangan'=>$data->keterangan,
            'print'=>1,
        ]);

        $this->storeDetail($data->dataDetail, $pembelian->id);

        return $pembelian;
    }

    public function update($data)
    {
        $data = (object) $data;
        $pembelian = $this->getDataById($data->pembelianId)->update([
            'nomor_nota'=>$data->nomorNota,
            'nomor_surat_jalan'=>$data->suratJalan,
            'jenis'=>$data->jenis,
            'supplier_id'=>$data->supplierId,
            'gudang_id'=>$data->gudangId,
            'user_id'=>auth()->id(),
            'tgl_nota'=>tanggalan_database_format($data->tglNota, 'd-M-Y'),
            'tgl_tempo'=>($data->jenisBayar == 'tempo') ? tanggalan_database_format($data->tglTempo, 'd-M-Y') : null,
            'jenis_bayar'=>$data->jenisBayar,
            'status_bayar'=>'belum',
            'total_barang'=>$data->totalBarang,
            'ppn'=>$data->ppn,
            'biaya_lain'=>$data->biayaLain,
            'total_bayar'=>$data->totalBayar,
            'keterangan'=>$data->keterangan,
            'print'=>1,
        ]);
        $this->storeDetail($data->dataDetail, $data->pembelianId);
        return $this->getDataById($data->pembelianId);
    }

    public function rollback($pembelianId)
    {
        return PembelianDetail::query()->where('pembelian_id', $pembelianId)->delete();
    }

    public function destroy($pembelianId)
    {
        $this->rollback($pembelianId);
        return Pembelian::destroy($pembelianId);
    }

    private function storeDetail($dataDetail, $pembelianId): void
    {
        foreach ($dataDetail as $item) {
            $item = (object) $item;
            PembelianDetail::query()->create([
                'pembelian_id'=>$pembelianId,
                'produk_id'=>$item->produk_id,
                'harga'=>$item->harga,
                'jumlah'=>$item->jumlah,
                'diskon'=>$item->diskon,
                'sub_total'=>$item->sub_total,
            ]);
        }
    }
}
