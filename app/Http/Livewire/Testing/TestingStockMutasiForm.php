<?php

namespace App\Http\Livewire\Testing;

use App\Haramain\Repository\Stock\StockMutasiRepo;
use App\Haramain\Traits\LivewireTraits\SetProdukTraits;
use App\Models\KonfigurasiJurnal;
use App\Models\Master\Gudang;
use App\Models\Master\Produk;
use App\Models\Stock\StockMutasi;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TestingStockMutasiForm extends Component
{
    use SetProdukTraits;

    // first initiate properties
    public $mode = 'create', $update = false;
    public $mutasiId;

    // for mounting
    public $gudangData = [];

    // form detail
    public $data_detail = [], $indexDetail, $index;
    public $idDetail, $idProduk, $namaProduk, $kodeLokalProduk;
    public $jumlahProduk, $gudangAsal, $gudangTujuan;


    // master
    public $kode, $jenisMutasi, $gudangAsalId, $gudangTujuanId;
    public $tglMutasi, $userId, $keterangan;
    public $gudangId, $stockId;

    // var transaksi
    public $persediaanBaikKalimas, $persediaanBaikPerak;

    protected $listeners = [
        'setProduk',
    ];

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->gudangData = Gudang::all();
        $this->tglMutasi = tanggalan_format(now('ASIA/JAKARTA'));
        $this->jenisMutasi = 'baik_baik';

        // initiate akun transaksi
        $this->persediaanBaikKalimas = KonfigurasiJurnal::query()->find('persediaan_baik_kalimas')->akun_id;
        $this->persediaanBaikPerak = KonfigurasiJurnal::query()->find('persediaan_baik_perak')->akun_id;
    }

    public function mount($mutasiId = null){

        if ($mutasiId){
            // load
            $mutasi = StockMutasi::query()->find($mutasiId);
            $this->mutasiId = $mutasi->id;
            $this->tglMutasi = $mutasi->tglMutasi;
            $this->gudangAsalId = $mutasi->gudangAsalId;
            $this->gudangTujuanId = $mutasi->gudangTujuanId;
            $this->keterangan = $mutasi->keterangan;
            foreach ($mutasi->stockMutasiDetail as $item) {
                $this->data_detail [] = [
                    'produk_id'=>$item->produk_id,
                    'kode_lokal'=>$item->produk->kode_lokal,
                    'nama_produk'=>$item->produk->nama,
                    'jumlah'=>$item->jumlahProduk,
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.testing.testing-stock-mutasi-form');
    }

    protected function resetForm()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset([
            'idProduk', 'namaProduk', 'jumlahProduk'
        ]);
    }

    public function setProduk(Produk $produk)
    {
        $this->setProduk_sales($produk);
    }

    public function updatedGudangAsalId()
    {
        $this->emit('setGudang', $this->gudangAsalId);
    }

    public function validatedToTable()
    {
        $this->validate([
            'idProduk'=>'required',
            'jumlahProduk'=>'required'
        ]);
    }

    public function addLine()
    {
        $this->validatedToTable();

        $this->data_detail [] = [
            'produk_id'=>$this->idProduk,
            'kode_lokal'=>$this->kodeLokalProduk,
            'nama_produk'=>$this->namaProduk,
            'jumlah'=>$this->jumlahProduk,
        ];
        $this->resetForm();
    }

    public function editLine($index)
    {
        $this->update = true;
        $this->indexDetail = $index;
        $this->idProduk = $this->data_detail[$index]['produk_id'];
        $this->namaProduk = $this->data_detail[$index]['nama_produk'];
        $this->jumlahProduk = $this->data_detail[$index]['jumlah'];
    }


    public function updateLine()
    {
        // update line transaksi
        $this->validatedToTable();

        $index = $this->indexDetail;
        $this->data_detail[$index]['produk_id'] = $this->idProduk;
        $this->data_detail[$index]['nama_produk'] = $this->namaProduk;
        $this->data_detail[$index]['jumlah'] = $this->jumlahProduk;
        $this->resetForm();
        $this->update = false;
    }

    public function destroyLine($index)
    {
        // remove line transaksi
        unset($this->data_detail[$index]);
        $this->data_detail = array_values($this->data_detail);
    }

    public function validateData()
    {
        return $this->validate([
            'mutasiId'=>'nullable',
            'jenisMutasi'=>'required',
            'gudangAsalId'=>'required',
            'gudangTujuanId'=>'required',
            'tglMutasi'=>'required',
            'keterangan'=>'nullable',
            'persediaanBaikKalimas'=>'required',
            'persediaanBaikPerak'=>'required',
            'data_detail'=>'required'
        ]);
    }

    public function store()
    {
        DB::beginTransaction();
        try{
            (new StockMutasiRepo())->store((object) $this->validateData());
            DB::commit();
        } catch (ModelNotFoundException $e){
            DB::rollback();
            session()->flash('message', $e);
        }
        return redirect()->route('stock.mutasi.baik.baik');

    }

    public function update()
    {
        DB::beginTransaction();
        try{
            (new StockMutasiRepo())->update((object) $this->validateData());
            DB::commit();
        } catch (ModelNotFoundException $e){
            DB::rollback();
            session()->flash('message', $e);
        }
        return redirect()->route('stock.mutasi.baik.baik');

    }
}
