<?php namespace App\Haramain\Repository\Stock;

use App\Models\Stock\StockMasuk;
use App\Models\Stock\StockMasukDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StockMasukRepo
{
    protected $stockMasuk;
    protected $stockMasukDetail;
    protected $stockInventoryRepo;

    public function __construct()
    {
        $this->stockMasuk = new StockMasuk();
        $this->stockMasukDetail = new StockMasukDetail();
        $this->stockInventoryRepo = new StockInventoryRepo();
    }

    /**
     * mengambil atau men-generate kode sesuai dengan active_cash dan kondisi
     * @param $kondisi
     * @return string
     */
    public function getKode($kondisi)
    {
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

    /**
     * menyimpan data stock masuk
     * menyimpan data stock masuk detail
     * menyimpan data stock inventory
     * @param $data
     * @param $stockableType
     * @param $stockableId
     * @return Builder|Model
     */
    public function store($data, $stockableType, $stockableId)
    {
        // store stock masuk and return object as create
        $tglMasuk = (isset($data['tglMasuk'])) ? tanggalan_database_format($data['tglMasuk'], 'd-M-Y') : null;
        if (isset($data['jenisMutasi'])){
            $kondisi = \Str::of($data['jenisMutasi'])->before('_');
        } else {
            $kondisi = $data['kondisi'];
        }
        $stockMasuk = $this->stockMasuk->newQuery()
            ->create([
                'kode'=>$this->getKode($kondisi),
                'active_cash'=>session('ClosedCash'),
                'stockable_masuk_id'=>$stockableId,
                'stockable_masuk_type'=>$stockableType,
                'kondisi'=>$kondisi,
                'gudang_id'=>$data['gudangId'] ?? $data['gudangTujuanId'],
                'supplier_id'=>$data['supplierId'],
                'tgl_masuk'=>$tglMasuk,
                'user_id'=>\Auth::id(),
                'nomor_po'=>$data['nomorPo'] ?? '-',
                'nomor_surat_jalan'=>$data['suratJalan'] ?? '-',
                'keterangan'=>$data['keterangan'],
            ]);
        $stockMasukId = $stockMasuk->id;
        $this->storeDetail($data['dataDetail'], $stockMasukId, $data['gudangId'], $data['kondisi']);
        return $stockMasuk;
    }

    /**
     * asumsi sebelumnya sudah di rollback dahulu
     * update data sesuai dengan stock masuk
     * menyimpan stock masuk detail
     * menyimpan stock inventory
     * @param $data
     * @param $stockableType
     * @param $stockableId
     * @return Builder|Model|object|null
     */
    public function update($data, $stockableType, $stockableId)
    {
        // update stock masuk
        $tglMasuk = (isset($data['tglMasuk'])) ? tanggalan_database_format($data['tglMasuk'], 'd-M-Y') : null;
        if (isset($data['jenisMutasi'])){
            $kondisi = \Str::of($data['jenisMutasi'])->before('_');
        } else {
            $kondisi = $data['kondisi'];
        }
        $stockMasuk = $this->stockMasuk->newQuery()
            ->where('stockable_masuk_type', $stockableType)
            ->where('stockable_masuk_id', $stockableId)->first();
        $stockMasukUpdate = $stockMasuk->update([
            'kondisi'=>$kondisi,
            'gudang_id'=>$data['gudangId'] ?? $data['gudangTujuanId'],
            'supplier_id'=>$data['supplierId'],
            'tgl_masuk'=>$tglMasuk,
            'user_id'=>\Auth::id(),
            'nomor_po'=>$data['nomorPo'] ?? '-',
            'nomor_surat_jalan'=>$data['suratJalan'] ?? '-',
            'keterangan'=>$data['keterangan'],
        ]);
        $stockMasukId = $stockMasuk->id;
        $this->storeDetail($data['dataDetail'], $stockMasukId, $data['gudangId'], $data['kondisi']);
        return $stockMasuk;
    }

    /**
     * menghapus data stock masuk
     * me-rollback persediaan
     * menghapus stock masuk detail
     * @param $stockableType
     * @param $stockableId
     * @return bool|mixed|null
     */
    public function destroy($stockableType, $stockableId)
    {
        $stockMasuk = $this->stockMasuk->newQuery()
            ->where('stockable_masuk_type', $stockableType)
            ->where('stockable_masuk_id', $stockableId)->first();
        $stockMasukDetail = $this->stockMasukDetail->newQuery()->where('stock_masuk_id', $stockableId);
        foreach ($stockMasukDetail->get() as $item) {
            $this->stockInventoryRepo->rollback($item, $stockMasuk->gudang_id, $stockMasuk->kondisi, 'stock_keluar');
        }
        $stockMasukDetail->delete();
        return $stockMasuk->delete();
    }

    /**
     * rollback stock inventory
     * menghapus stock masuk detail
     * @param $stockableType
     * @param $stockableId
     * @return mixed
     */
    public function rollback($stockableType, $stockableId)
    {
        $stockMasuk = $this->stockMasuk->newQuery()
            ->where('stockable_masuk_type', $stockableType)
            ->where('stockable_masuk_id', $stockableId)->first();
        $stockMasukDetail = $this->stockMasukDetail->newQuery()->where('stock_masuk_id', $stockableId);
        foreach ($stockMasukDetail->get() as $item) {
            $this->stockInventoryRepo->rollback($item, $stockMasuk->gudang_id, $stockMasuk->kondisi, 'stock_keluar');
        }
        return $stockMasukDetail->delete();
    }

    protected function storeDetail($dataDetail, $stockMasukId, $gudangId, $kondisi)
    {
        foreach ($dataDetail as $item) {
            // store stock masuk detail
            $this->stockMasukDetail->newQuery()->create([
                'stock_masuk_id'=>$stockMasukId,
                'produk_id'=>$item['produk_id'],
                'jumlah'=>$item['jumlah'],
            ]);
            // store stock inventory
            $this->stockInventoryRepo->incrementArrayData($item, $gudangId, $kondisi, 'stock_masuk');
        }
    }
}
