<?php

namespace App\Http\Livewire\Pembelian;

use App\Models\Keuangan\HargaHppALL;
use App\Models\Master\Produk;

class PembelianInternalForm extends PembelianLivewire
{
    // var pembelian overrides
    public $jenisBayar = 'tunai';
    public $statusBayar = 'belum';

    // var pembelian internal
    public $jenis = 'INTERNAL', $kondisi = 'baik';

    // var pembelian detail
    public $hpp;

    // var stock masuk


    /**
     * @param $pembelianId
     * @return void
     */
    public function mount($pembelianId = null)
    {
        // load hpp
        $this->hpp = HargaHppALL::query()->latest()->first()->persen;
        if ($pembelianId){
            $this->mountPembelian($pembelianId);
        }
    }

    public function setProduk(Produk $produk)
    {
        parent::setProduk($produk); // TODO: Change the autogenerated stub
        $this->hitungHpp();
    }

    public function hitungHpp()
    {
        (int)$this->harga = $this->produk_harga * (float)$this->hpp;
    }

    public function addLine()
    {
        $this->validate([
            'produk_nama'=>'required',
            'jumlah'=>'required',
            'hpp'=>'required'
        ]);
        parent::addLine();
    }

    public function store()
    {
        $data = $this->setDataValidate();
        //dd($data);
        $store = $this->pembelianService->handleStore($data);
        if ($store->status){
            // redirect
            session()->flash('storeMessage', $store->keterangan);
            return redirect()->to(route('stock.masuk'));
        }
        session()->flash('storeMessage', $store->keterangan);
        return null;
    }

    public function update()
    {
        $data = $this->setDataValidate();
        $pembelian = $this->pembelianService->handleUpdate($data);
        session()->flash('storeMessage', $pembelian->keterangan);
        if ($pembelian->status){
            // redirect
            session()->flash('storeMessage', $pembelian->keterangan);
            return redirect()->to(route('stock.masuk'));
        }
        session()->flash('storeMessage', $pembelian->keterangan);
        return null;
    }

    public function render()
    {
        return view('livewire.pembelian.pembelian-internal-form');
    }
}
