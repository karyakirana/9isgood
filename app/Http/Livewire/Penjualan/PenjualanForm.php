<?php

namespace App\Http\Livewire\Penjualan;

use App\Haramain\SistemPenjualan\PenjualanService;
use App\Http\Livewire\Master\LivewireProdukTrait;
use App\Http\Livewire\Master\SetCustomerTrait;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PenjualanForm extends Component
{
    // trait
    use SetCustomerTrait;
    use LivewireProdukTrait;
    use LivewirePenjualanTrait;

    protected $listeners = [
        'set_produk'=>'setProduk',
        'set_customer'=>'setCustomer'
    ];

    public $mode = 'create'; // default create

    protected $penjualanService;

    // penjualan attribute
    public $penjualan_id;
    // customer in customer trait
    public $gudang_id;
    public $user_id;
    public $tgl_nota, $tgl_tempo;
    public $jenis_bayar;
    public $status_bayar = 'belum';
    public $total_barang;
    public $ppn;
    public $biaya_lain;
    public $total_bayar, $total_bayar_rupiah;
    public $keterangan;
    public $print;

    // penjualan attribute ada di traits

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->penjualanService = new PenjualanService();
        // initiate default date
        $this->tgl_nota = tanggalan_format(now('ASIA/JAKARTA'));
        $this->tgl_tempo = tanggalan_format(now('ASIA/JAKARTA')->addMonths(2));
    }

    public function mount($penjualan_id = null)
    {
        $this->user_id = auth()->id();
        if ($penjualan_id){
            $this->mode = 'update';
            $penjualan = $this->penjualanService->handleGetData($penjualan_id);
            $this->penjualan_id = $penjualan->id;
            $this->setCustomer($penjualan->customer); // set customer
            $this->gudang_id = $penjualan->gudang_id;
            $this->tgl_nota = $penjualan->tgl_nota;
            $this->jenis_bayar = $penjualan->jenis_bayar;
            $this->tgl_tempo = ($penjualan->tgl_tempo) ?: $this->tgl_tempo;
            $this->total_barang = $penjualan->total_barang;
            $this->ppn = $penjualan->ppn;
            $this->biaya_lain = $penjualan->biaya_lain;
            $this->total_bayar = $penjualan->total_bayar;
            $this->keterangan = $penjualan->keterangan;

            // penjualan_detail
            $this->setDataDetail($penjualan->penjualanDetail);

            // helper atteribute
            $this->total_penjualan = (int) $this->total_bayar - (int) $this->ppn - (int) $this->biaya_lain;
            $this->total_penjualan_rupiah = rupiah_format($this->total_penjualan);
            $this->total_bayar_rupiah = rupiah_format($this->total_bayar);
        }
    }

    public function updatedPpn()
    {
        $this->setTotalForm();
    }

    public function updatedBiayaLain()
    {
        $this->setTotalForm();
    }

    protected function validateData()
    {
        return $this->validate([
            'penjualan_id'=>($this->mode == 'update') ? 'required' : 'nullable',
            'customer_id'=>'required',
            'customer_nama'=>'required',
            'user_id'=>'required',
            'gudang_id'=>'required',
            'tgl_nota'=>'required',
            'tgl_tempo'=>($this->jenis_bayar == 'tempo') ? 'required' : 'nullable',
            'jenis_bayar'=>'required',
            'status_bayar'=>'nullable',
            'total_barang'=>'required',
            'total_penjualan'=>'required',
            'total_bayar'=>'required',
            'dataDetail'=>'required',
            'keterangan'=>'nullable',
            'biaya_lain'=>( (int)$this->biaya_lain > 0) ?'required' : 'nullable',
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
