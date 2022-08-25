<?php

namespace App\Http\Livewire\Stock;

use App\Haramain\Repository\Persediaan\PersediaanRepository;
use App\Haramain\Repository\Stock\StockInventoryRepo;
use App\Haramain\Repository\Stock\StockMutasiRepo;
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

    public string $mode = 'create';
    public bool $update = false;
    public $jenisMutasi;

    public $mutasi_id;

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

    public function mount($mutasiId = null)
    {
        $this->gudang_data = Gudang::query()->oldest()->get();
        $this->tgl_mutasi = tanggalan_format(now('ASIA/JAKARTA'));

        $this->jenis_mutasi = match (Str::between(url()->current(), 'mutasi/', '/trans')) {
            "baik_baik" => "baik_baik",
            "baik_rusak" => "baik_rusak",
            "rusak_rusak" => "rusak_rusak",
            default => null,
        };

        if ($mutasiId){
            // get data for update
            $this->mode = 'update';
            $mutasi = StockMutasi::query()->find($mutasiId);
            $stockMasuk = $mutasi->stockMasukMorph()->first();
            $this->jenis_mutasi = $mutasi->jenis_mutasi;
            $this->mutasi_id = $mutasi->id;
            $this->gudang_asal_id = $mutasi->gudang_asal_id;
            $this->gudang_tujuan_id = $mutasi->gudang_tujuan_id;
            $this->tgl_mutasi = tanggalan_format($mutasi->tgl_mutasi);
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
            'gudang_asal_id'=>'required',
        ]);
    }

    public function addLine()
    {
        $this->validatedToTable();
        $check = (new StockInventoryRepo())->check($this->produk_id, $this->gudang_asal_id, 'baik', $this->jumlah);
        if (!$check->status){
            session()->flash('error jumlah', $check->keterangan);
        } else {
            $this->data_detail [] = [
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
        $this->produk_id = $this->data_detail[$index]['produk_id'];
        $this->produk_nama = $this->data_detail[$index]['produk_nama'];
        $this->produk_kode_lokal = $this->data_detail[$index]['kode_lokal'];
        $this->produk_kategori_harga = $this->data_detail[$index]['kategori_harga'];
        $this->produk_cover = $this->data_detail[$index]['cover'];
        $this->jumlah = $this->data_detail[$index]['jumlah'];
        $this->produk_screen = $this->produk_nama."\n".$this->produk_kategori_harga." ".$this->produk_cover;
    }

    public function updateLine()
    {
        $this->validatedToTable();
        $check = (new PersediaanRepository())->check($this->produk_id, $this->gudang_asal_id, 'baik', $this->jumlah);
        if (!$check->status){
            session()->flash('error jumlah', $check->keterangan);
        } else {
            $index = $this->index;
            $this->data_detail[$index]['produk_id'] = $this->produk_id;
            $this->data_detail[$index]['produk_nama'] = $this->produk_nama;
            $this->data_detail[$index]['kode_lokal'] = $this->produk_kode_lokal;
            $this->data_detail[$index]['kategori_harga'] = $this->produk_kategori_harga;
            $this->data_detail[$index]['cover'] = $this->produk_cover;
            $this->data_detail[$index]['jumlah'] = $this->jumlah;
            $this->update = false;
            $this->resetFormDetail();
        }
    }

    public function destroyLine($index)
    {
        unset($this->data_detail[$index]);
        $this->data_detail = array_values($this->data_detail);
    }

    protected function validatemaster()
    {
        return (object) $this->validate([
            'mutasi_id'=>'nullable',
            'jenis_mutasi'=>'required',
            'gudang_asal_id'=>'required',
            'gudang_tujuan_id'=>'required',
            'tgl_mutasi'=>'required',
            'keterangan'=>'nullable',
            'data_detail'=>'required'
        ]);
    }

    public function store()
    {
        $data = $this->validatemaster();

    }

    public function update()
    {
        $data = $this->validatemaster();
        DB::beginTransaction();
        try {
            (new StockMutasiRepo())->update($data);
            DB::commit();
            return redirect()->route('mutasi.baik_baik');
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            session()->flash('error_store', $e);
        }
    }

    public function render()
    {
        return view('livewire.stock.stock-mutasi-form');
    }
}
