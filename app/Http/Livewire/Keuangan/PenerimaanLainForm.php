<?php /** @noinspection PhpLackOfCohesionInspection */

namespace App\Http\Livewire\Keuangan;

use App\Haramain\SistemKeuangan\SubKasir\PenerimaanLainService;
use App\Http\Livewire\Keuangan\Kasir\PaymentTransaksiTrait;
use Livewire\Component;

class PenerimaanLainForm extends Component
{
    use PaymentTransaksiTrait;
    use SetAkunTrait;
    use SetPersonTrait;
    use PenerimaanPengeluaranTrait;

    protected $listeners = [
        'set_akun'=>'setAkun'
    ];

    public $penerimaan_lain_id;
    public $tgl_penerimaan;

    public $asal;
    public $user_id;
    public $nominal;
    public $keterangan;

    public $data;

    public $mode = 'create';

    public function mount($penerimaan_lain_id = null)
    {
        $this->user_id = auth()->id();
        $this->tgl_penerimaan = tanggalan_format(now('ASIA/JAKARTA'));
        if ($penerimaan_lain_id){
            $this->mode = 'update';
            $this->penerimaan_lain_id = $penerimaan_lain_id;
            $penerimaanLain = (new PenerimaanLainService())->handleGetData($penerimaan_lain_id);
            $this->tgl_penerimaan = $penerimaanLain->tgl_penerimaan;
            $this->person_relation_id = $penerimaanLain->person_relation_id;
            $this->person_relation_nama = $penerimaanLain->personRelation->nama ?? null;
            $this->asal = $penerimaanLain->asal;
            $this->nominal = $penerimaanLain->nominal;
            $this->keterangan = $penerimaanLain->keterangan;

            foreach ($penerimaanLain->penerimaanLainDetail as $penerimaanLainDetail) {
                $this->dataDetail[] = [
                    'akun_id'=>$penerimaanLainDetail->akun_id,
                    'akun_nama'=>$penerimaanLainDetail->akun->nama,
                    'akun_kode'=>$penerimaanLainDetail->akun->kode,
                    'nominal'=>$penerimaanLainDetail->nominal
                ];
            }
        }
    }

    public function payment()
    {
        $this->data = $this->validate([
            'penerimaan_lain_id'=>($this->mode == 'update') ? 'required' : 'nullable',
            'tgl_penerimaan'=>'required',
            'person_relation_id'=>'nullable',
            'asal'=>'nullable',
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
        $store = (new PenerimaanLainService())->handleStore($this->data);
        if ($store['status']){
            return redirect()->to(route('kasir.penerimaan.lain'));
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
        $update = (new PenerimaanLainService())->handleUpdate($this->data);
        if ($update['status']){
            return redirect()->to(route('kasir.penerimaan.lain'));
        }
        session()->flash('message', $update['keterangan']);
        return null;
    }

    public function render()
    {
        return view('livewire.keuangan.penerimaan-lain-form');
    }
}
