<?php

namespace App\Http\Livewire\Stock;

use App\Haramain\Traits\LivewireTraits\SetProdukTraits;
use App\Models\KonfigurasiJurnal;
use App\Models\Stock\StockMutasi;
use App\Models\Master\Gudang;
use App\Models\Master\Produk;
use App\Haramain\Repository\Stock\StockMutasiBaikRepo;
use Livewire\Component;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockMutasiBaikBaikForm extends Component
{
    use SetProdukTraits;

     // first initiate properties
     public $mode = 'create', $update = false;
     public $mutasi_id;

     // for mounting
     public $gudang_data = [];

    // form detail
    public $data_detail = [], $indexDetail, $index;
    public $idDetail, $idProduk, $namaProduk, $kodeLokalProduk;
    public $jumlahProduk, $gudangAsal, $gudangTujuan;


    // master
    public $kode, $jenis_mutasi, $gudang_asal_id, $gudang_tujuan_id;
    public $tgl_mutasi, $user_id, $keterangan;
    public $gudang_id, $stock_id;

    // var transaksi
    public $persediaan_baik_kalimas, $persediaan_baik_perak;

    protected $listeners = [
        'set_produk'=>'setProduk',
    ];

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->gudang_data = Gudang::all();
        $this->tgl_mutasi = tanggalan_format(now('ASIA/JAKARTA'));

        // initiate akun transaksi
        $this->persediaan_baik_kalimas = KonfigurasiJurnal::query()->find('persediaan_baik_kalimas')->akun_id;
        $this->persediaan_baik_perak = KonfigurasiJurnal::query()->find('persediaan_baik_perak')->akun_id;
    }

    public function render()
    {
        return view('livewire.stock.stock-mutasi-baik-baik-form');
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
        $produk = $this->setProduk_sales($produk);
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
            'mutasi_id'=>'nullable',
            'gudang_asal_id'=>'required',
            'gudang_tujuan_id'=>'required',
            'tgl_mutasi'=>'required',
            'keterangan'=>'nullable',
            'persediaan_baik_kalimas'=>'required',
            'persediaan_baik_perak'=>'required',
            'data_detail'=>'required'
        ]);
    }

    public function store()
    {
        \DB::beginTransaction();
        try{
            (new StockMutasiBaikRepo)->store((object) $this->validateData());
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollback();
            session()->flash('message', $e);
        }
        return redirect()->route('stock.mutasi.baik.baik');

    }

    public function update()
    {
        \DB::beginTransaction();
        try{
            (new StockMutasiBaikRepo)->update((object) $this->validateData(), $this->data_detail);
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollback();
            session()->flash('message', $e);
        }
        return redirect()->route('stock.mutasi.baik.baik');

    }

}
