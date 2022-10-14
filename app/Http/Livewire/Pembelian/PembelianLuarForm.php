<?php

namespace App\Http\Livewire\Pembelian;


class PembelianLuarForm extends PembelianLivewire
{
    //use PembelianFormTraits;
    // var master
    public $jenis = 'BLU', $kondisi='baik';

    public function mount($pembelianId = null)
    {
        if ($pembelianId){
            $this->mountPembelian($pembelianId);
        }
    }

    public function addLine()
    {
        $this->validate([
            'produk_nama'=>'required',
            'jumlah'=>'required'
        ]);
        parent::addLine();
    }

    public function store()
    {
        //dd($this->data_detail);
        $data = $this->setDataValidate();
        $store = $this->pembelianService->handleStore($data);
        if ($store->status){
            // redirect
            session()->flash('storeMessage', $store->keterangan);
            return redirect()->to(route('pembelian'));
        }
        session()->flash('storeMessage', $store->keterangan);
        return null;
    }

    public function update()
    {
        $data = $this->setDataValidate();
        $store = $this->pembelianService->handleUpdate($data);
        if ($store->status){
            // redirect
            session()->flash('storeMessage', $store->keterangan);
            return redirect()->to(route('pembelian'));
        }
        session()->flash('storeMessage', $store->keterangan);
        return null;
    }

    public function render()
    {
        return view('livewire.pembelian.pembelian-luar-form');
    }
}
