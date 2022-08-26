<?php

namespace App\Http\Livewire\Stock;

use App\Haramain\Repository\Persediaan\PersediaanRepository;
use App\Haramain\Repository\Stock\StockInventoryRepo;
use App\Haramain\Repository\Stock\StockMutasiRepo;
use App\Haramain\Service\SistemStock\StockMutasiService;
use App\Models\Master\Gudang;
use App\Models\Master\Produk;
use App\Models\Stock\StockMutasi;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class StockMutasiForm extends Component
{
    protected $listeners = [
        'setProduk'
    ];

    protected $stockMutasiService;

    public $mode = 'create';
    public $update = false;
    public $jenisMutasi;

    public $mutasiId;

    public $gudang_data = [];
    public $gudangAsalId, $gudangTujuanId;
    public $tglMutasi;
    public $suratJalan;
    public $keterangan;

    // stock masuk
    public $tglMasuk;

    // stock keluar
    public $tglKeluar;

    // persediaan transaksi
    public $tglInput;

    public $index;
    public array $dataDetail = [];
    public $produk_id;
    public $produk_nama, $produk_kode_lokal, $produk_kategori_harga, $produk_cover;
    public $produk_screen;
    public $jumlah;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->stockMutasiService = new StockMutasiService();
    }

    public function mount($mutasiId = null)
    {
        $this->gudang_data = Gudang::query()->oldest()->get();
        $this->tglMutasi = tanggalan_format(now('ASIA/JAKARTA'));

        if ($mutasiId){
            // get data for update
            $this->mode = 'update';
            $mutasi = StockMutasi::query()->find($mutasiId);
            $stockMasuk = $mutasi->stockMasukMorph()->first();
            $this->jenisMutasi = $mutasi->jenis_mutasi;
            $this->mutasiId = $mutasi->id;
            $this->gudangAsalId = $mutasi->gudang_asal_id;
            $this->gudangTujuanId = $mutasi->gudang_tujuan_id;
            $this->tglMutasi = tanggalan_format($mutasi->tgl_mutasi);
            $this->keterangan = $mutasi->keterangan;

            foreach ($mutasi->stockMutasiDetail as $item) {
                $this->dataDetail [] = [
                    'produk_id'=>$item->produk_id,
                    'produk_nama'=>$item->produk->nama,
                    'kode_lokal'=>$item->produk->kode_lokal,
                    'kategori_harga'=>$item->produk->kategoriHarga->nama,
                    'cover'=>$item->produk->cover,
                    'jumlah'=>$item->jumlah,
                ];
            }
        }
    }

    public function updatedGudangAsalId()
    {
        $this->emit('setGudang', $this->gudangAsalId);
    }

    public function setProduk(Produk $produk)
    {
        $this->produk_id = $produk->id;
        $this->produk_nama = $produk->nama;
        $this->produk_kode_lokal = $produk->kode_lokal;
        $this->produk_kategori_harga = $produk->kategoriHarga->nama;
        $this->produk_cover = $produk->cover;
        $this->produk_screen = $this->produk_nama."\n".$this->produk_kategori_harga." ".$this->produk_cover;
    }

    public function resetFormDetail()
    {
        $this->reset([
            'produk_id', 'produk_nama', 'produk_kode_lokal', 'produk_cover',
            'produk_screen', 'produk_kategori_harga', 'jumlah'
        ]);
    }

    protected function validatedToTable()
    {
        $this->validate([
            'produk_id'=>'required',
            'jumlah'=>'required',
            'gudangAsalId'=>'required',
        ]);
    }

    public function addLine()
    {
        $this->validatedToTable();
        $check = (new StockInventoryRepo())->check($this->produk_id, $this->gudangAsalId, 'baik', $this->jumlah);
        if (!$check->status){
            session()->flash('error jumlah', $check->keterangan);
        } else {
            $this->dataDetail [] = [
                'produk_id'=>$this->produk_id,
                'kode_lokal'=>$this->produk_kode_lokal,
                'produk_nama'=>$this->produk_nama,
                'kategori_harga'=>$this->produk_kategori_harga,
                'cover'=>$this->produk_cover,
                'jumlah'=>$this->jumlah,
            ];
            $this->resetFormDetail();
        }
    }

    public function editLine($index)
    {
        $this->update = true;
        $this->index = $index;
        $this->produk_id = $this->dataDetail[$index]['produk_id'];
        $this->produk_nama = $this->dataDetail[$index]['produk_nama'];
        $this->produk_kode_lokal = $this->dataDetail[$index]['kode_lokal'];
        $this->produk_kategori_harga = $this->dataDetail[$index]['kategori_harga'];
        $this->produk_cover = $this->dataDetail[$index]['cover'];
        $this->jumlah = $this->dataDetail[$index]['jumlah'];
        $this->produk_screen = $this->produk_nama."\n".$this->produk_kategori_harga." ".$this->produk_cover;
    }

    public function updateLine()
    {
        $this->validatedToTable();
        $index = $this->index;
        $this->dataDetail[$index]['produk_id'] = $this->produk_id;
        $this->dataDetail[$index]['produk_nama'] = $this->produk_nama;
        $this->dataDetail[$index]['kode_lokal'] = $this->produk_kode_lokal;
        $this->dataDetail[$index]['kategori_harga'] = $this->produk_kategori_harga;
        $this->dataDetail[$index]['cover'] = $this->produk_cover;
        $this->dataDetail[$index]['jumlah'] = $this->jumlah;
        $this->update = false;
        $this->resetFormDetail();
    }

    public function destroyLine($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->data_detail);
    }

    protected function validatemaster()
    {
        $this->tglInput = $this->tglMutasi;
        $this->tglKeluar = $this->tglMutasi;
        $this->tglMasuk = $this->tglMutasi;
        return $this->validate([
            'mutasiId'=>($this->mutasiId) ? 'required' : 'nullable',
            'jenisMutasi'=>'required',
            'gudangAsalId'=>'required',
            'gudangTujuanId'=>'required',
            'tglMutasi'=>'required',
            'keterangan'=>'nullable',
            'dataDetail'=>'required',

            'tglMasuk'=>'required',
            'tglKeluar'=>'required',
            'tglInput'=>'required',
        ]);
    }

    public function store()
    {
        $data = $this->validatemaster();
        $store = $this->stockMutasiService->handleStore($data);
        if ($store->status){
            // redirect
            session()->flash('storeMessage', $store->keterangan);
            return redirect()->to('penjualan/print/'.$store->keterangan->id);
        }
        session()->flash('storeMessage', $store->keterangan);
        return null;
    }

    public function update()
    {
        $data = $this->validatemaster();
        $store = $this->stockMutasiService->handleUpdate($data);
        if ($store->status){
            // redirect
            session()->flash('storeMessage', $store->keterangan);
            return redirect()->to('penjualan/print/'.$store->keterangan->id);
        }
        session()->flash('storeMessage', $store->keterangan);
        return null;
    }

    public function render()
    {
        return view('livewire.stock.stock-mutasi-form');
    }
}
