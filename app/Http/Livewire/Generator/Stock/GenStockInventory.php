<?php

namespace App\Http\Livewire\Generator\Stock;

use App\Haramain\Service\Generator\GenStockInventoryService;
use Livewire\Component;

class GenStockInventory extends Component
{
    protected $genStockInvemtoryService;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->genStockInvemtoryService = new GenStockInventoryService();
    }

    public function generateStockOpname()
    {
        $store = $this->genStockInvemtoryService->generateFromStockOpname();
        if ($store['status']){
            $this->emit('refreshDatatable');
            session()->flash('success', 'sukses Generate Stock Opname');
        } else {
            session()->flash('error_message', $store['keterangan']);
        }
    }

    public function generateStockMutasi()
    {
        $store = $this->genStockInvemtoryService->generateFromStockMutasi();
        if ($store['status']){
            $this->emit('refreshDatatable');
            session()->flash('success', 'sukses Generate Stock Mutasi');
        } else {
            session()->flash('error_message', $store['keterangan']);
        }
    }

    public function generatePembelian()
    {
        $store = $this->genStockInvemtoryService->generateFromPembelian();
        if ($store['status']){
            $this->emit('refreshDatatable');
            session()->flash('success', 'sukses Generate Stock Mutasi');
        } else {
            session()->flash('error_message', $store['keterangan']);
        }
    }

    public function generatePenjualan()
    {
        $store = $this->genStockInvemtoryService->generateFromPenjualan();
        if ($store['status']){
            $this->emit('refreshDatatable');
            session()->flash('success', 'sukses Generate Stock Mutasi');
        } else {
            session()->flash('error_message', $store['keterangan']);
        }
    }

    public function generateStockMasuk()
    {
        $store = $this->genStockInvemtoryService->generateFromStockMasuk();
        if ($store['status']){
            $this->emit('refreshDatatable');
            session()->flash('success', 'sukses Generate Stock Masuk');
        } else {
            session()->flash('error_message', $store['keterangan']);
        }
    }

    public function generateStockKeluar()
    {
        $store = $this->genStockInvemtoryService->generateFromStockKeluar();
        if ($store['status']){
            $this->emit('refreshDatatable');
            session()->flash('success', 'sukses Generate Stock Keluar');
        } else {
            session()->flash('error_message', $store['keterangan']);
        }
    }

    public function render()
    {
        return view('livewire.generator.stock.gen-stock-inventory');
    }
}
