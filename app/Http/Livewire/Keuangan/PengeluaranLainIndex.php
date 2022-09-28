<?php

namespace App\Http\Livewire\Keuangan;

use App\Haramain\SistemKeuangan\SubKasir\PengeluaranLainService;
use Livewire\Component;

class PengeluaranLainIndex extends Component
{
    public $pengeluaran_lain_id;

    public function destroy($pengeluaran_lain_id)
    {
        $this->pengeluaran_lain_id = $pengeluaran_lain_id;
        $this->emit('showDeleteNotification');
    }

    public function confirmationDestroy()
    {
        $delete = (new PengeluaranLainService())->handleDestroy($this->pengeluaran_lain_id);
        if ($delete['status']){
            $this->emit('refreshDatatable');
        }
        $this->emit('hideDeleteNotification');
        session()->flash('messagaes', $delete['keterangan']);
        $this->reset(['pengeluaran_lain_id']);
    }

    public function render()
    {
        return view('livewire.keuangan.pengeluaran-lain-index');
    }
}
