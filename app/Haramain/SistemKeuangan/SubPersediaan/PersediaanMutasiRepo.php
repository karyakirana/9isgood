<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Haramain\SistemKeuangan\SubPersediaan\Transaksi\PersediaanTransaksiRepository;
use App\Models\Keuangan\PersediaanMutasi;
use App\Models\Stock\StockMutasi;
use Str;

class PersediaanMutasiRepo
{
    protected $stockMutasiId;
    protected $jenisMutasi;
    protected $gudangAsalId;
    protected $gudangTujuanId;
    protected $totalBarang;
    protected $totalHarga;

    protected $tglInput;

    protected $dataDetail;

    protected $persediaanTransaksiDebet;
    protected $persediaanTransaksiKredit;

    public function __construct(StockMutasi $stockMutasi)
    {
        $this->stockMutasiId = $stockMutasi->id;
        $this->jenisMutasi = $stockMutasi->jenis_mutasi;
        $this->gudangAsalId = $stockMutasi->gudang_asal_id;
        $this->gudangTujuanId = $stockMutasi->gudang_tujuan_id;
        $this->totalBarang = 0;
        $this->totalHarga = 0;

        $this->tglInput = $stockMutasi->tgl_mutasi;

        $this->dataDetail = $stockMutasi->stockMutasiDetail;
    }

    public static function build($stockMutasi)
    {
        return new static($stockMutasi);
    }

    public function getDataById()
    {
        return PersediaanMutasi::query()
            ->where('stock_mutasi_id', $this->stockMutasiId)
            ->first();
    }

    public function store()
    {
        $detail = $this->storeDetail();
        $persediaanMutasi = PersediaanMutasi::query()
            ->create([
                'stock_mutasi_id'=>$this->stockMutasiId,
                'jenis_mutasi'=>$this->jenisMutasi,
                'gudang_asal_id'=>$this->gudangAsalId,
                'gudang_tujuan_id'=>$this->gudangTujuanId,
                'total_barang'=>$this->totalBarang,
                'total_harga'=>$this->totalHarga,
            ]);
        $persediaanMutasi->persediaanMutasiDetail()->createMany($detail['detailMutasi']);
        // TODO : persediaan transaksi keluar
        $persediaanTransaksiKeluar = $persediaanMutasi->persediaan_transaksi()->create([
            'active_cash'=>session('ClosedCash'),
            'kode'=>PersediaanTransaksiRepository::getKode(),
            'jenis'=>'keluar', // masuk atau keluar
            'tgl_input'=>$this->tglInput,
            'kondisi'=> Str::before($this->jenisMutasi, '_'), // baik atau rusak
            'gudang_id'=>$this->gudangAsalId,
            'kredit'=>$this->persediaanTransaksiKredit,
        ]);
        $persediaanTransaksiKeluar->persediaan_transaksi_detail()->createMany($detail['detailKeluar']);
        // TODO : persediaan transaksi masuk
        $persediaanTransaksiMasuk = $persediaanMutasi->persediaan_transaksi()->create([
            'active_cash'=>session('ClosedCash'),
            'kode'=>PersediaanTransaksiRepository::getKode(),
            'jenis'=>'masuk', // masuk atau keluar
            'tgl_input'=>$this->tglInput,
            'kondisi'=> Str::after($this->jenisMutasi, '_'), // baik atau rusak
            'gudang_id'=>$this->gudangTujuanId,
            'debet'=>$this->persediaanTransaksiDebet,
        ]);
        $persediaanTransaksiMasuk->persediaan_transaksi_detail()->createMany($detail['detailMasuk']);
        return $persediaanMutasi;
    }

    public function update()
    {
        $detail = $this->storeDetail();
        $persediaanMutasi = $this->getDataById();
        $persediaanMutasi->update([
            'jenis_mutasi'=>$this->jenisMutasi,
            'gudang_asal_id'=>$this->gudangAsalId,
            'gudang_tujuan_id'=>$this->gudangTujuanId,
            'total_barang'=>$this->totalBarang,
            'total_harga'=>$this->totalHarga,
        ]);
        $persediaanMutasi->persediaanMutasiDetail()->createMany($detail['detailMutasi']);
        // TODO Persediaan Transaksi Keluar
        $persediaanTransaksiKeluar = $persediaanMutasi->persediaan_transaksi()->firstWhere('jenis', 'keluar');
        $persediaanTransaksiKeluar->update([
            'tgl_input'=>$this->tglInput,
            'kondisi'=> Str::before($this->jenisMutasi, '_'), // baik atau rusak
            'gudang_id'=>$this->gudangAsalId,
            'kredit'=>$this->persediaanTransaksiKredit,
        ]);
        $persediaanTransaksiKeluar->persediaan_transaksi_detail()->createMany($detail['detailKeluar']);
        // TODO Persediaan Transaksi Masuk
        $persediaanTransaksiMasuk = $persediaanMutasi->persediaan_transaksi()->firstWhere('jenis', 'masuk');
        $persediaanTransaksiMasuk->update([
            'tgl_input'=>$this->tglInput,
            'kondisi'=> Str::after($this->jenisMutasi, '_'), // baik atau rusak
            'gudang_id'=>$this->gudangTujuanId,
            'debet'=>$this->persediaanTransaksiDebet,
        ]);
        $persediaanTransaksiMasuk->persediaan_transaksi_detail()->createMany($detail['detailMasuk']);
        return $persediaanMutasi->refresh();
    }

