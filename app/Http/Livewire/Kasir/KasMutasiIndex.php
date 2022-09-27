<?php

namespace App\Http\Livewire\Kasir;

use Livewire\Component;

class KasMutasiIndex extends Component
{
    protected $kas_mutasi_id;

    public function destroy($kas_mutasi_id)
    {
        $this->kas_mutasi_id = $kas_mutasi_id;
    }

    public function destroyConfirmation()
    {
        // todo delete
    }

    public function render()
    {
        return view('livewire.kasir.kas-mutasi-index');
    }
}
