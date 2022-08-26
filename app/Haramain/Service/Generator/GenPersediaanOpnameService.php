<?php namespace App\Haramain\Service\Generator;

use App\Models\Keuangan\JurnalTransaksi;
use App\Models\Keuangan\NeracaSaldo;
use App\Models\Keuangan\Persediaan;
use App\Models\Keuangan\PersediaanOpname;
use App\Models\Keuangan\PersediaanOpnameDetail;
use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Keuangan\PersediaanTransaksiDetail;
use App\Models\KonfigurasiJurnal;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GenPersediaanOpnameService
{
    protected $persediaanStockOpname;
    protected $persediaanStockOpnameDetail;
    protected $persediaan;
    protected $persediaanTransaksi;
    protected $persediaanTransaksiDetail;
    protected $jurnalTransaksi;
    protected $neracaSaldo;
    protected $konfigurasiAkun; // model

    // var
    protected $akunPersediaanBaikKalimasId;
    protected $akunPersediaanBaikPerakId;
    protected $akunModalAwal;

    public function __construct()
    {
        $this->persediaanStockOpname = new PersediaanOpname();
        $this->persediaanStockOpnameDetail = new PersediaanOpnameDetail();
        $this->persediaan = new Persediaan();
        $this->persediaanTransaksi = new PersediaanTransaksi();
        $this->persediaanTransaksiDetail = new PersediaanTransaksiDetail();
        $this->jurnalTransaksi = new JurnalTransaksi();
        $this->neracaSaldo = new NeracaSaldo();
        $this->konfigurasiAkun = new KonfigurasiJurnal();
    }

    // handle all
    public function handleGenerateAll()
    {
        \DB::beginTransaction();
        try {
            // set konfigurasi jurnal
            $this->setKonfigurasiJurnal();
            $stockOpnameAll = $this->getStockOpname();
            foreach ($stockOpnameAll as $item) {
                // store persediaan transaksi
                $persediaanTransaksiStore = $this->storePersediaanTransaksi($item);
                $persediaanTransaksiData = $persediaanTransaksiStore['persediaanTransaksi'];
                $totalHarga = $persediaanTransaksiStore['totalHarga'];
                if ($persediaanTransaksiData->gudang_id == 1){
                    // gudang kalimas
                    $this->storeJurnalTransaksiDebet($item::class, $item->id, $this->akunPersediaanBaikKalimasId, $totalHarga);
                    $this->neracaSaldoIncrement($this->akunPersediaanBaikKalimasId, $totalHarga, 'debet');
                } else {
                    // gudang perak
                    $this->storeJurnalTransaksiDebet($item::class, $item->id, $this->akunPersediaanBaikPerakId, $totalHarga);
                    $this->neracaSaldoIncrement($this->akunPersediaanBaikPerakId, $totalHarga, 'debet');
                }
                $this->storeJurnalTransaksiKredit($item::class, $item->id, $this->akunModalAwal, $totalHarga);
                $this->neracaSaldoIncrement($this->akunModalAwal, $totalHarga, 'kredit');
            }
            \DB::commit();
            return [
                'status'=>true,
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    /**
     * persediaan opname proses
     */
    protected function getStockOpname()
    {
        return $this->persediaanStockOpname->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->get();
    }

    /**
     * persediaan transaksi proses
     */
    protected function storePersediaanTransaksi($data)
    {
        $persediaanTransaksi = $this->persediaanTransaksi->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'kode'=>$data->kode,
                'jenis'=>'masuk', // masuk atau keluar
                'tgl_input'=>$data->created_at,
                'kondisi'=>$data->kondisi, // baik atau rusak
                'gudang_id'=>$data->gudang_id,
                'persediaan_type'=>$data::class,
                'persediaan_id'=>$data->id,
                'debet',
                'kredit',
            ]);
        $totalHarga = 0;
        $dataDetail = $data->persediaan_opname_detail;
        foreach ($dataDetail as $item) {
            $persediaan = $this->storePersediaan($data, $item);
            $this->persediaanTransaksiDetail->newQuery()
                ->create([
                    'persediaan_transaksi_id'=>$persediaanTransaksi->id,
                    'persediaan_id'=>$persediaan->id,
                    'produk_id'=>$item->produk_id,
                    'harga'=>$item->harga,
                    'jumlah'=>$item->jumlah,
                    'sub_total'=>$item->sub_total
                ]);
            $totalHarga += $item->sub_total;
        }
        return [
            'persediaanTransaksi'=>$persediaanTransaksi,
            'totalHarga'=>$totalHarga
        ];
    }

    protected function storePersediaan($data, $dataItem)
    {
        //dd($dataItem);
        $query = $this->persediaan->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $data->kondisi)
            ->where('gudang_id', $data->gudang_id)
            ->where('produk_id', $dataItem->produk_id)
            ->where('harga', $dataItem->harga)
            ->latest('tgl_input');
        if ($query->doesntExist()){
            // create
            return $this->persediaan->newQuery()
                ->create([
                    'active_cash'=>session('ClosedCash'),
                    'jenis'=>$data->kondisi,// baik or buruk
                    'tgl_input'=>$data->created_at,
                    'gudang_id'=>$data->gudang_id,
                    'produk_id'=>$dataItem->produk_id,
                    'harga'=>$dataItem->harga,
                    'stock_opname'=>$dataItem->jumlah,
                    'stock_masuk',
                    'stock_keluar',
                    'saldo'=>$dataItem->jumlah,
                ]);
        }
        $query = $query->first();
        $query->increment('stock_opname', $dataItem->jumlah);
        $query->increment('saldo', $dataItem->jumlah);
        return $query;
    }

    /**
     * Jurnal Transaksi Proses
     */
    protected function setKonfigurasiJurnal()
    {
        $this->akunPersediaanBaikKalimasId = $this->konfigurasiAkun->newQuery()->find('akun_persediaan_awal_kalimas')->akun_id;
        $this->akunPersediaanBaikPerakId = $this->konfigurasiAkun->newQuery()->find('akun_persediaan_awal_perak')->akun_id;
        $this->akunModalAwal = $this->konfigurasiAkun->newQuery()->find('prive_modal_awal')->akun_id;
    }

    protected function storeJurnalTransaksiDebet($jurnalableType, $jurnalableId, $akunDebetId, $nominal)
    {
        return $this->jurnalTransaksi->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jurnal_type'=>$jurnalableType,
                'jurnal_id'=>$jurnalableId,
                'akun_id'=>$akunDebetId,
                'nominal_debet'=>$nominal,
                'nominal_kredit'=>null,
                'keterangan'
            ]);
    }

    protected function storeJurnalTransaksiKredit($jurnalableType, $jurnalableId, $akunKreditId, $nominal)
    {
        return $this->jurnalTransaksi->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jurnal_type'=>$jurnalableType,
                'jurnal_id'=>$jurnalableId,
                'akun_id'=>$akunKreditId,
                'nominal_debet'=>null,
                'nominal_kredit'=>$nominal,
                'keterangan'
            ]);
    }

    protected function neracaSaldoIncrement($akunDebetId, $nominal, $field)
    {
        $query = $this->neracaSaldo->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $akunDebetId);
        if ($query->exists()){
            $query->increment($field, $nominal);
            return $query->first();
        }
        return $this->neracaSaldo->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'akun_id'=>$akunDebetId,
                $field=>$nominal
            ]);
    }
}
