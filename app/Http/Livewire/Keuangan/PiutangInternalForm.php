<?php

namespace App\Http\Livewire\Keuangan;

use App\Haramain\SistemKeuangan\SubKasir\PiutangInternalService;
use App\Models\Master\Pegawai;
use Livewire\Component;

class PiutangInternalForm extends Component
{
    protected $listeners = [
        'set_pegawai'=>'setPegawai'
    ];

    public $mode = 'create';

    public $piutang_internal_id;
    public $saldo_pegawai_id;
    public $jenis_piutang;
    public $tgl_transaksi;
    public $user_id;
    public $nominal;
    public $keterangan;

    public function mount($piutang_internal_id = null)
    {
        $this->tgl_transaksi = tanggalan_format(now('ASIA/JAKARTA'));
        $this->user_id = auth()->id();
        if ($piutang_internal_id){
            $this->mode = 'update';
            $this->piutang_internal_id = $piutang_internal_id;
            $piutangInternal = (new PiutangInternalService())->handleGetData($piutang_internal_id);
            $this->jenis_piutang = $piutangInternal->jenis_piutang;
            $this->tgl_transaksi = $piutangInternal->tgl_transaksi;
            $this->user_id = auth()->id();
            $this->nominal = $piutangInternal->nominal;
            $this->keterangan = $piutangInternal->keterangan;

            // pegawai
            $pegawai = Pegawai::find($piutangInternal->saldo_pegawai_id);
            $this->pegawai_id = $pegawai->id;
            $this->saldo_pegawai_id = $pegawai->id;
            $this->pegawai_nama = $pegawai->nama;
            $this->pegawai_saldo = ($pegawai->saldoPegawai) ? $pegawai->saldoPegawai->saldo : null;
        }
    }

    public $pegawai_id, $pegawai_nama, $pegawai_saldo;

    public function setPegawai(Pegawai $pegawai)
    {
        $this->pegawai_id = $pegawai->id;
        $this->saldo_pegawai_id = $pegawai->id;
        $this->pegawai_nama = $pegawai->nama;
        $this->pegawai_saldo = ($pegawai->saldoPegawai) ? $pegawai->saldoPegawai->saldo : null;
        $this->emit('hideModalDaftarPegawai');
    }

    public function validationForm()
    {
        return $this->validate([
            'piutang_internal_id'=>($this->mode == 'update') ? 'required' : 'nullable',
            'saldo_pegawai_id'=>'required',
            'jenis_piutang'=>'required',
            'tgl_transaksi'=>'required',
            'user_id'=>'required',
            'nominal'=>'required',
            'keterangan'=>'nullable'
        ]);
    }

    public $dataPayment = [];
    public $data;

    public function openPayment()
    {
        $this->data = $this->validationForm();
        $this->dataPayment[] = [
            'akun_id'=>null,
            'nominal'=> 0
        ];
        $this->emit('showPayment');
    }

    public function addPayment()
    {
        $this->dataPayment[] = [
            'akun_id'=>'',
            'nominal'=> 0
        ];
    }

    public function deletePayment($index)
    {
        unset($this->dataPayment[$index]);
        $this->dataPayment = array_values($this->dataPayment);
    }

    public function store()
    {
        $this->data['dataPayment'] = $this->dataPayment;
        $store = (new PiutangInternalService())->handleStore($this->data);
        if ($store['status']){
            return redirect()->to(route('kasir.piutanginternal'));
        }
        session()->flash('message', $store['keterangan']);
        return null;
    }

    public function update()
    {
        $this->data['dataPayment'] = $this->dataPayment;
        $store = (new PiutangInternalService())->handleUpdate($this->data);
        if ($store['status']){
            return redirect()->to(route('kasir.piutanginternal'));
        }
        session()->flash('message', $store['keterangan']);
        return null;
    }

    public function render()
    {
        return view('livewire.keuangan.piutang-internal-form');
    }
}