    protected function storeDetail()
    {
        $detailMutasi = [];
        $detailKeluar = [];
        $detailMasuk = [];
        $kondisiKeluar = Str::before($this->jenisMutasi, '_');
        $kondisiMasuk = Str::after($this->jenisMutasi, '_');
        foreach ($this->dataDetail as $item) {
            // TODO get data from persediaan
            $getPersediaan = PersediaanKeluar::set($kondisiKeluar, $this->gudangAsalId, $item->produk_id, $item->jumlah)->getData();
            foreach ($getPersediaan as $value) {
                // TODO store Mutasi
                $detailMutasi[] = [
                    'produk_id'=>$value['produk_id'],
                    'harga'=>$value['harga'],
                    'jumlah'=>$value['jumlah'],
                    'sub_total'=>$value['sub_total']
                ];
                // TODO store Persediaan Keluar
                $detailKeluar[] = [
                    'persediaan_id'=>$value['persediaan_id'],
                    'produk_id'=>$value['produk_id'],
                    'harga'=>$value['harga'],
                    'jumlah'=>$value['jumlah'],
                    'sub_total'=>$value['sub_total'],
                ];
                // TODO update data to persediaan
                $setPersediaan = PersediaanMasuk::set(
                    $this->gudangTujuanId,
                    $kondisiMasuk,
                    $this->tglInput,
                    $value['produk_id'],
                    $value['harga'] ,
                    $value['jumlah']
                )->update();
                // TODO store for Persediaan Masuk
                $detailMasuk[] = [
                    'persediaan_id'=>$setPersediaan->id,
                    'produk_id'=>$setPersediaan->produk_id,
                    'harga'=>$setPersediaan->harga,
                    'jumlah'=>$value['jumlah'],
                    'sub_total'=>$value['jumlah'] * $setPersediaan->harga,
                ];
            }
        }
        $this->totalBarang = array_sum(array_column($detailMutasi, 'jumlah'));
        $this->totalHarga = array_sum(array_column($detailMutasi, 'sub_total'));
        $this->persediaanTransaksiKredit = array_sum(array_column($detailKeluar, 'sub_total'));
        $this->persediaanTransaksiDebet = array_sum(array_column($detailMasuk, 'sub_total'));
        return [
            'detailMutasi'=>$detailMutasi,
            'detailKeluar'=>$detailKeluar,
            'detailMasuk'=>$detailMasuk
        ];
    }

    public function rollback()
    {
        $persediaanMutasi = $this->getDataById();
        // todo rollback persediaan keluar and delete detail transaksi keluar
        $persediaanTransaksiKeluar = $persediaanMutasi->persediaan_transaksi()->firstWhere('jenis', 'keluar');
        $persediaanTransaksiKeluarDetail = $persediaanTransaksiKeluar->persediaan_transaksi_detail;
        foreach ($persediaanTransaksiKeluarDetail as $item) {
            // todo rollback persediaan keluar
            PersediaanRollback::set($item->persediaan_id, $item->jumlah)->rollbackStockKeluar();
        }
        $persediaanTransaksiKeluar->persediaan_transaksi_detail()->delete();
        // todo rollback persediaan masuk and delete detail transaksi masuk
        $persediaanTransaksiMasuk = $persediaanMutasi->persediaan_transaksi()->firstWhere('jenis', 'masuk');
        $persediaanTransaksiMasukDetail = $persediaanTransaksiMasuk->persediaan_transaksi_detail;
        foreach ($persediaanTransaksiMasukDetail as $item) {
            // todo rollback persediaan masuk
            PersediaanRollback::set($item->persediaan_id, $item->jumlah)->rollbackStockMasuk();
        }
        $persediaanTransaksiMasuk->persediaan_transaksi_detail()->delete();
        // todo delete mutasi detail
        $persediaanMutasi->persediaanMutasiDetail()->delete();
    }

    public function destroy()
    {
        $persediaanMutasi = $this->getDataById();
        $this->rollback();
        // todo delete persediaan transaksi
        $persediaanMutasi->persediaan_transaksi()->delete();
        // todo delete mutasi
        return $persediaanMutasi->delete();
    }
}
