<?php

namespace App\Http\Livewire\Stock;

use App\Haramain\Service\SistemStock\StockInventoryService;
use Livewire\Component;

class RefreshStock extends Component
{
    public StockInventoryService $stockInventoryService;

    public function render()
    {
        return view('livewire.stock.refresh-stock');
    }

    public function generateStockOpname(StockInventoryService $stockInventoryService)
    {
        $hbasil = $stockInventoryService->handleGenerateStockOpname();
        $this->emit('refreshDatatable');
        session()->flash('generate', $hbasil->keterangan);
    }

    public function generateStockMasuk(StockInventoryService $stockInventoryService)
    {
        $hbasil = $stockInventoryService->handleGenerateStockMasuk();
        $this->emit('refreshDatatable');
        session()->flash('generate', $hbasil->keterangan);
    }

    public function generateStockKeluar(StockInventoryService $stockInventoryService)
    {
        $hbasil = $stockInventoryService->handleGenerateStockKeluar();
        $this->emit('refreshDatatable');
        session()->flash('generate', $hbasil->keterangan);
    }

    public function generateClean(StockInventoryService $stockInventoryService)
    {
        $stockInventoryService->handleClean();
        $this->emit('refreshDatatable');
        session()->flash('generate', 'berhasil');
    }
}
