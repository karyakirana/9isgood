<?php

namespace App\Http\Livewire\Stock;

use App\Haramain\Traits\LivewireTraits\SetProdukTraits;
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


    protected $listeners = [
        'set_produk'=>'setProduk',
    ];

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->gudang_data = Gudang::all();
        $this->tgl_mutasi = tanggalan_format(now('ASIA/JAKARTA'));

    }

    public function render()
    {
        return view('livewire.stock.stock-mutasi-baik-baik-form');
    }

    public function forMount($mode, $data, $data_detail)
    {
        $this->mode = $mode;
        $this->stock_id = $data->id;
        $this->jenis_mutasi = $data->jenis_mutasi;
        $this->gudang_asal_id = $data ->gudang_asal_id;
        $this->gudang_tujuan_id = $data ->gudang_tujuan_id;
        $this->user_id = $data->user_id;
        $this->tgl_mutasi = ($data->tgl_mutasi) ? tanggalan_format($data->tgl_mutasi) : null;
        $this->keterangan = $data->keterangan;

        foreach ($data_detail as $row)
        {
            $this->data_detail [] = [
                'produk_id'=>$row->produk_id,
                'kode_lokal'=>$row->produk->kode_lokal,
                'nama_produk'=>$row->produk->nama."\n".$row->produk->cover."\n".$row->produk->hal,
                'jumlah'=>$row->jumlah,
            ];
        }
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
            StockMutasiBaikRepo::update((object) $this->validateData(), $this->data_detail);
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollback();
            session()->flash('message', $e);
        }
        return redirect()->route('stock.mutasi.baik.baik');

    }

}
