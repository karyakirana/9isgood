<?php

namespace App\Http\Livewire\Keuangan\Persediaan;

use App\Haramain\SistemKeuangan\SubOther\Generator\KoreksiStockOpname;
use Livewire\Component;

class GenerateOpnamePrice extends Component
{
    public function render()
    {
        return view('livewire.keuangan.persediaan.generate-opname-price');
    }

    public function generate()
    {
        $koreksi = (new KoreksiStockOpname())->generate();
        session()->flash('message', $koreksi['keterangan']);
        $this->emit('refreshDatatable');
    }
}
