<?php

namespace App\Http\Livewire\Keuangan;

use App\Haramain\SistemKeuangan\SubKasir\PenerimaanLainService;
use Livewire\Component;

class PenerimaanLainIndex extends Component
{
    protected $penerimaan_lain_id;

    public function destroy($penerimaan_lain_id)
    {
        $this->penerimaan_lain_id = $penerimaan_lain_id;
        // todo emit show confirmation delete
    }

    public function confirmationDestroy()
    {
        $delete = (new PenerimaanLainService())->handleDestroy($this->penerimaan_lain_id);
        $this->emit('$refresh'); // refresh table
        session()->flash('messages', $delete['keterangan']);
    }

    public function render()
    {
        return view('livewire.keuangan.penerimaan-lain-index');
    }
}
