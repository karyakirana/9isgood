<?php namespace App\Haramain\Repository\Stock;

use App\Haramain\Repository\TransaksiRepositoryInterface;
use App\Models\Stock\StockOpname;
use App\Models\Stock\StockOpnameDetail;

class StockOpnameRepository
{
    protected $stockOpname;
    protected $stockOpnameDetail;

    public function __construct()
    {
        $this->stockOpname = new StockOpname();
        $this->stockOpnameDetail = new StockOpnameDetail();
    }

    public function kode($jenis='baik'): ?string
    {
        $query = StockOpname::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $jenis)
            ->latest('kode');

        $kode = ($jenis == 'baik') ? 'SO' : 'SOR';

        // check last num
        if ($query->doesntExist()){
            return "0001/{$kode}/".date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/{$kode}/".date('Y');
    }

    public function store($data)
    {
        $stockOpname = $this->stockOpname->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode($data['kondisi']),
                'tglInput'=>$data['tglInput'],
                'gudang_id'=>$data['gudangId'],
                'user_id'=>$data['userId'],
                'pegawai_id'=>$data['pegawaiId'],
                'keterangan'=>$data['keterangan']
            ]);
        $this->storeDetail($data['dataDetail'], $stockOpname->id);
        return $stockOpname;
    }

    protected function storeDetail($dataDetail, $stockOpnameId)
    {
        foreach ($dataDetail as $item) {
            $this->stockOpnameDetail->newQuery()
                ->create([
                    'stock_opname_id'=>$stockOpnameId,
                    'produk_id'=>$item['produk_id'],
                    'jumlah'=>$item['jumlah'],
                ]);
        }
    }

    public function update($data)
    {
        $stockOpname = $this->stockOpname->newQuery()->find($data['stockOpnameId']);
        $stockOpname->update([
            'tglInput'=>$data['tglInput'],
            'gudang_id'=>$data['gudangId'],
            'user_id'=>$data['userId'],
            'pegawai_id'=>$data['pegawaiId'],
            'keterangan'=>$data['keterangan']
        ]);
        $this->storeDetail($data['dataDetail'], $stockOpname->id);
        return $stockOpname;
    }

    public function rollback($stockOpnameId)
    {
        $stockOpname = $this->stockOpname->newQuery()->find($stockOpnameId);
        $this->stockOpnameDetail->newQuery()->where('stock_opname_id', $stockOpnameId);
        return $stockOpname;
    }

    public function destroy($stockOpnameId)
    {
        return $this->rollback($stockOpnameId)->delete();
    }
}
