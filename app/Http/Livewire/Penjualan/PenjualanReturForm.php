<?php

namespace App\Http\Livewire\Penjualan;

use App\Haramain\Repository\Penjualan\PenjualanReturRepo;
use App\Haramain\Repository\PenjualanReturRepository;
use App\Models\Master\Customer;
use App\Models\Master\Gudang;
use App\Models\Master\Produk;
use App\Models\Penjualan\PenjualanRetur;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class PenjualanReturForm extends Transaksi
{
    public $kondisi;
    public $penjualan_retur_id;

    public function mount($kondisi, $retur=null)
    {
        // initiate
        $retur = PenjualanRetur::query()->find($retur);

        // kondisi
        $this->kondisi = ($kondisi == 'baik') ? 'baik' : 'rusak';

        if ($retur)
        {
            // mode update true
            $this->penjualan_retur_id = $retur->id;

            // mount customer data
            $this->forMount('update', $retur, $retur->returDetail);

            $this->hitungTotal();
            $this->hitungTotalBayar();
        }
    }

    public function render()
    {
        return view('livewire.penjualan.penjualan-retur-form');
    }

    protected function validatedData():array
    {
        $this->total_barang = array_sum(array_column($this->data_detail, 'jumlah'));
        return $this->validate([
            'penjualan_retur_id'=>'nullable',
            'kondisi'=>'required',
            'customer_id'=>'required',
            'gudang_id'=>'required',
            'tgl_nota'=>'required|date_format:d-M-Y',
            'total_barang'=>'nullable|numeric',
            'ppn'=>'nullable|numeric',
            'biaya_lain'=>'nullable|numeric',
            'total_bayar'=>'required|numeric',
            'keterangan'=>'nullable|string',
            'data_detail'=>'nullable|array'
        ]);
    }

    public function store()
    {
        \DB::beginTransaction();
        try {
            $penjualanRetur = (new PenjualanReturRepo())->store($this->validatedData());
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            session()->flash('message', $e);
        }
        return redirect()->to(url('/').'/penjualan/retur/print/'.$penjualanRetur);
    }

    public function update()
    {
        \DB::beginTransaction();
        try {
            $penjualanRetur = (new PenjualanReturRepo())->update($this->validatedData());
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            session()->flash('message', $e);
        }
        return redirect()->to(url('/').'/penjualan/retur/print/'.$penjualanRetur);
    }

}
