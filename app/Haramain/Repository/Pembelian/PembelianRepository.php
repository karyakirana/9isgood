<?php namespace App\Haramain\Repository\Pembelian;

use App\Haramain\Repository\JurnalTransaksi\JurnalPembelianTrait;
use App\Haramain\Repository\Persediaan\PersediaanJenisMasukRepo;
use App\Haramain\Repository\PersediaanPerpetualRepo;
use App\Haramain\Repository\StockInventoryRepository;
use App\Models\Keuangan\HutangPembelian;
use App\Models\Purchase\PembelianDetail;
use App\Haramain\Repository\StockMasuk\{StockMasukRepoTrait};
use App\Haramain\Repository\TransaksiRepositoryInterface;
use App\Models\Purchase\Pembelian;

class PembelianRepository
{
    // initiate
    protected $pembelian;
    protected $pembelianDetail;

    public function __construct()
    {
        $this->pembelian = new Pembelian();
        $this->pembelianDetail = new PembelianDetail();
    }

    public function kode($jenisPembelian)
    {
        $query = $this->pembelian->newQuery()
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

    public function getData($pembelianId)
    {
        return $this->pembelian->newQuery()->find($pembelianId);
    }

    public function store($data)
    {
        $pembelian = $this->pembelian->newQuery()
            ->create([
                'kode'=>$this->kode($data['jenis']),
                'nomor_nota'=>$data['nomorNota'],
                'nomor_surat_jalan'=>$data['suratJalan'],
                'jenis'=>$data['jenis'],
                'active_cash'=>session('ClosedCash'),
                'supplier_id'=>$data['supplierId'],
                'gudang_id'=>$data['gudangId'],
                'user_id'=>auth()->id(),
                'tgl_nota'=>tanggalan_database_format($data['tglNota'], 'd-M-Y'),
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
        $this->storeDetail($data['dataDetail'], $pembelian->id);
        return $pembelian;
    }

    public function update($data)
    {
        $pembelian = $this->pembelian->newQuery()->find($data['pembelianId']);
        $pembelianUpdate = $pembelian->update([
            'nomor_nota'=>$data['nomorNota'],
            'nomor_surat_jalan'=>$data['suratJalan'],
            'active_cash'=>session('ClosedCash'),
            'supplier_id'=>$data['supplierId'],
            'gudang_id'=>$data['gudangId'],
            'user_id'=>auth()->id(),
            'tgl_nota'=>tanggalan_database_format($data['tglNota'], 'd-M-Y'),
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
        $this->storeDetail($data['dataDetail'], $pembelian->id);
        return $pembelian;
    }

    public function destroy($pembelianId)
    {
        $this->rollback($pembelianId);
        $pembelian = $this->pembelian::destroy($pembelianId);
    }

    public function rollback($pembelianId)
    {
        return $this->pembelianDetail->newQuery()->where('pembelian_id', $pembelianId)->delete();
    }

    protected function storeDetail($dataDetail, $pembelianId)
    {
        foreach ($dataDetail as $item) {
            $this->pembelianDetail->newQuery()
                ->create([
                    'pembelian_id'=>$pembelianId,
                    'produk_id'=>$item['produk_id'],
                    'harga'=>$item['harga'],
                    'jumlah'=>$item['jumlah'],
                    'diskon'=>$item['diskon'],
                    'sub_total'=>$item['sub_total'],
                ]);
        }
    }
}
