<?php

namespace App\Http\Livewire\Generator;

use App\Haramain\Service\Generator\GenPersediaanOpnameService;
use Livewire\Component;

class PersediaanOpname extends Component
{
    protected $genPersediaanOpnameService;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->genPersediaanOpnameService = new GenPersediaanOpnameService();
    }

    public function generate()
    {
        $generate = $this->genPersediaanOpnameService->handleGenerateAll();
        if ($generate['status']){
            $this->emit('refreshDatatables');
            session()->flash('success', 'sukses Generate');
        } else {
            session()->flash('error_message', $generate['keterangan']);
        }
    }

    public function render()
    {
        return view('livewire.generator.persediaan-opname');
    }
}
