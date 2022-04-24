<?php

namespace App\Http\Livewire\Stock;

use App\Haramain\Repository\Stock\StockMasukRepo;
use App\Models\Stock\StockMasuk;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockMasukForm extends StockTransaksi
{
    public $kondisi;

    public function mount($kondisi = null, $stockmasuk = null): void
    {
        $this->kondisi = ($kondisi) ? $kondisi : 'baik';

        $stockmasuk = StockMasuk::query()->find($kondisi);

        if ($stockmasuk)
        {
            $this->stock_id = $stockmasuk->id;

            $this->forMount('update', $stockmasuk, $stockmasuk->stockMasukDetail);

        }
    }

    public function render()
    {
        return view('livewire.stock.stock-masuk-form');
    }
    
     /**
     * validate data before update or insert
     * @return array
     */
    protected function validateData(): array
    {
        return $this->validate([
            'stock_id'=>'nullable',
            'supplier_id'=>'required',
            'gudang_id'=>'required',
            'kondisi'=>'required',
            'tgl_masuk'=>'required|date_format:d-M-Y',
            'tgl_keluar'=>'nullable|date_format:d-M-Y',
            'keterangan'=>'nullable|string',
        ]);
    }

    /**
     * Store data
     * if fail, show message
     */
    public function store()
    {
        \DB::beginTransaction();
        try{
            (new StockMasukRepo)::store((object) $this->validateData(), $this->data_detail);
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollback();
            session()->flash('message', $e);
        }
        return redirect()->route('stock.masuk');
    }

    public function update()
    {
        \DB::beginTransaction();
        try{
            (new StockMasukRepo)::update((object) $this->validateData(), $this->data_detail);
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollback();
            session()->flash('message', $e);
        }
        return redirect()->route('stock.masuk');
    }
}
