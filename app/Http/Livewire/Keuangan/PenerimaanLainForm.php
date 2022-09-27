<?php /** @noinspection PhpLackOfCohesionInspection */

namespace App\Http\Livewire\Keuangan;

use App\Haramain\SistemKeuangan\SubKasir\PenerimaanLainService;
use App\Http\Livewire\Keuangan\Kasir\PaymentTransaksiTrait;
use App\Models\Keuangan\Akun;
use App\Models\Master\PersonRelation;
use Livewire\Component;

class PenerimaanLainForm extends Component
{
    use PaymentTransaksiTrait;

    protected $listeners = [
        'set_akun'=>'setAkun'
    ];

    public $penerimaan_lain_id;
    public $tgl_penerimaan;
    public $person_relation_id, $person_relation_nama;
    public $asal;
    public $user_id;
    public $nominal;
    public $keterangan;

    public $akun_id, $akun_nama, $akun_kode;
    public $nominal_detail;

    public $dataDetail = [];
    //public $dataPayment = [];
    public $data;

    public $mode = 'create';
    public $update = false;
    public $index;

    public function mount($penerimaan_lain_id = null)
    {
        if ($penerimaan_lain_id){
            $this->penerimaan_lain_id = $penerimaan_lain_id;
        }
    }

    public function setAkun(Akun $akun)
    {
        $this->akun_id = $akun->id;
        $this->akun_nama = $akun->deskripsi;
        $this->emit('hideModalAkun');
    }

    public function setPerson(PersonRelation $personRelation)
    {
        $this->person_relation_id = $personRelation->id;
        $this->person_relation_nama = $personRelation->nama;
    }

    protected function resetFormDetail()
    {
        $this->reset(['akun_id', 'akun_nama', 'akun_kode', 'nominal_detail']);
    }

    protected function setNominal()
    {
        $this->nominal = array_sum(array_column($this->dataDetail, 'nominal'));
    }

    public function addLine()
    {
        $this->dataDetail[] = [
            'akun_id'=>$this->akun_id,
            'akun_nama'=>$this->akun_nama,
            'akun_kode'=>$this->akun_kode,
            'nominal'=>$this->nominal_detail
        ];
        $this->setNominal();
        $this->resetFormDetail();
    }

    public function editLine($index)
    {
        $this->index = $index;
        $this->akun_id = $this->dataDetail[$index]['akun_id'];
        $this->akun_nama = $this->dataDetail[$index]['akun_nama'];
        $this->akun_kode = $this->dataDetail[$index]['akun_kode'];
        $this->nominal_detail = $this->dataDetail[$index]['nominal'];
        $this->update = true;
    }

    public function updateLine()
    {
        $index = $this->index;
        $this->dataDetail[$index]['akun_id'] = $this->akun_id;
        $this->dataDetail[$index]['akun_nama'] = $this->akun_nama;
        $this->dataDetail[$index]['akun_kode'] = $this->akun_kode;
        $this->dataDetail[$index]['nominal'] = $this->nominal_detail;
        $this->update = false;
        $this->setNominal();
        $this->resetFormDetail();
    }

    public function destroyLine($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
    }

    public function store()
    {
        $store = (new PenerimaanLainService())->handleStore($this->data);
        if ($store['status']){
            return redirect()->to(route('kasir.penerimaan.lain'));
        }
        session()->flash('message', $store['keterangan']);
        return null;
    }

    public function update()
    {
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
