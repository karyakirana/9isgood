<?php

namespace App\Http\Livewire\Keuangan\Kasir;

use App\Haramain\Traits\LivewireTraits\SetSupplierTraits;
use App\Models\Keuangan\HutangPembelian;
use Livewire\Component;

class PengeluaranPembelianForm extends Component
{
    use SetSupplierTraits;

    protected $listeners = [
        'setHutangPembelian'
    ];

    public $pengeluaran_pembelian_id;
    public $tgl_pengeluaran;
    public $jenis;
    public $user_id;
    public $total_pengeluaran;
    public $keterangan;

    public $dataPayment = [];
    public $dataDetail = [];

    public function mount($pengeluaran_pembelian_id = null)
    {
        $this->user_id = auth()->id();
        $this->tgl_pengeluaran = tanggalan_format(now('ASIA/JAKARTA'));
    }

    public function setHutangPembelian(HutangPembelian $hutangPembelian)
    {
        //
    }

    public function addLine()
    {
        //
    }

    public function render()
    {
        return view('livewire.keuangan.kasir.pengeluaran-pembelian-form');
    }
}
