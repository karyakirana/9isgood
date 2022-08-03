<?php

namespace App\Http\Livewire\Keuangan\Kasir;

use App\Haramain\Service\SistemKeuangan\Neraca\PiutangPenjualanReturService;
use App\Models\KonfigurasiJurnal;
use App\Models\Master\Customer;
use App\Models\Penjualan\PenjualanRetur;
use Livewire\Component;

class PiutangPenjualanReturForm extends Component
{
    public function render()
    {
        return view('livewire.keuangan.kasir.piutang-penjualan-retur-form');
    }

    protected $piutangPenjualanReturService;

    protected $listeners = [
        'set_customer',
        'setPenjualanRetur'
    ];

    public $piutangReturId;

    public array $data_detail = [];
    public $customer_id, $customer_nama;

    public $tgl_jurnal;

    public $modal_piutang_awal, $piutang_usaha, $ppn_penjualan, $biaya_penjualan;

    public $retur_id, $retur_jenis, $retur_kode, $retur_ppn, $retur_biaya_lain, $retur_total_bayar;
    public $total_bayar, $total_bayar_rupiah;

    public $mode = 'create';
    public $type = 'retur';

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->piutangPenjualanReturService = new PiutangPenjualanReturService();
    }

    public function mount($piutangReturId = null)
    {
                $this->tgl_jurnal = tanggalan_format(now('ASIA/JAKARTA'));
        $this->setForJurnalTransaksi();
    }

    protected function setForJurnalTransaksi()
    {
        // set aku from config
        $this->modal_piutang_awal = KonfigurasiJurnal::query()->find('modal_piutang_awal')->akun_id;
        $this->piutang_usaha = KonfigurasiJurnal::query()->find('piutang_usaha')->akun_id;
        $this->ppn_penjualan = KonfigurasiJurnal::query()->find('ppn_penjualan')->akun_id;
        $this->biaya_penjualan = KonfigurasiJurnal::query()->find('biaya_penjualan')->akun_id;
    }

    public function set_customer($customerId)
    {
        $customer = Customer::query()->find($customerId);
        $this->customer_id = $customer->id;
        $this->customer_nama = $customer->nama;
    }

    public function setPenjualanRetur($id)
    {
        $penjualanRetur = PenjualanRetur::query()->find($id);
        $this->data_detail[] = [
            'retur_id'=>$penjualanRetur->id,
            'kode'=>$penjualanRetur->kode,
            'jenis'=>$penjualanRetur->jenis_retur,
            'ppn'=>$penjualanRetur->ppn,
            'biaya_lain'=>$penjualanRetur->biaya_lain,
            'total_bayar'=>$penjualanRetur->total_bayar
        ];
    }

    public function unsetRowTable($index)
    {
        unset($this->data_detail[$index]);
        $this->data_detail = array_values($this->data_detail);
    }

    public function store()
    {
        $data = $this->validate($this->piutangPenjualanReturService->handleValidation);
        dd($data);
        $store = $this->piutangPenjualanReturService->handleStore($data);
        if ($store->status){
            // redirect
            return redirect()->to('/keuangan/neraca/awal/piutang-penjualan');
        } else {
            // session flash
            session()->flash('storeMessage', $store->keterangan);
        }
    }

    public function update()
    {
        //
    }
}
