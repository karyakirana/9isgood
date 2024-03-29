<?php

namespace App\Http\Livewire\Keuangan\Kasir;

use App\Haramain\SistemKeuangan\SubKasir\PengeluaranPembelianService;
use App\Haramain\Traits\LivewireTraits\SetSupplierTraits;
use Livewire\Component;

class PengeluaranPembelianForm extends Component
{
    use SetSupplierTraits, PengeluaranPembelianHelperTrait;

    protected $listeners = [
        'setHutangPembelian',
        'setSupplier'
    ];

    public $pengeluaran_pembelian_id;
    public $tgl_pengeluaran;
    public $jenis;
    public $user_id;
    public $total_pengeluaran;
    public $keterangan;
    public $data;

    // attribute payment
    public $mode = 'create';

    public function mount($pengeluaran_pembelian_id = null)
    {
        $this->user_id = auth()->id();
        $this->tgl_pengeluaran = tanggalan_format(now('ASIA/JAKARTA'));
        $this->jenis = 'BLU';

        if ($pengeluaran_pembelian_id)
        {
            $this->mode = 'update';
            $this->pengeluaran_pembelian_id = $pengeluaran_pembelian_id;
            $pengeluaranPembelian = (new PengeluaranPembelianService())->handleGetData($pengeluaran_pembelian_id);
            $this->supplier_id = $pengeluaranPembelian->supplier_id;
            $this->supplier_nama = $pengeluaranPembelian->supplier->nama;
            $this->supplier_saldo = $pengeluaranPembelian->supplier->saldoHutang->saldo ?: null;
            $this->tgl_pengeluaran = $pengeluaranPembelian->tgl_pengeluaran;
            $this->jenis = $pengeluaranPembelian->jenis;
            $this->total_pengeluaran = $pengeluaranPembelian->total_pengeluaran;
            $this->keterangan = $pengeluaranPembelian->keterangan;

            foreach ($pengeluaranPembelian->pengeluaranPembelianDetail as $pengeluaranPembelianDetail) {
                $hutangPembelian = $pengeluaranPembelianDetail->hutangPembelian;
                $this->dataDetail[] = [
                    'hutang_pembelian_id'=>$pengeluaranPembelianDetail->hutang_pembelian_id,
                    'status_bayar'=>$hutangPembelian->status_bayar,
                    'nominal_dibayar'=>$pengeluaranPembelianDetail->nominal_dibayar,
                    'kurang_bayar'=>$pengeluaranPembelianDetail->kurang_bayar,
                    'kode_pembelian'=>$hutangPembelian->hutangablePembelian->kode,
                    'jenis_pembelian'=>class_basename($hutangPembelian->pembelian_type),
                    'pembelian'=> $hutangPembelian->hutangablePembelian->jenis
                ];
            }

            // helper
            $this->total_dibayar = array_sum(array_column($this->dataDetail, 'nominal_dibayar'));
        }
    }

    public function hydrate()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function addHutang()
    {
        $this->validate(['supplier_nama'=>'required']);
        $this->emit('showModalHutangPembelian');
    }

    protected function formValidate()
    {
        $this->total_pengeluaran = array_sum(array_column($this->dataDetail, 'nominal_dibayar'));
        return $this->validate([
            'pengeluaran_pembelian_id'=>($this->mode == 'update') ? 'required' : 'nullable',
            'tgl_pengeluaran'=>'required',
            'jenis'=>'required',
            'supplier_id'=>'required',
            'user_id'=>'required',
            'total_pengeluaran'=>'required|integer',
            'keterangan'=>'nullable',
            'dataDetail'=>'required|array',
        ]);
    }

    public function store()
    {
        //dd($this->dataPayment[0]['akun_id']);
        $this->validate([
            'dataPayment.*.akun_id'=>'required',
            'dataPayment.*.nominal'=>'required|numeric|min:0|not_in:0'
        ]);
        $this->data['dataPayment'] = $this->dataPayment;

        $store = (new PengeluaranPembelianService())->handleStore($this->data);
        if ($store['status']){
            return redirect()->to(route('kasir.pengeluaran.pembelian'));
        }
        session()->flash('message', $store['keterangan']);
        return null;
    }

    public function update()
    {
        $this->validate([
            'dataPayment.*.akun_id'=>'required',
            'dataPayment.*.nominal'=>'required|numeric|min:0|not_in:0'
        ]);
        $this->data['dataPayment'] = $this->dataPayment;

        $store = (new PengeluaranPembelianService())->handleUpdate($this->data);
        if ($store['status']){
            return redirect()->to(route('kasir.pengeluaran.pembelian'));
        }
        session()->flash('message', $store['keterangan']);
        return null;
    }

    public function render()
    {
        return view('livewire.keuangan.kasir.pengeluaran-pembelian-form');
    }
}
