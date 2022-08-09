<?php

namespace App\Http\Livewire\Jurnal;

use App\Haramain\Service\SistemKeuangan\Jurnal\PiutangPenjualanAwalService;
use App\Models\KonfigurasiJurnal;
use App\Models\Master\Customer;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanRetur;
use Livewire\Component;

class JurnalPiutangPenjualanAwalForm extends Component
{
    // dependency injection
    protected $piutangPenjualanAwalService;

    // mode (penjualan atau retur)
    public ?string $mode;
    protected ?int $jurnal_set_piutang_awal;
    public $create = true; // false for update

    /**
     * variabel for table jurnal_set_piutang_awal
     * @var
     */
    protected $jenis;
    public $piutangSetAwalId;
    public $tgl_jurnal;
    public $customer_id, $customer_nama;
    public $total_piutang;
    public $keterangan;

    /**
     * variabel for detail
     * @var
     */
    public $data_detail = [];
    public $item_id;
    public $total_bayar;

    public $modal_piutang_awal, $piutang_usaha, $ppn_penjualan, $biaya_penjualan;

    protected $listeners = [
        'set_customer'=>'setCustomer',
        'setPenjualan'=>'setTablePenjualan',
        'setPenjualanRetur'=>'setTablePenjualanRetur',
    ];

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->piutangPenjualanAwalService = new PiutangPenjualanAwalService();
    }

    public function mount($mode = null, $jurnal_set_piutang_awal = null)
    {
        $this->mode = $mode;
        $this->tgl_jurnal = tanggalan_format(now('ASIA/JAKARTA'));
        // set aku from config
        $this->modal_piutang_awal = KonfigurasiJurnal::query()->find('modal_piutang_awal')->akun_id;
        $this->piutang_usaha = KonfigurasiJurnal::query()->find('piutang_usaha')->akun_id;
        $this->ppn_penjualan = KonfigurasiJurnal::query()->find('ppn_penjualan')->akun_id;
        $this->biaya_penjualan = KonfigurasiJurnal::query()->find('biaya_penjualan')->akun_id;
        // load edit data
        if ($jurnal_set_piutang_awal){
            $this->create = false;
            $data = $this->piutangPenjualanAwalService->handleEdit($jurnal_set_piutang_awal);
            // load data
            $this->mode = $data->jenis;
            $this->piutangSetAwalId = $data->id;
            $this->tgl_jurnal = tanggalan_format($data->tgl_jurnal);
            $this->customer_id = $data->customer_id;
            $this->customer_nama = $data->customer->nama;
            $this->keterangan = $data->keterangan;
            if ($this->mode == 'penjualan'){
                foreach ($data->piutang_penjualan as $item) {
                    $this->setTablePenjualan($item->penjualan_id);
                }
                $this->total_piutang = $data->total_piutang;
            } else {
                foreach ($data->piutang_penjualan as $item) {
                    $this->setTablePenjualanRetur($item->penjualan_id);
                }
                $this->total_piutang = abs($data->total_piutang);
            }
        }
    }

    public function setTablePenjualan($penjualanId)
    {
        // validation customer
        $this->validate([
            'customer_nama'=>'required'
        ]);
        $penjualan = Penjualan::query()->find($penjualanId);
        $this->data_detail[] = [
            'item_id'=>$penjualan->id,
            'kode'=>$penjualan->kode,
            'jenis'=>null,
            'ppn'=>$penjualan->ppn,
            'biaya_lain'=>$penjualan->biaya_lain,
            'total_bayar'=>$penjualan->total_bayar
        ];
        $this->totalBayar();
    }

    public function setTablePenjualanRetur($penjualanReturId)
    {
        // validation customer
        $this->validate([
            'customer_nama'=>'required'
        ]);
        $penjualanRetur = PenjualanRetur::query()->find($penjualanReturId);
        $this->data_detail[] = [
            'item_id'=>$penjualanRetur->id,
            'kode'=>$penjualanRetur->kode,
            'jenis'=>$penjualanRetur->jenis_retur,
            'ppn'=>$penjualanRetur->ppn,
            'biaya_lain'=>$penjualanRetur->biaya_lain,
            'total_bayar'=>$penjualanRetur->total_bayar
        ];
        $this->totalBayar();
    }

    public function unsetTable($index)
    {
        unset($this->data_detail[$index]);
        $this->data_detail = array_values($this->data_detail);
        $this->totalBayar();
    }

    public function store()
    {
        $data = $this->validate([
            'tgl_jurnal'=>'required',
            'customer_id'=>'required',
            'customer_nama'=>'required',
            'total_piutang'=>'required',
            'keterangan'=>'nullable',
            'data_detail'=>'required',
            'modal_piutang_awal'=>'required',
            'piutang_usaha'=>'required'
        ]);
        // mode penjualan
        if ($this->mode == 'penjualan'){
            // validate store
            $store = $this->piutangPenjualanAwalService->handleStorePenjualan($data);
        }else{
            $store = $this->piutangPenjualanAwalService->handleStoreRetur($data);
        }
        // mode penjualan retur
        if ($store->status){
            session()->flash('storeMessage', 'Data Sukses Disimpan dengan Kode '.$store->keterangan->kode);
            return redirect()->to('keuangan/neraca/awal/piutang-penjualan');
        }
        session()->flash('storeMessage', $store->keterangan);
        return null;
    }

    public function update()
    {
        $data = $this->validate([
            'piutangSetAwalId'=>'required',
            'tgl_jurnal'=>'required',
            'customer_id'=>'required',
            'customer_nama'=>'required',
            'total_piutang'=>'required',
            'keterangan'=>'nullable',
            'data_detail'=>'required',
            'modal_piutang_awal'=>'required',
            'piutang_usaha'=>'required'
        ]);
        // mode penjualan
        if ($this->mode == 'penjualan'){
            // validate store
            $update = $this->piutangPenjualanAwalService->handleUpdatePenjualan($data);
        } else {
            $update = $this->piutangPenjualanAwalService->handleUpdateRetur($data);
        }
        // mode retur
        if ($update->status){
            session()->flash('storeMessage', 'Data Sukses Di-Update dengan Kode '.$update->keterangan->kode);
            return redirect()->to('keuangan/neraca/awal/piutang-penjualan');
        }
        session()->flash('storeMessage', $update->keterangan);
        return null;
    }

    public function setCustomer($customer_id)
    {
        $customer = Customer::query()->find($customer_id);
        $this->customer_id = $customer->id;
        $this->customer_nama = $customer->nama;
        $this->resetValidation('customer_nama');
        $this->resetErrorBag('customer_nama');
    }

    public function resetCustomer()
    {
        $this->customer_id = null;
        $this->customer_nama = null;
        // unset customer
        $this->emit('unset_customer');
        // unset data_detail
        $this->data_detail = [];
        // remove warning validation
        $this->resetValidation('customer_nama');
        $this->resetErrorBag('customer_nama');
    }

    protected function totalBayar()
    {
        $this->total_piutang = collect($this->data_detail)->sum('total_bayar');
    }

    public function render()
    {
        return view('livewire.jurnal.jurnal-piutang-penjualan-awal-form');
    }
}
