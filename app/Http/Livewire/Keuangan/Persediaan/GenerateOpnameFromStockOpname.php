<?php

namespace App\Http\Livewire\Keuangan\Persediaan;

use App\Haramain\SistemKeuangan\SubPersediaan\Opname\PersediaanOpnameFromStockOpname;
use App\Models\Keuangan\PersediaanOpname;
use App\Models\Keuangan\PersediaanOpnameDetail;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class GenerateOpnameFromStockOpname extends Component
{
    public function render()
    {
        return view('livewire.keuangan.persediaan.generate-opname-from-stock-opname');
    }

    public function destroy()
    {
        DB::beginTransaction();
        try {
            PersediaanOpnameDetail::whereRelation('persediaan_opname', 'active_cash', '=', session('ClosedCash'))->delete();
            PersediaanOpname::where('active_cash', session('ClosedCash'))->delete();
            DB::commit();
            session()->flash('message', 'Data Berhasil di Generate');
            $this->emit('refreshDatatable');
        } catch (ModelNotFoundException $e){
            session()->flash('message', $e->getMessage());
        }
    }

    public function generate()
    {
        DB::beginTransaction();
        try {
            (new PersediaanOpnameFromStockOpname())->generate();
            DB::commit();
            session()->flash('message', 'Data Berhasil di Generate');
            $this->emit('refreshDatatable');
        } catch (ModelNotFoundException $e){
            session()->flash('message', $e->getMessage());
        }
    }
}
