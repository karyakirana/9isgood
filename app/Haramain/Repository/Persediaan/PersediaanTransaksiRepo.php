<?php namespace App\Haramain\Repository\Persediaan;

use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Keuangan\PersediaanTransaksiDetail;

class PersediaanTransaksiRepo
{
    protected $persediaanTransaksi;
    protected $persediaanTransaksiDetail;
    protected $persediaanRepository;

    public function __construct()
    {
        $this->persediaanTransaksi = new PersediaanTransaksi();
        $this->persediaanTransaksiDetail = new PersediaanTransaksiDetail();
        $this->persediaanRepository = new PersediaanRepository();
    }

    public function kode()
    {
        $query = $this->persediaanTransaksi::query()
            ->where('active_cash', session('ClosedCash'));

        if ($query->doesntExist()){
            return '0001/PD/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PD/".date('Y');
    }

    public function storeIn($data, $persediaanableType, $persediaanableId)
    {
        $persediaanTransaksi = $this->persediaanTransaksi->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode(),
                'jenis'=>$data['jenisPersediaan'], // masuk atau keluar
                'tgl_input'=>tanggalan_database_format($data['tglInput'], 'd-M-Y'),
                'kondisi'=>$data['kondisi'], // baik atau rusak
                'gudang_id'=>$data['gudangId'],
                'persediaan_type'=>$persediaanableType,
                'persediaan_id'=>$persediaanableId,
            ]);
        $this->storeDetailIn($data, $persediaanTransaksi->id);
        return $persediaanTransaksi;
    }

    public function rollbackStoreIn($persediaanableType, $persediaanableId)
    {
        // initiate
        $persediaanTransaksi = $this->persediaanTransaksi->newQuery()
            ->where('persediaan_type', $persediaanableType)
            ->where('persediaan_id', $persediaanableId)
            ->first();
        $persediaanTransaksiDetail = $this->persediaanTransaksiDetail->newQuery()->where('persediaan_transaksi_id', $persediaanTransaksi->id);
        // rollback persediaan
        foreach ($persediaanTransaksiDetail->get() as $item) {
            $this->persediaanRepository->rollbackIn($persediaanTransaksi->gudang_id, $persediaanTransaksi->kondisi, $persediaanTransaksi->tgl_input, $item);
        }
        return $persediaanTransaksi;
    }

    public function updateIn($data, $persediaanableType, $persediaanableId)
    {
        // inititate
        $persediaanTransaksi = $this->persediaanTransaksi->newQuery()
            ->where('persediaan_type', $persediaanableType)
            ->where('persediaan_id', $persediaanableId)
            ->first();
        // update
        $persediaanTransaksi->update([
            'tgl_input'=>$data['tglInput'],
            'kondisi'=>$data['kondisi'], // baik atau rusak
            'gudang_id'=>$data['gudangId'],
        ]);
        $this->storeDetailIn($data, $persediaanTransaksi->id);
        return $persediaanTransaksi;
    }

    protected function storeDetailIn($data, $persediaanId)
    {
        foreach ($data['dataDetail'] as $item) {
            $this->persediaanTransaksiDetail->newQuery()->create([
                'persediaan_transaksi_id'=>$persediaanId,
                'produk_id'=>$item['jumlah'],
                'harga'=>$item['harga'],
                'jumlah'=>$item['jumlah'],
                'sub_total'=>$item['sub_total'],
            ]);
            // store persediaan
            $this->persediaanRepository->storeIn($data['gudangId'], $data['kondisi'], tanggalan_database_format($data['tglInput'], 'd-M-Y'), $item);
        }
    }
}
