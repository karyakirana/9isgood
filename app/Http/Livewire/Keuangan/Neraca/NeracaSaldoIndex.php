<?php

namespace App\Http\Livewire\Keuangan\Neraca;

use App\Models\Keuangan\NeracaSaldo;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NeracaSaldoIndex extends Component
{
    // properties
    public $akun_id, $debet, $kredit;
    
    // master form manipulate interface (rupiah format)
    public $total_debet, $total_kredit;

    public array $data = [];

    public function render()
    {
        return view('livewire.keuangan.neraca.neraca-saldo-index');
    }
 

    public function mount()
    {
        $data = NeracaSaldo::query()
            ->select(['active_cash', DB::raw('SUM(kredit) as kredit'), DB::raw('SUM(debet) as debet')])
            ->groupBy('active_cash')
            ->first();
            $this->total_debet=$data->debet;
            $this->total_kredit=$data->kredit;
    }

    public function total_debet()
    {
      
        $this->total_debet = NeracaSaldo::sum('debet');

    }
    public function total_kredit()
    {
      
        $this->total_kredit = NeracaSaldo::sum('kredit');

    }
}
