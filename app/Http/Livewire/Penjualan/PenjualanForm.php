<?php

namespace App\Http\Livewire\Penjualan;

use App\Haramain\SistemPenjualan\PenjualanService;
use App\Models\KonfigurasiJurnal;
use App\Models\Master\Produk;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanRetur;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PenjualanForm extends Component
{
    // trait
    use LivewirePenjualanTrait;

    protected $listeners = [
        'set_produk'=>'setProduk',
        'set_customer'=>'setCustomer'
    ];

    // penjualan attribute
    public $penjualan_id;
    public $customer_id;
    public $gudang_id;
    public $user_id;
    public $tgl_nota, $tgl_tempo;
    public $jenis_bayar;
    public $status_bayar;
    public $total_barang;
    public $ppn;
    public $biaya_lain;
    public $total_bayar;
    public $keterangan;
    public $print;

    // penjualan detail attribute
    public $produk_id;
    public $harga;
    public $jumlah;
    public $diskon;
    public $sub_total;

    // initiate
    protected $penjualanService;

    // initiation attributes
    protected $customer;
    protected $produk;
    protected $penjualan;
    protected $penjualanRetur;
    protected $konfigAkun;
    protected $jenisTransaksi; // penjualan, retur baik, retur rusak
    public $mode = 'create'; // default create dan bisa update

    // general attributes
    public $userId;

    // penjualan attributes
    public $penjualanId;
    public $gudangId;
    public $tglNota;
    public $tglTempo;
    public $jenisBayar;
    public $statusBayar = 'belum';
    public $totalBarang;
   // public $ppn, $biayaLain;
    public $totalPenjualan, $totalPenjualanRupiah; // total penjualan = pendapatan
    public $totalBayar, $totalBayarRupiah; // total bayar = piutang
    // public $keterangan;
    // public $print;

    // stock keluar
    public $kondisi = 'baik';
    public $tglKeluar;

    // persediaan
    public $tglInput;
    public $jenisPersediaan = 'keluar';

    // detail attributes
    public $dataDetail = [];
    public $update = false;
    public $index;
    // public $jumlah;
    public $pendapatan;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->penjualanService = new PenjualanService();
        // initaite
        $this->produk = new Produk();
        $this->penjualan = new Penjualan();
        $this->penjualanRetur = new PenjualanRetur();
        $this->konfigAkun = new KonfigurasiJurnal();
        // initiate default date
        $this->tglNota = tanggalan_format(now('ASIA/JAKARTA'));
        $this->tglTempo = tanggalan_format(now('ASIA/JAKARTA')->addMonth(2));
    }

    public function mount($jenisTransaksi = 'penjualan', $penjualanId = null)
    {
        $this->userId = \Auth::id();
        $this->jenisTransaksi = $jenisTransaksi;
        if ($penjualanId){
            $this->penjualanId = $penjualanId;
            $this->editData($penjualanId);
        }
    }

    protected function editData($penjualanId)
    {
        $penjualan = $this->penjualanService->handleGetData($penjualanId);
        $this->mode = 'update';
        // data penjualan
        $this->customerId = $penjualan->customer_id;
        $this->customerNama = $penjualan->customer->nama;
        $this->customerDiskon = $penjualan->customer->diskon;
        $this->userId = auth()->id();
        $this->gudangId = $penjualan->gudang_id;
        $this->tglNota = tanggalan_format($penjualan->tgl_nota);
        $tglNota = new Carbon($penjualan->tgl_nota);
        $this->tglTempo = ($penjualan->tgl_tempo != null) ? tanggalan_format($penjualan->tgl_tempo) : tanggalan_format($tglNota->addMonth(2));
        //dd($this->tglTempo);
        $this->jenisBayar = $penjualan->jenis_bayar;
        $this->totalBarang = $penjualan->total_barang;
        $this->ppn = $penjualan->ppn;
        $this->biayaLain = $penjualan->biaya_lain;
        $this->totalBayar = $penjualan->total_bayar;
        $this->totalBayarRupiah = rupiah_format($this->totalBayar);
        $this->totalPenjualan = (int)$this->totalBayar - (int)$this->ppn - (int)$this->biayaLain;
        $this->totalPenjualanRupiah = rupiah_format($this->totalPenjualan);
        $this->keterangan = $penjualan->keterangan;
        foreach ($penjualan->penjualanDetail as $item) {
            $this->dataDetail[] = [
                'produk_id'=>$item->produk_id,
                'produk_nama'=>$item->produk->nama."\n".$item->produk->kode_lokal."\n".$item->produk->kategoriHarga->deskripsi."\n".$item->produk->cover,
                'produk_kode_lokal'=>$item->produk->kode_lokal,
                'produk_kategori'=>$item->produk->kategori->nama,
                'produk_kategori_harga'=>$item->produk->kategoriHarga->deskripsi,
                'harga'=>$item->harga,
                'harga_rupiah'=>rupiah_format($item->harga),
                'diskon'=>$item->diskon,
                'jumlah'=>$item->jumlah,
                'sub_total'=>$item->sub_total
            ];
        }
        $this->pendapatan = $this->totalPenjualan;
    }

    public function setProduk($produkId)
    {
        $produk = $this->produk->newQuery()->find($produkId);
        $this->setDetailFromProduk($produk); // from trait
        $this->setSubTotal(); // from trait
        $this->update = false;
    }

    public function addLine()
    {
        $this->validateFormDetail();
        $this->setDataDetail(); // from trait
        $this->setTotalItem();
        $this->resetFormDetail();
    }

    public function editLine($index)
    {
        //dd( $this->dataDetail[$index]);
        $this->update = true;
        $this->index = $index;
        $this->getDataDetail($index); // from trait
        $this->setSubTotal();
    }

    public function updateLine()
    {
        $this->validateFormDetail();
        $index = $this->index;
        $this->updateDataDetail($index); // from trait
        $this->setTotalItem();
        $this->update = false;
        $this->resetFormDetail();
    }

    public function setRemoveLineIndex($index)
    {
        $this->index = $index;
        $this->emit('showConfirmation');
        $this->setTotalItem();
    }

    public function removeLine($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
        $this->emit('hideConfirmation');
    }

    public function setTotalItem()
    {
        // jumlah total barang
        $this->totalBarang = array_sum(array_column($this->dataDetail, 'jumlah'));
        // jumlah total dari sub_total
        $this->totalPenjualan = array_sum(array_column($this->dataDetail, 'sub_total'));
        $this->totalPenjualanRupiah = rupiah_format($this->totalPenjualan);
        $this->pendapatan = $this->totalPenjualan;
        // jumlah total bayar
        $this->totalBayar = (int)$this->totalPenjualan + (int)$this->biayaLain + (int)$this->ppn;
        $this->totalBayarRupiah = rupiah_format($this->totalBayar);
    }

    protected function resetFormDetail()
    {
        $this->reset([
            'index',
            'produkId', 'produkNama', 'produkKodeLokal', 'produkKategori', 'produkKategoriHarga', 'produkCover',
            'harga', 'diskon', 'jumlah', 'subTotal',
            // gimmick interface
            'hargaRupiah', 'hargaDiskon', 'hargaDiskonRupiah', 'subTotalRupiah'
        ]);
    }

    protected function validateData()
    {
        $this->tglInput = $this->tglNota;
        return $this->validate([
            'penjualanId'=>($this->mode == 'update' && $this->jenisTransaksi == 'penjualan') ? 'required' : 'nullable',
            'customerId'=>'required',
            'customerNama'=>'required',
            'userId'=>'required',
            'gudangId'=>'required',
            'tglNota'=>'required',
            'tglTempo'=>($this->jenisBayar == 'tempo') ? 'required' : 'nullable',
            'jenisBayar'=>'required',
            'statusBayar'=>'nullable',
            'totalBarang'=>'required',
            'totalPenjualan'=>'required',
            'totalBayar'=>'required',
            'dataDetail'=>'required',
            'keterangan'=>'nullable',
            // stock
            'kondisi'=>'required',
            // persediaan
            'jenisPersediaan'=>'required',
            'tglInput'=>'required',

            // akuntansi
            'pendapatan'=>'required',
            'biayaLain'=>( (int)$this->biayaLain > 0) ?'required' : 'nullable',
            'ppn'=>( (int)$this->ppn > 0) ?'required' : 'nullable',
        ]);
    }

    /**
     * Store data
     * if fail, show message
     */
    public function store()
    {
        $data = $this->validateData();
        // dd($data);
        $store = $this->penjualanService->handleStore($data);
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
        $data = $this->validateData();
        //dd($data);
        $store = $this->penjualanService->handleUpdate($data);
        if ($store->status){
            // redirect
            session()->flash('storeMessage', $store->keterangan);
            return redirect()->to('penjualan/print/'.$store->keterangan->id);
        }
        session()->flash('storeMessage', $store->keterangan);
        return null;
    }

    public function render(): View
    {
        return view('livewire.penjualan.penjualan-form')
            ->layout('layouts.metronics-811', ['minimize' => 'on']);
    }
}
