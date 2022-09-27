<?php

namespace App\Http\Livewire\Keuangan;

use Livewire\Component;

class PiutangInternalIndex extends Component
{
    public $piutang_internal_id;

    public function destroy($piutang_internal_id)
    {
        $this->piutang_internal_id = $piutang_internal_id;
    }

    public function render()
    {
        return view('livewire.keuangan.piutang-internal-index');
    }
}
