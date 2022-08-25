<?php namespace App\Haramain\Repository\Stock;

use App\Models\Stock\StockKeluar;
use App\Models\Stock\StockKeluarDetail;
use Illuminate\Support\Facades\Auth;

class StockKeluarRepo
{
    protected $stockKeluar;
    protected $stockKeluarDetail;
    protected $stockInventoryRepo;

    public function __construct()
    {
        $this->stockKeluar = new StockKeluar();
        $this->stockKeluarDetail = new StockKeluarDetail();
        $this->stockInventoryRepo = new StockInventoryRepo();
    }

    public function kode($kondisi= 'baik', $jenisMutasi = null)
    {
        if ($jenisMutasi){
            $kondisi = $this->setKondisi($jenisMutasi);
        }

        // query
        $query = $this->stockKeluar::query()
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
        if ($kondisi == 'baik_baik'|| $kondisi == 'baik_rusak'){
            return 'baik';
        }

        return 'rusak';
    }

    public function store($data, $stockableType, $stockableId)
    {
        $tglKeluar = $data['tglKeluar'] ?? $data['tglNota'] ?? $data['tglMutasi'];
        $databaseTglKeluar = tanggalan_database_format($tglKeluar, 'd-M-Y');

        if (isset($data['jenisMutasi'])){
            $kondisi = \Str::of($data['jenisMutasi'])->after('_');
        } else {
            $kondisi = $data['kondisi'];
        }

        $stockKeluar = $this->stockKeluar->newQuery()
            ->create([
                'kode'=>$this->kode($kondisi),
                'supplier_id'=>(isset($data['supplierId'])) ? $data['supplierId'] : null,
                'active_cash'=>session('ClosedCash'),
                'stockable_keluar_id'=>$stockableId,
                'stockable_keluar_type'=>$stockableType,
                'kondisi'=>$kondisi,
                'gudang_id'=>$data['gudangId'],
                'tgl_keluar'=>$databaseTglKeluar,
                'user_id'=>$data['userId'],
                'keterangan'=>$data['keterangan'],
            ]);
        $this->storeDetail($data, $data['dataDetail'], $stockKeluar->id);
        return $stockKeluar;
    }

    public function storeDetail($data, $dataItem, $stockKeluarId)
    {
        foreach ($dataItem as $item) {
            $this->stockKeluarDetail->newQuery()
                ->create([
                    'stock_keluar_id'=>$stockKeluarId,
                    'produk_id'=>$item['produk_id'],
                    'jumlah'=>$item['jumlah'],
                ]);
            // update stock inventory
            $this->stockInventoryRepo->incrementArrayData($item, $data['gudangId'], $data['kondisi'], 'stock_keluar');
        }
    }

    public function update($data, $stockableType, $stockableId)
    {
        // initiate
        $tglKeluar = $data['tglKeluar'] ?? $data['tglNota'] ?? $data['tglMutasi'];
        if (isset($data['jenisMutasi'])){
            $kondisi = \Str::of($data['jenisMutasi'])->after('_');
        } else {
            $kondisi = $data['kondisi'];
        }
        $databaseTglKeluar = tanggalan_database_format($tglKeluar, 'd-M-Y');
        $stockKeluar = $this->stockKeluar->newQuery()
            ->where('stockable_keluar_id', $stockableId)
            ->where('stockable_keluar_type', $stockableType)
            ->first();
        $update = $stockKeluar->update([
            'supplier_id'=>(isset($data['supplierId'])) ? $data['supplierId'] : null,
            'kondisi'=>$data['kondisi'],
            'gudang_id'=>$data['gudangId'],
            'tgl_keluar'=>$databaseTglKeluar,
            'user_id'=>$data['userId'],
            'keterangan'=>$data['keterangan'],
        ]);
        $this->storeDetail($data, $data['dataDetail'], $stockKeluar->id);
        return $stockKeluar;
    }

    public function rollback($stockableType, $stockableId)
    {
        $stockKeluar = $this->stockKeluar->newQuery()
            ->where('stockable_keluar_id', $stockableId)
            ->where('stockable_keluar_type', $stockableType)
            ->first();
        $stockKeluarDetail = $this->stockKeluarDetail->newQuery()
            ->where('stock_keluar_id', $stockKeluar->id);
        // rollback stock inventory
        foreach ($stockKeluarDetail->get() as $item) {
            $this->stockInventoryRepo->rollback($item, $stockKeluar->gudang_id, $stockKeluar->kondisi, 'stock_keluar');
        }
        $stockKeluarDetail->delete();
        return $stockKeluar;
    }

    public function destroy($stockableType, $stockableId)
    {
        $rollback = $this->rollback($stockableType, $stockableId);
        return $rollback->delete();
    }
}
