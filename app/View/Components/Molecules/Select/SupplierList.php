<?php

namespace App\View\Components\Molecules\Select;

use App\Models\Master\Supplier;
use Illuminate\View\Component;

class SupplierList extends Component
{
    public $supplier;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->supplier = new Supplier();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.molecules.select.supplier-list', ['supplier'=>$this->supplier->newQuery()->get()]);
    }
}
