<?php namespace App\Haramain\Repository\Persediaan;

class PersediaanTransaksiMutasiRepo extends PersediaanTransaksiRepo
{
    public function getStockOut($data)
    {
        $returnData = [];
        foreach ($data['dataItem'] as $item) {
            $returnData[] = $this->persediaanRepository->getStockOut($data['gudangId'], $data['kondisi'], $item);
        }
        return $returnData;
    }

    public function storeMutasiOut($data, $dataPersediaanOut, $persediaanableType, $persediaanableId)
    {
        $kondisi = \Str::of($data['jenisMutasi'])->before('_');
        $persediaanTransaksi = $this->persediaanTransaksi->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode(),
                'jenis'=>'keluar', // masuk atau keluar
                'tgl_input'=>tanggalan_database_format($data['tglInput'], 'd-M-Y'),
                'kondisi'=>$kondisi, // baik atau rusak
                'gudang_id'=>$data['gudangAsalId'],
                'persediaan_type'=>$persediaanableType,
                'persediaan_id'=>$persediaanableId,
            ]);
        $totalPersediaanKeluar = 0;
        foreach ($dataPersediaanOut as $row){
            // dd($row['persediaan_id']);
            $this->persediaanTransaksiDetail->newQuery()->create([
                'persediaan_transaksi_id'=>$persediaanTransaksi->id,
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
        return $persediaanTransaksi;
    }

    public function storeMutasiIn($data, $dataDetailMutasiIn,  $persediaanableType, $persediaanableId)
    {
        $kondisi = \Str::of($data['jenisMutasi'])->after('_');
        $persediaanTransaksi = $this->persediaanTransaksi->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode(),
                'jenis'=>'masuk', // masuk atau keluar
                'tgl_input'=>tanggalan_database_format($data['tglInput'], 'd-M-Y'),
                'kondisi'=>$kondisi, // baik atau rusak
                'gudang_id'=>$data['gudangTujuanId'],
                'persediaan_type'=>$persediaanableType,
                'persediaan_id'=>$persediaanableId,
            ]);
        $this->storeMutasiDetailIn($data, $dataDetailMutasiIn, $persediaanTransaksi->id);
        return $persediaanTransaksi;
    }

    protected function storeMutasiDetailIn($data, $dataDetailMutasiIn, $persediaanTransaksiId)
    {
        foreach ($dataDetailMutasiIn as $item) {
            $this->storeItem($data, $item, $persediaanTransaksiId);
        }
    }
}
