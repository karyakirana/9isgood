<?php

namespace App\Http\Livewire\Keuangan\Kasir;

use App\Haramain\SistemKeuangan\SubKasir\PengeluaranPembelianService;
use Livewire\Component;

class PengeluaranPembelianIndex extends Component
{
    protected $listeners = [
        'confirmDestroy'
    ];

    protected $pengeluaranPembelianId;

    public function render()
    {
        return view('livewire.keuangan.kasir.pengeluaran-pembelian-index');
    }

    public function destroy($id)
    {
        $this->pengeluaranPembelianId = $id;
        $this->emit('showDeleteNotification');
    }

    public function confirmDestroy()
    {
        $delete = (new PengeluaranPembelianService())->handleDestroy($this->pengeluaranPembelianId);
        if ($delete['status']){
            $this->emit('refreshDatatable');
        }
        $this->emit('hideDeleteNotification');
        session()->flash('message', $delete['keterangan']);
    }
}
