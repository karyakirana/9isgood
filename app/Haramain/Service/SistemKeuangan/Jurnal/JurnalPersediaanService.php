<?php namespace App\Haramain\Service\SistemKeuangan\Jurnal;

use App\Haramain\Service\SistemKeuangan\Neraca\NeracaSaldoRepository;
use App\Models\Keuangan\Persediaan;
use App\Models\Keuangan\PersediaanTransaksi;

class JurnalPersediaanService
{
    // initiate
    protected $persediaanTransaksi;
    protected $persediaanTransaksiDetail;
    protected $jurnalTransaksi;
    protected $neracaSaldo;

    // persediaan transaksi variabel
    public $persediaanTransaksiId;
    protected $activeCash;
    protected $kode;
    protected $jenis; // masuk atau keluar
    protected $tglInput;
    protected $gudangId;
    protected $kondisi;
    protected $total;
    protected $persediaanType;
    protected $persediaanId;
    protected $field;

    // data detail
    protected $dataDetail;
    protected $dataDetailOut;

    // jurnal transaksi
    protected $akun_persediaan_id;
    protected $akun_hpp_id;

    public function __construct()
    {
        $this->persediaanTransaksi = new PersediaanTransaksi();
        $this->neracaSaldo = new NeracaSaldoRepository();
    }

    public function handleException($data_detail, $kondisi)
    {
        // check semua item keluar
        $count = count($data_detail);
        // jika item lebih dari persediaan maka akan menghasilkan exception
        $a = 0;
        foreach ($data_detail as $item) {
            $a =+ $this->checkItem($item, $kondisi);
        }
        // jika salah satu item tidak ada atau kurang data
        // maka false
        if ($count < $a){
            return false;
        }
        return true;
    }

    public function handleStore($data)
    {
        // set data
        $this->setData($data);
        // store data persediaan_transaksi
        $persediaanTransaksi = $this->store();
        // store data persediaan_transaksi_detail
        // update persediaan perpetual by stock masuk or stock keluar
        $this->persediaanTransaksiDetail = $persediaanTransaksi->persediaan_transaksi_detail();
        $this->storeDetailIn();
        // update jurnal transaksi
        $this->jurnalTransaksi = $persediaanTransaksi->jurnal_transaksi();
    }

    public function handleUpdate($data)
    {
        //
    }

    public function handleDelete($jurnalPersediaanId)
    {
        //
    }

    protected function setData($data)
    {
        $this->activeCash = session('ClosedCash');
        $this->kode = $data['kode'];
        $this->jenis = $data['jenis'];
        $this->tglInput = $data['tgl_input'];
        $this->kondisi = $data['kondisi'];
        $this->gudangId = $data['gudang_id'];
        $this->dataDetail = $data['data_detail'];
        $this->dataDetailOut = $data['data_detail_out'];
    }

    protected function setKode($jenis, $kondisi = 'baik')
    {
        return null;
    }

    protected function store()
    {
        return $this->persediaanTransaksi::query()->create([
            'active_cash'=>$this->activeCash,
            'kode'=>$this->kode,
            'jenis'=>$this->jenis, // masuk atau keluar
            'kondisi'=>$this->kondisi ?? null, // baik atau rusak
            'gudang_id'=>$this->gudangId,
            'persediaan_type'=>null,
            'persediaan_id'=>null,
            'debet'=>null,
            'kredit'=>null,
        ]);
    }

    protected function storeDetailIn()
    {
        foreach ($this->dataDetail as $item) {
            $this->persediaanTransaksiDetail->create([
                'produk_id'=>$item['produk_id'],
                'harga'=>$item['harga'],
                'jumlah'=>$item['jumlah'],
                'sub_total'=>$item['jumlah'],
            ]);
            $this->setDataToPersediaan($item, $this->field);
        }
    }

    protected function storeDetailOut()
    {
        //
    }

    protected function setDataToPersediaan($dataItem, $field)
    {
        // store or update data persediaan
        $persediaan = Persediaan::query()
            ->where('active_cash', $this->activeCash)
            ->where('produk_id', $dataItem['produk_id'])
            ->where('harga', $dataItem['harga']);
        if ($persediaan->exists()){
            // update persediaan
            return $persediaan->increment($field, $dataItem['jumlah']);
        }
        // create persediaan
        return Persediaan::query()->create([
            'active_cash'=>$this->activeCash,
            'tgl_input'=>$this->tglInput,
            'gudang_id'=>$this->gudangId,
            'jenis'=>$this->kondisi,
            'harga'=>$dataItem['harga'],
            $field => $dataItem['jumlah']
        ]);
    }

    /**
     * check item data
     * kepentingan untuk exception stock keluar
     * @param $dataItem
     * @param $kondisi
     * @return int
     */
    protected function checkItem($dataItem, $kondisi)
    {
        $persediaan = Persediaan::query()
            ->where($this->activeCash, session('ClosedCash'))
            ->where('produk_id', $dataItem['produk_id'])
            ->where('jenis', $kondisi);
        if ($persediaan->doesntExist() || $persediaan->sum('stock_saldo') < $dataItem['jumlah']){
            return 0;
        }
        return 1;
    }

    protected function getPersediaanToOut($dataItem, $field)
    {
        // get data by produk_id
        $persediaan = Persediaan::query()
            ->where('active_cash', $this->activeCash)
            ->where('produk_id', $dataItem['produk_id']);
        $persediaanSum = $persediaan->sum('jumlah');
        $persediaanCount = $persediaan->count();
        // get persediaan
        $persediaanGet = $persediaan->oldest('tgl_input')->get();
        // loop persediaan
        $setData = [];
        $jumlahProduk = $dataItem['jumlah'];
        for ($count = 0; $count < $persediaanCount; $count++){
            $jumlahField = $persediaanGet[$count]->{$field};
            $hargaField = $persediaanGet[$count]->harga;
            if ($jumlahProduk > $jumlahField){
                // continue
                $setData[] = [
                    'produk_id'=>$dataItem['produk_id'],
                    'jumlah'=>$jumlahField,
                    'harga_persediaan'=>$hargaField
                ];
                continue;
            }
            // break
            $setData[] = [
                'produk_id'=>$dataItem['produk_id'],
                'jumlah'=>$dataItem['jumlah'],
                'harga_persediaan'=>$hargaField
            ];
            break;
        }
        return $setData;
    }

    protected function storeJurnalTransaksiAndNeracaSaldoIn()
    {
        // debet
        $this->jurnalTransaksi->create();
        $this->neracaSaldo->rollbackDebet($this->akun_hpp_id, $this->total);
        // kredit
        $this->jurnalTransaksi->create();
        $this->neracaSaldo->rollbackKredit($this->akun_persediaan_id, $this->total);
    }

    protected function storeJurnalTransaksiAndNeracaSaldoOut()
    {
        // debet
        $this->jurnalTransaksi->create();
        $this->neracaSaldo->rollbackKredit($this->akun_persediaan_id, $this->total);
        // kredit
        $this->jurnalTransaksi->create();
        $this->neracaSaldo->rollbackDebet($this->akun_hpp_id, $this->total);
    }
}
