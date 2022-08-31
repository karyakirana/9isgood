<?php

namespace App\Http\Livewire\Generator\Keuangan;

use App\Haramain\SistemHelper\Generator\GenPiutangPenjualanService;
use Livewire\Component;

class PiutangPenjualanGenerator extends Component
{
    protected $genSaldoPiutangPenjualanService;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->genSaldoPiutangPenjualanService = new GenPiutangPenjualanService();
    }

    public function generateFromPenjualan()
    {
        $generate = $this->genSaldoPiutangPenjualanService->handleGeneratePenjualan();
        if ($generate['status']){
            $this->emit('refreshDatatables');
            session()->flash('success', 'sukses Generate Stock Opname');
        } else {
            session()->flash('error_message', $generate['keterangan']);
        }
    }

    public function generateFromPenjualanRetur()
    {
        $generate = $this->genSaldoPiutangPenjualanService->handleGeneratePenjualanRetur();
        if ($generate['status']){
            $this->emit('refreshDatatables');
            session()->flash('success', 'sukses Generate Stock Opname');
        } else {
            session()->flash('error_message', $generate['keterangan']);
        }
    }

    public function render()
    {
        return view('livewire.generator.keuangan.piutang-penjualan-generator');
    }
}
