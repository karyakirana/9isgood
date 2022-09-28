<?php

namespace App\Http\Livewire\Keuangan;

use App\Haramain\SistemKeuangan\SubKasir\PengeluaranLainService;
use App\Http\Livewire\Keuangan\Kasir\PaymentTransaksiTrait;
use Livewire\Component;

class PengeluaranLainForm extends Component
{
    use PaymentTransaksiTrait;
    use SetAkunTrait;
    use SetPersonTrait;
    use PenerimaanPengeluaranTrait;

    protected $listeners = [
        'set_akun'=>'setAkun'
    ];

    public $pengeluaran_lain_id;
    public $tgl_pengeluaran;
    public $tujuan;
    public $user_id;
    public $keterangan;

    public $mode = 'create';

    public $data;

    public function mount($pengeluaran_lain_id = null)
    {
        $this->user_id = auth()->id();
        $this->tgl_pengeluaran = tanggalan_format(now('ASIA/JAKARTA'));
        if ($pengeluaran_lain_id){
            $this->mode = 'update';
            $this->pengeluaran_lain_id = $pengeluaran_lain_id;
            $pengeluaranLain = (new PengeluaranLainService())->handleGetData($pengeluaran_lain_id);
            $this->tgl_pengeluaran = $pengeluaranLain->tgl_pengeluaran;
            $this->person_relation_id = $pengeluaranLain->person_relation_id;
            $this->person_relation_nama = $pengeluaranLain->personRelation->nama ?? null;
            $this->tujuan = $pengeluaranLain->tujuan;
            $this->nominal = $pengeluaranLain->nominal;
            $this->keterangan = $pengeluaranLain->keterangan;

            foreach ($pengeluaranLain->pengeluaranLainDetail as $pengeluaranLainDetail) {
                $this->dataDetail [] = [
                    'akun_id'=>$pengeluaranLainDetail->akun_id,
                    'akun_nama'=>$pengeluaranLainDetail->akun->nama,
                    'akun_kode'=>$pengeluaranLainDetail->akun->kode,
                    'nominal'=>$pengeluaranLainDetail->nominal
                ];
            }
        }
    }

    public function payment()
    {
        $this->data = $this->validate([
            'pengeluaran_lain_id'=>($this->mode == 'update') ? 'required' : 'nullable',
            'tgl_pengeluaran'=>'required',
            'person_relation_id'=>'nullable',
            'tujuan'=>'nullable',
            'user_id'=>'required',
            'nominal'=>'required',
            'keterangan'=>'nullable',

            'dataDetail'=>'required|array'
        ]);
        $this->emit('showPayment');
    }

    public function store()
    {
        $this->validate([
            'dataPayment.*.akun_id'=>'required',
            'dataPayment.*.nominal'=>'required|gt:0'
        ]);
        $this->data['dataPayment'] = $this->dataPayment;
        $store = (new PengeluaranLainService())->handleStore($this->data);
        if ($store['status']){
            return redirect()->to(route('pengeluaran.lain'));
        }
        session()->flash('message', $store['keterangan']);
        return null;
    }

    public function update()
    {
        $this->validate([
            'dataPayment.*.akun_id'=>'required',
            'dataPayment.*.nominal'=>'required|gt:0'
        ]);
        $this->data['dataPayment'] = $this->dataPayment;
        $store = (new PengeluaranLainService())->handleUpdate($this->data);
        if ($store['status']){
            return redirect()->to(route('pengeluaran.lain'));
        }
        session()->flash('message', $store['keterangan']);
        return null;
    }

    public function render()
    {
        return view('livewire.keuangan.pengeluaran-lain-form');
    }
}
