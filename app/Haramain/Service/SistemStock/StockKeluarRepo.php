<?php namespace App\Haramain\Service\SistemStock;

use App\Models\Stock\StockKeluar;

class StockKeluarRepo
{
    protected $stockInventoryRepo;

    public function __construct()
    {
        $this->stockInventoryRepo = new StockInventoryRepo();
    }

    public function createFromMorph($classMorph, $data)
    {
        $tglKeluar = $data['tglKeluar'] ?? $data['tglNota'] ?? $data['tglMutasi'];
        return $classMorph->create([
            'kode'=>$this->kode($data['kondisi'] ?? null, $data['jenisMutasi'] ?? null),
            'supplier_id'=>$data['supplierId'] ?? null,
            'active_cash'=>$data['activeCash'],
            'kondisi'=>$data['kondisi'] ?? 'baik',
            'gudang_id'=>$data['gudangId'] ?? $data['gudangAsalId'],
            'tgl_keluar'=>tanggalan_database_format($tglKeluar, 'd-M-Y'),
            'user_id'=>$data['userId'],
            'keterangan'=>$data['keterangan'],
        ]);
    }

    public function storeDetail($stockKeluarDetail, $dataitem, $gudangKeluarId, $kondisi = 'baik')
    {
        $stockKeluarDetail->create([
            'produk_id'=>$dataitem['produk_id'],
            'jumlah'=>$dataitem['jumlah'],
        ]);
        // update stock inventory
        $this->stockInventoryRepo->store($kondisi, $gudangKeluarId, 'stock_keluar', $dataitem);
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }

    public function rollback()
    {
        //
    }

    protected function kode($kondisi = 'baik', $jenisMutasi = null)
    {
        if ($jenisMutasi){
            $kondisi = $this->setKondisi($jenisMutasi);
        }

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

    protected function setKondisi($kondisi)
    {
        if ($kondisi == 'baik_baik'|| $kondisi == 'baik_rusak'){
            return 'baik';
        }

        return 'rusak';
    }
}
