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

    protected function create($data, $persediaanableType, $persediaanableId)
    {
        return $this->persediaanTransaksi->newQuery()
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
    }

    public function storeIn($data, $persediaanableType, $persediaanableId)
    {
        $persediaanTransaksi = $this->create($data, $persediaanableType, $persediaanableId);
        $this->storeDetailIn($data, $persediaanTransaksi->id);
        return $persediaanTransaksi;
    }

    protected function storeDetailIn($data, $persediaanTransaksiId)
    {
        foreach ($data['dataDetail'] as $item) {
            // store persediaan
            $persediaan = $this->persediaanRepository->storeIn($data['gudangId'], $data['kondisi'], tanggalan_database_format($data['tglInput'], 'd-M-Y'), $item);
            // store persediaan_detail
            $this->persediaanTransaksiDetail->newQuery()->create([
                'persediaan_transaksi_id'=>$persediaanTransaksiId,
                'persediaan_id'=>$persediaan->id,
                'produk_id'=>$item['jumlah'],
                'harga'=>$item['harga'],
                'jumlah'=>$item['jumlah'],
                'sub_total'=>$item['sub_total'],
            ]);
        }
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
            $this->persediaanRepository->rollbackIn($item->persediaan_id, $item->jumlah);
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
            'tgl_input'=>tanggalan_database_format($data['tglInput'], 'd-M-Y'),
            'kondisi'=>$data['kondisi'], // baik atau rusak
            'gudang_id'=>$data['gudangId'],
        ]);
        $this->storeDetailIn($data, $persediaanTransaksi->id);
        return $persediaanTransaksi;
    }

    public function storeOut($data, $persediaanableType, $persediaanableId)
    {
        $persediaanTransaksi = $this->create($data, $persediaanableType, $persediaanableId);
        $persediaanTransaksiDetail = $this->storeDetailOut($data, $persediaanTransaksi->id);
        return [
            'persediaanTransaksi'=>$persediaanTransaksi,
            'totalPersediaanKeluar'=>$persediaanTransaksiDetail
        ];
    }

    public function updateOut($data, $persediaanableType, $persediaanableId)
    {
        // initiate
        $persediaanTransaksi = $this->persediaanTransaksi->newQuery()
            ->where('persediaan_type', $persediaanableType)
            ->where('persediaan_id', $persediaanableId)
            ->first();
        // update
        $persediaanTransaksi->update([
            'tgl_input'=>tanggalan_database_format($data['tglInput'], 'd-M-Y'),
            'kondisi'=>$data['kondisi'], // baik atau rusak
            'gudang_id'=>$data['gudangId'],
        ]);
        $persediaanTransaksiDetail = $this->storeDetailOut($data, $persediaanTransaksi->id);
        return [
            'persediaanTransaksi'=>$persediaanTransaksi,
            'totalPersediaanKeluar'=>$persediaanTransaksiDetail
        ];
    }

    protected function storeDetailOut($data, $persediaanTransaksiId)
    {
        $totalPersediaanKeluar = 0;
        foreach ($data['dataDetail'] as $item) {

            $getStockOut = $this->persediaanRepository->getStockOut($data['gudangId'], $data['kondisi'], $item);
            // dd($getStockOut);

            foreach ($getStockOut as $row){
                // dd($row['persediaan_id']);
                $this->persediaanTransaksiDetail->newQuery()->create([
                    'persediaan_transaksi_id'=>$persediaanTransaksiId,
                    'persediaan_id'=>$row['persediaan_id'],
                    'produk_id'=>$row['jumlah'],
                    'harga'=>$row['harga'],
                    'jumlah'=>$row['jumlah'],
                    'sub_total'=>$row['sub_total'],
                ]);
                $totalPersediaanKeluar += $row['sub_total'];

                // store persediaan
                $this->persediaanRepository->storeOut($row['persediaan_id'], $row['jumlah']);
            }
        }
        return $totalPersediaanKeluar;
    }

    public function rollbackStoreOut($persediaanableType, $persediaanableId)
    {
        // initiate
        $persediaanTransaksi = $this->persediaanTransaksi->newQuery()
            ->where('persediaan_type', $persediaanableType)
            ->where('persediaan_id', $persediaanableId)
            ->first();
        $persediaanTransaksiDetail = $this->persediaanTransaksiDetail->newQuery()->where('persediaan_transaksi_id', $persediaanTransaksi->id);
        // rollback persediaan
        foreach ($persediaanTransaksiDetail->get() as $item) {
            $this->persediaanRepository->rollbackOut($item->persediaan_id, $item->jumlah);
        }
        $persediaanTransaksiDetail->delete();
        return $persediaanTransaksi;
    }

    public function destroy($persediaanableType, $persediaanableId)
    {
        $persediaanTransaksi = $this->persediaanTransaksi->newQuery()
            ->where('persediaan_type', $persediaanableType)
            ->where('persediaan_id', $persediaanableId)
            ->first();
        // check
        if ($persediaanTransaksi->jenis == 'masuk'){
            // rollback persediaan transaksi in
            $this->rollbackStoreIn($persediaanableType, $persediaanableId);
        } else {
            // rollback persediaan transaksi out
            $this->rollbackStoreOut($persediaanableType, $persediaanableId);
        }
        return $persediaanTransaksi->delete();
    }
}
