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

    public function storeTransaksiMasuk($data, $persediaanableType, $persediaanableId)
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
        $this->storeDetailMasuk($data->dataDetail, $persediaanTransaksi->id, $persediaanTransaksi->gudang_id, $persediaanTransaksi->kondisi, $persediaanTransaksi->tgl_input);
        return $persediaanTransaksi;
    }

    public function updateTransaksiMasuk($data, $persediaanableType, $persediaanableId)
    {
        $data = (object) $data;
        $persediaanTransaksi = $this->getByPersediaanMasukLine($persediaanableType, $persediaanableId);
        $persediaanTransaksi->update([
            'jenis'=>'masuk', // masuk atau keluar
            'tgl_input'=>tanggalan_database_format($data->tglInput, 'd-M-Y'),
            'kondisi'=>$data->kondisi, // baik atau rusak
            'gudang_id'=>$data->gudangId,
            'persediaan_type'=>$persediaanableType,
            'persediaan_id'=>$persediaanableId,
        ]);
        $this->storeDetailMasuk($data->dataDetail, $persediaanTransaksi->id, $persediaanTransaksi->gudang_id, $persediaanTransaksi->kondisi, $persediaanTransaksi->tgl_input);
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

    public function storeTransaksiKeluar($data, $persediaanableType, $persediaanableId)
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
        $persediaanKeluar = $this->storeDetailKeluar($data->dataDetail, $persediaanTransaksi->id, $persediaanTransaksi->gudang_id, $persediaanTransaksi->kondisi, $persediaanTransaksi->tgl_input);
        return (object)[
            'persediaanTransaksi'=>$persediaanTransaksi,
            'totalPersediaanKeluar'=>$persediaanKeluar
        ];
    }

    public function updateTransaksiKeluar($data, $persediaanableType, $persediaanableId)
    {
        $data = (object) $data;
        $persediaanTransaksi = $this->getByPersediaanKeluarLine($persediaanableType, $persediaanableId);
        $persediaanTransaksi->update([
            'jenis'=>'keluar', // masuk atau keluar
            'tgl_input'=>tanggalan_database_format($data->tglInput, 'd-M-Y'),
            'kondisi'=>$data->kondisi, // baik atau rusak
            'gudang_id'=>$data->gudangId,
        ]);
        $persediaanKeluar = $this->storeDetailKeluar($data->dataDetail, $persediaanTransaksi->id, $persediaanTransaksi->gudang_id, $persediaanTransaksi->kondisi, $persediaanTransaksi->tgl_input);
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
                // dd($row['persediaan_id']);
                $row = (object) $row;
                PersediaanTransaksiDetail::query()->create([
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
        foreach ($persediaanTransaksiDetail as $item) {
            $this->persediaanRepository->rollbackIn($item->persediaan_id, $item->jumlah);
        }
    }

    public function rollbackKeluar($persediaanableType, $persediaanableId)
    {
        $persediaanTransaksi = $this->getByPersediaanKeluarLine($persediaanableType, $persediaanableId);
        $persediaanTransaksiDetail = PersediaanTransaksiDetail::query()->where('persediaan_transaksi_id', $persediaanTransaksi->id);
        foreach ($persediaanTransaksiDetail as $item) {
            $this->persediaanRepository->rollbackIn($item->persediaan_id, $item->jumlah);
        }
    }
}
