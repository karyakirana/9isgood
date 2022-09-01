<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Keuangan\PersediaanTransaksiDetail;

class PersediaanTransaksiRepo
{
    protected $persediaanRepository;

    public function __construct()
    {
        $this->persediaanRepository = new PersediaanRepository();
    }

    protected function kode()
    {
        $query = PersediaanTransaksi::query()
            ->where('active_cash', session('ClosedCash'));

        if ($query->doesntExist()){
            return '0001/PD/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PD/".date('Y');
    }

    protected function create($data, $persediaanableType, $persediaanableId)
    {
        $data = (object) $data;
        return PersediaanTransaksi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode(),
                'jenis'=>$data->jenisPersediaan, // masuk atau keluar
                'tgl_input'=>tanggalan_database_format($data->tglInput, 'd-M-Y'),
                'kondisi'=>$data->kondisi, // baik atau rusak
                'gudang_id'=>$data->gudangId,
                'persediaan_type'=>$persediaanableType,
                'persediaan_id'=>$persediaanableId,
            ]);
    }

    public function getByPersediaanMasukLine($persediaanableType, $persediaanableId)
    {
        return PersediaanTransaksi::query()
            ->where('persediaan_type', $persediaanableType)
            ->where('persediaan_id', $persediaanableId)
            ->where('jenis', 'masuk')
            ->first();
    }

    public function storeTransaksiMasuk($data, $persediaanableType, $persediaanableId, $detailStockOut = null)
    {
        $data = (object) $data;
        $persediaanTransaksi = PersediaanTransaksi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode(),
                'jenis'=>'masuk', // masuk atau keluar
                'tgl_input'=>tanggalan_database_format($data->tglInput, 'd-M-Y'),
                'kondisi'=>$data->kondisi, // baik atau rusak
                'gudang_id'=>$data->gudangId,
                'persediaan_type'=>$persediaanableType,
                'persediaan_id'=>$persediaanableId,
            ]);
        $dataDetail = $detailStockOut ?? $data->dataDetail;
        $this->storeDetailMasuk($dataDetail, $persediaanTransaksi->id, $persediaanTransaksi->gudang_id, $persediaanTransaksi->kondisi, $persediaanTransaksi->tgl_input);
        return $persediaanTransaksi;
    }

    public function updateTransaksiMasuk($data, $persediaanableType, $persediaanableId, $detailStockOut = null)
    {
        $data = (object) $data;
        $this->getByPersediaanMasukLine($persediaanableType, $persediaanableId)->update([
            'jenis'=>'masuk', // masuk atau keluar
            'tgl_input'=>tanggalan_database_format($data->tglInput, 'd-M-Y'),
            'kondisi'=>$data->kondisi, // baik atau rusak
            'gudang_id'=>$data->gudangId,
            'persediaan_type'=>$persediaanableType,
            'persediaan_id'=>$persediaanableId,
        ]);
        $persediaanTransaksi = $this->getByPersediaanMasukLine($persediaanableType, $persediaanableId);
        $dataDetail = $detailStockOut ?? $data->dataDetail;
        $this->storeDetailMasuk($dataDetail, $persediaanTransaksi->id, $persediaanTransaksi->gudang_id, $persediaanTransaksi->kondisi, $persediaanTransaksi->tgl_input);
        return $persediaanTransaksi;
    }

    protected function storeDetailMasuk($dataDetail, $persediaanTransaksiId, $gudangId, $kondisi, $tglInput)
    {
        foreach ($dataDetail as $item) {
            $item = (object) $item;
            $persediaan = $this->persediaanRepository->storeIn($gudangId, $kondisi, $tglInput, $item);
            PersediaanTransaksiDetail::query()
                ->create([
                    'persediaan_transaksi_id'=>$persediaanTransaksiId,
                    'persediaan_id'=>$persediaan->id,
                    'produk_id'=>$item->produk_id,
                    'harga'=>$item->harga,
                    'jumlah'=>$item->jumlah,
                    'sub_total'=>$item->sub_total,
                ]);
        }
    }

    public function getByPersediaanKeluarLine($persediaanableType, $persediaanableId)
    {
        return PersediaanTransaksi::query()
            ->where('persediaan_type', $persediaanableType)
            ->where('persediaan_id', $persediaanableId)
            ->where('jenis', 'keluar')
            ->first();
    }

    public function getPersediaanByDetailForOut($dataDetail, $kondisi, $gudangId)
    {
        $returnData = [];
        foreach ($dataDetail as $item) {
            $itemObject = (object) $item;
            $dataFromPersediaan = $this->persediaanRepository->getStockOut($gudangId,$kondisi, $itemObject);
            foreach ($dataFromPersediaan as $persediaan) {
                $returnData[] = $persediaan;
            }
        }
        return $returnData;
    }

    public function storeTransaksiKeluar($data, $detailStockOut, $persediaanableType, $persediaanableId)
    {
        $data = (object) $data;
        $persediaanTransaksi = PersediaanTransaksi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$this->kode(),
                'jenis'=>'keluar', // masuk atau keluar
                'tgl_input'=>tanggalan_database_format($data->tglInput, 'd-M-Y'),
                'kondisi'=>$data->kondisi, // baik atau rusak
                'gudang_id'=>$data->gudangId,
                'persediaan_type'=>$persediaanableType,
                'persediaan_id'=>$persediaanableId,
            ]);
        $persediaanKeluar = $this->storeDetailKeluar($detailStockOut, $persediaanTransaksi->id, $persediaanTransaksi->gudang_id, $persediaanTransaksi->kondisi, $persediaanTransaksi->tgl_input);
        return (object)[
            'persediaanTransaksi'=>$persediaanTransaksi,
            'totalPersediaanKeluar'=>$persediaanKeluar
        ];
    }

    public function updateTransaksiKeluar($data, $detailStockOut, $persediaanableType, $persediaanableId)
    {
        $data = (object) $data;
        $persediaanTransaksi = $this->getByPersediaanKeluarLine($persediaanableType, $persediaanableId);
        $update = $persediaanTransaksi->update([
            'jenis'=>'keluar', // masuk atau keluar
            'tgl_input'=>tanggalan_database_format($data->tglInput, 'd-M-Y'),
            'kondisi'=>$data->kondisi, // baik atau rusak
            'gudang_id'=>$data->gudangId,
        ]);
        $persediaanTransaksi = $this->getByPersediaanKeluarLine($persediaanableType, $persediaanableId);
        $persediaanKeluar = $this->storeDetailKeluar($detailStockOut, $persediaanTransaksi->id, $persediaanTransaksi->gudang_id, $persediaanTransaksi->kondisi, $persediaanTransaksi->tgl_input);
        return (object)[
            'persediaanTransaksi'=>$persediaanTransaksi,
            'totalPersediaanKeluar'=>$persediaanKeluar
        ];
    }

    protected function storeDetailKeluar($dataDetail, $persediaanTransaksiId, $gudangId, $kondisi, $tglInput)
    {
        $totalPersediaanKeluar = 0;
        foreach ($dataDetail as $item) {

            $getStockOut = $this->persediaanRepository->getStockOut($gudangId, $kondisi, $item, $tglInput);
            // dd($getStockOut);

            foreach ($getStockOut as $row){
                $row = (object) $row;
                //dd($persediaanTransaksiId);
                PersediaanTransaksiDetail::query()->insert([
                    'persediaan_transaksi_id'=>$persediaanTransaksiId,
                    'persediaan_id'=>$row->persediaan_id,
                    'produk_id'=>$row->jumlah,
                    'harga'=>$row->harga,
                    'jumlah'=>$row->jumlah,
                    'sub_total'=>$row->sub_total,
                ]);
                $totalPersediaanKeluar += $row->sub_total;

                // store persediaan
                $this->persediaanRepository->storeOut($row->persediaan_id, $row->jumlah);
            }
        }
        return $totalPersediaanKeluar;
    }

    public function rollbackMasuk($persediaanableType, $persediaanableId)
    {
        $persediaanTransaksi = $this->getByPersediaanMasukLine($persediaanableType, $persediaanableId);
        $persediaanTransaksiDetail = PersediaanTransaksiDetail::query()->where('persediaan_transaksi_id', $persediaanTransaksi->id);
        foreach ($persediaanTransaksiDetail->get() as $item) {
            $this->persediaanRepository->rollbackIn($item->persediaan_id, $item->jumlah);
        }
        $persediaanTransaksiDetail->delete();
        return $persediaanTransaksi;
    }

    public function destroyMasuk($persediaanableType, $persediaanableId)
    {
        return $this->rollbackMasuk($persediaanableType, $persediaanableId)->delete();
    }

    public function rollbackKeluar($persediaanableType, $persediaanableId)
    {
        $persediaanTransaksi = $this->getByPersediaanKeluarLine($persediaanableType, $persediaanableId);
        $persediaanTransaksiDetail = PersediaanTransaksiDetail::query()->where('persediaan_transaksi_id', $persediaanTransaksi->id);
        foreach ($persediaanTransaksiDetail->get() as $item) {
            $this->persediaanRepository->rollbackOut($item->persediaan_id, $item->jumlah);
        }
        $persediaanTransaksiDetail->delete();
        return $persediaanTransaksi;
    }

    public function destroyKeluar($persediaanableType, $persediaanableId)
    {
        return $this->rollbackKeluar($persediaanableType, $persediaanableId)->delete();
    }
}
