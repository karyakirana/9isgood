<?php

namespace App\Http\Livewire\Stock;

use App\Haramain\SistemStock\StockOpnameKoreksiService;
use App\Models\Master\Produk;
use Livewire\Component;

class StockOpnameKoreksiForm extends Component
{
    protected $listeners = [
        'set_produk'
    ];
    protected $stockOpanameKoreksiService;

    // attribute
    public $stockOpnameKoreksiId;
    public $jenis;
    public $kondisi;
    public $tglInput;
    public $gudangId;
    public $userId;
    public $keterangan;

    public $mode = 'create';
    public $update = false;
    public $produk_id;
    public $kode_lokal;
    public $produk_nama;
    public $jumlah;

    public $dataDetail = [];
    public $index;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->stockOpanameKoreksiService = new StockOpnameKoreksiService();
    }

    public function mount($jenis, $stockOpnameKoreksiId = null)
    {
        $this->tglInput = tanggalan_format(now('ASIA/JAKARTA'));
        $this->jenis = $jenis;
        if ($stockOpnameKoreksiId){
            $stockOpnameKoreksi = $this->stockOpanameKoreksiService->handleGetData($stockOpnameKoreksiId);
            if (!$stockOpnameKoreksi->status){
                // error
                session()->flash('storeMessage', $stockOpnameKoreksi->keterangan);
            }else{
                // load data
                $this->mode = 'update';
                $stockOpnameKoreksi = $stockOpnameKoreksi->data;
                $this->stockOpnameKoreksiId = $stockOpnameKoreksi->id;
                $this->kondisi = $stockOpnameKoreksi->kondisi;
                $this->tglInput = $stockOpnameKoreksi->tglInput;
                $this->gudangId = $stockOpnameKoreksi->gudang_id;
                $this->keterangan = $stockOpnameKoreksi->keterangan;
                $dataDetail = $stockOpnameKoreksi->stockOpnameKoreksiDetail;
                foreach ($dataDetail as $item) {
                    $this->dataDetail[] = [
                        'produk_id'=>$item->produk_id,
                        'kode_lokal'=>$item->produk->kode_lokal,
                        'produk_nama'=>$item->produk->nama."\n".$item->produk->kode_lokal."\n".$item->produk->kategoriHarga->deskripsi."\n".$item->produk->cover,
                        'jumlah'=>$item->jumlah
                    ];
                }
            }
        }
    }

    protected function validationLine()
    {
        $this->validate([
            'produk_nama'=>'required',
            'jumlah'=>'required'
        ]);
    }

    public function set_produk($produkId)
    {
        $produk = Produk::query()->findOrFail($produkId);
        $this->produk_id = $produk->id;
        $this->kode_lokal = $produk->kode_lokal;
        $this->produk_nama = $produk->nama."\n".$produk->kode_lokal."\n".$produk->kategoriHarga->deskripsi."\n".$produk->cover;
    }

    protected function resetFormDetail()
    {
        $this->reset(['produk_id', 'produk_nama', 'kode_lokal', 'jumlah']);
    }

    protected function removeValidation()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function addLine()
    {
        $this->validationLine();
        $this->dataDetail[] = [
            'produk_id'=>$this->produk_id,
            'kode_lokal'=>$this->kode_lokal,
            'produk_nama'=>$this->produk_nama,
            'jumlah'=>$this->jumlah
        ];
        $this->resetFormDetail();
    }

    public function editLine($index)
    {
        $this->index = $index;
        $this->produk_id = $this->dataDetail[$index]['produk_id'];
        $this->kode_lokal = $this->dataDetail[$index]['kode_lokal'];
        $this->produk_nama = $this->dataDetail[$index]['produk_nama'];
        $this->jumlah = $this->dataDetail[$index]['jumlah'];
        $this->update = true;
    }

    public function updateLine()
    {
        $this->validationLine();
        $index = $this->index;
        $this->dataDetail[$index]['produk_id'] = $this->produk_id;
        $this->dataDetail[$index]['kode_lokal'] = $this->kode_lokal;
        $this->dataDetail[$index]['produk_nama'] = $this->produk_nama;
        $this->dataDetail[$index]['jumlah'] = $this->jumlah;
        unset($this->index);
        $this->update = false;
        $this->resetFormDetail();
    }

    public function removeLine($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
    }

    protected function setData()
    {
        $this->userId = auth()->id();
        return $this->validate([
            'stockOpnameKoreksiId'=>($this->mode == 'update') ? 'required' : 'nullable',
            'jenis'=>'required',
            'kondisi'=>'required',
            'tglInput'=>'required',
            'gudangId'=>'required',
            'userId'=>'required',
            'keterangan'=>'required',

            'dataDetail'=>'required'
        ]);
    }

    public function store()
    {
        $store = $this->stockOpanameKoreksiService->handleStore($this->setData());
        // dd($store);
        if ($store->status){
            // redirect
            session()->flash('storeMessage', $store->keterangan);
            return redirect()->to('stock/opname/koreksi');
        }
        session()->flash('storeMessage', $store->keterangan);
        return null;
    }

    public function update()
    {
        $update = $this->stockOpanameKoreksiService->handleUpdate($this->setData());
        if ($update->status){
            // redirect
            session()->flash('storeMessage', $update->keterangan);
            return redirect()->to('stock/opname/koreksi');
        }
        session()->flash('storeMessage', $update->keterangan);
        return null;
    }

    public function render()
    {
        return view('livewire.stock.stock-opname-koreksi-form');
    }
}
