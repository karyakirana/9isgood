<?php

namespace App\View\Components\Molecules\Select;

use App\Haramain\Service\SistemKeuangan\Akun\AkunRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AkunKasList extends Component
{
    public $akun;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->akun = (new AkunRepository())->getAkunByTipe('kas');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('components.molecules.select.akun-kas-list');
    }
}
