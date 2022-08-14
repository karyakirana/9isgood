<?php

namespace App\Http\Livewire\Datatables;

use App\Haramain\Traits\LivewireTraits\DatatablesTraits;
use App\Models\Keuangan\Persediaan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PersediaanTable extends DataTableComponent
{
    use DatatablesTraits;

    protected $listeners = [
        'refreshDatatables'=>'$refresh',
        'setGudang',
        'setKondisi',
        'resetVar'
    ];

    protected $activeCash;
    protected $kondisi;
    protected $gudang;

    public function mount($activeCash = true, $kondisi = null, $gudang = null)
    {
        $this->activeCash = ($activeCash) ? session('ClosedCash') : null;
        $this->kondisi = $kondisi;
        $this->gudang = $gudang;
    }

    public function columns(): array
    {
        return [
            Column::make('Kondisi'),
            Column::make('Tgl Input'),
            Column::make('Gudang'),
            Column::make('Produk'),
            Column::make('Harga'),
            Column::make('Total'),
        ];
    }

    public function query(): Builder
    {
        $persediaan = Persediaan::query();
        if ($this->activeCash){
            $persediaan = $persediaan->where('active_cash', $this->activeCash);
        }
        if ($this->gudang != null){
            $persediaan = $persediaan->where('gudang_id', $this->gudang);
        }
        if ($this->kondisi != null){
            $persediaan = $persediaan->where('jenis', $this->kondisi);
        }
        return $persediaan;
    }

    public function setGudang($gudangId)
    {
        $this->gudang = $gudangId;
    }

    public function setKondisi($kondisi)
    {
        $this->kondisi = $kondisi;
    }

    public function setVar()
    {
        $this->reset(['gudang', 'kondisi']);
    }

    public function rowView(): string
    {
        return 'livewire-tables.rows.persediaan_table';
    }
}
