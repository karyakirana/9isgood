<?php

namespace App\Http\Livewire\Generator;

use App\Haramain\Service\Generator\GenPembelianInternalService;
use App\Haramain\Service\Generator\GenPersediaanMutasiService;
use App\Haramain\Service\Generator\GenPersediaanOpnameService;
use App\Haramain\Service\Generator\GenPersediaanPenjualanService;
use Livewire\Component;

class PersediaanOpname extends Component
{
    protected $genPersediaanOpnameService;
    protected $genPembelianService;
    protected $genPenjualanService;
    protected $genMutasiService;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->genPersediaanOpnameService = new GenPersediaanOpnameService();
        $this->genPembelianService = new GenPembelianInternalService();
        $this->genPenjualanService = new GenPersediaanPenjualanService();
        $this->genMutasiService = new GenPersediaanMutasiService();
    }

    public function generateStockOpname()
    {
        $generate = $this->genPersediaanOpnameService->handleGenerateAll();
        if ($generate['status']){
            $this->emit('refreshDatatables');
            session()->flash('success', 'sukses Generate Stock Opname');
        } else {
            session()->flash('error_message', $generate['keterangan']);
        }
    }

    public function generatePembelian()
    {
        $generate = $this->genPembelianService->handleGenerate();
        if ($generate['status']){
            $this->emit('refreshDatatables');
            session()->flash('success', 'sukses Generate Pembelian');
        } else {
            session()->flash('error_message', $generate['keterangan']);
        }
    }

    public function generateMutasi()
    {
        $generate = $this->genMutasiService->handleGenerate();
        if ($generate['status']){
            $this->emit('refreshDatatables');
            session()->flash('success', 'sukses Generate Mutasi');
        } else {
            session()->flash('error_message', $generate['keterangan']);
        }
    }

    public function generatePenjualan()
    {
        $generate = $this->genPenjualanService->handleGeneratePenjualan();
        if ($generate['status']){
            $this->emit('refreshDatatables');
            session()->flash('success', 'sukses Generate Penjualan');
        } else {
            session()->flash('error_message', $generate['keterangan']);
        }
    }

    public function generatePenjualanRetur()
    {
        $generate = $this->genPenjualanService->handleGeneratrPenjualanRetur();
        if ($generate['status']){
            $this->emit('refreshDatatables');
            session()->flash('success', 'sukses Generate Penjualan Retur');
        } else {
            session()->flash('error_message', $generate['keterangan']);
        }
    }

    public function render()
    {
        return view('livewire.generator.persediaan-opname');
    }
}
