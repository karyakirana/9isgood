<?php /** @noinspection PhpLackOfCohesionInspection */

namespace App\Http\Livewire\Kasir;

use App\Haramain\SistemKeuangan\SubJurnal\JurnalMutasiKasService;
use App\Models\Keuangan\Akun;
use Livewire\Component;

class KasMutasiForm extends Component
{
    protected $listeners = [];

    public $kas_mutasi_id;
    public $tgl_mutasi;
    public $user_id;
    public $total_mutasi;
    public $keterangan;

    // detail attribute
    public $jenis; // masuk atau keluar
    public $akun_kas_id, $akun_kas_nama;
    public $nominal;
    public $nominal_masuk;
    public $nominal_keluar;

    // helper attribute
    public $data;
    public $dataDetail = [];
    public $mode = 'create';
    public $update = false;
    public $index;

    public function mount($kas_mutasi_id = null)
    {
        $this->tgl_mutasi = tanggalan_format(now('ASIA/JAKARTA'));
        if ($kas_mutasi_id){
            $this->mode = 'update';
            // reload data kas mutasi for update
            $this->kas_mutasi_id = $kas_mutasi_id;
            $kasMutasi = (new JurnalMutasiKasService())->handleGetData($kas_mutasi_id);
            $this->tgl_mutasi = $kasMutasi->tgl_mutasi;
            $this->keterangan = $kasMutasi->keterangan;
            foreach ($kasMutasi->kasMutasiDetail as $kasMutasiDetail){
                $akunKasNama = $this->getAkunNama($kasMutasiDetail->akun_kas_id);
                $this->dataDetail[] = [
                    'jenis'=>$kasMutasiDetail->jenis,
                    'akun_kas_id'=>$kasMutasiDetail->akun_kas_id,
                    'akun_kas_nama'=>$akunKasNama,
                    'nominal_masuk'=>$kasMutasiDetail->nominal_masuk,
                    'nominal_keluar'=>$kasMutasiDetail->nominal_keluar
                ];
            }
        }
    }

    protected function resetFormDetail()
    {
        $this->reset(['jenis', 'akun_kas_id', 'akun_kas_nama','nominal_masuk', 'nominal_keluar', 'nominal']);
    }

    protected function getAkunNama($akun_kas_id)
    {
        return Akun::find($akun_kas_id)->deskripsi;
    }

    public function addLine()
    {
        $this->akun_kas_nama = $this->getAkunNama($this->akun_kas_id);
        $this->dataDetail[] = [
            'jenis'=>$this->jenis,
            'akun_kas_id'=>$this->akun_kas_id,
            'akun_kas_nama'=>$this->akun_kas_nama,
            'nominal_masuk'=>($this->jenis == 'masuk') ? $this->nominal : null,
            'nominal_keluar'=>($this->jenis == 'keluar') ? $this->nominal : null
        ];
        $this->resetFormDetail();
    }

    public function editLine($index)
    {
        $this->index = $index;
        $this->jenis = $this->dataDetail[$index]['jenis'];
        $this->akun_kas_id = $this->dataDetail[$index]['akun_kas_id'];
        $this->akun_kas_nama = $this->dataDetail[$index]['akun_kas_nama'];
        $this->nominal = $this->dataDetail[$index]["nominal_$this->jenis"];
        $this->update = true;
    }

    public function updateLine()
    {
        $index = $this->index;
        $this->akun_kas_nama = $this->getAkunNama($this->akun_kas_id);
        $this->dataDetail[$index]['jenis'] = $this->jenis;
        $this->dataDetail[$index]['akun_kas_id'] = $this->akun_kas_id;
        $this->dataDetail[$index]['akun_kas_nama'] = $this->akun_kas_nama;
        $this->dataDetail[$index]['nominal_masuk'] = ($this->jenis == 'masuk') ? $this->nominal : null;
        $this->dataDetail[$index]['nominal_keluar'] = ($this->jenis == 'keluar') ? $this->nominal : null;
        $this->update = false;
        $this->resetFormDetail();
    }

    public function destroyLine($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
    }

    protected function validateData()
    {
        $this->total_mutasi = array_sum(array_column($this->dataDetail, 'nominal_masuk'));
        $totalDebet = array_sum(array_column($this->dataDetail, 'nominal_masuk'));
        $totalkredit = array_sum(array_column($this->dataDetail, 'nominal_keluar'));

        if ($totalDebet != $totalkredit){
            session()->flash('messages', 'Total debet tidak sama dengan total kredit');
            return null;
        }

        $this->user_id = auth()->id();

        return $this->validate([
            'kas_mutasi_id'=>($this->mode == 'update') ? 'required' : 'nullable',
            'tgl_mutasi'=>'required',
            'user_id'=>'required',
            'total_mutasi'=>'required',
            'keterangan'=>'nullable',
            'dataDetail'=>'required|array'
        ]);
    }

    public function store()
    {
        $data = $this->validateData();
        if ($data == null){
            return null;
        }
        $store = (new JurnalMutasiKasService)->handleStore($data);
        if ($store['status']){
            // true
            return redirect()->to(route('kasir.mutasi'));
        }
        session()->flash('message', $store['keterangan']);
        return null;
    }

    public function update()
    {
        $data = $this->validateData();
        if ($data == null){
            return null;
        }
        $store = (new JurnalMutasiKasService)->handleUpdate($data);
        if ($store['status']){
            // true
            return redirect()->to(route('kasir.mutasi'));
        }
        session()->flash('message', $store['keterangan']);
        return null;
    }

    public function render()
    {
        return view('livewire.kasir.kas-mutasi-form');
    }
}
