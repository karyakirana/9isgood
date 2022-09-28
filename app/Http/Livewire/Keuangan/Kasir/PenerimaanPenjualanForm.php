<?php /** @noinspection PhpLackOfCohesionInspection */

namespace App\Http\Livewire\Keuangan\Kasir;

use App\Haramain\SistemKeuangan\SubKasir\PenerimaanPenjualanService;
use App\Http\Livewire\Master\SetCustomerTrait;
use Livewire\Component;

class PenerimaanPenjualanForm extends Component
{
    use SetCustomerTrait, PaymentTransaksiTrait, PenerimaanPenjualanHelperTrait;

    protected $listeners = [
        'set_customer'=>'setCustomer',
        'setPiutangPenjualan',
        'setPenjualanRetur',
        'closeFormDetail'
    ];

    public $mode = 'create';
    public $update = false;
    public $indexDetail;

    // penerimaan penjualan attribute
    public $penerimaan_penjualan_id;
    public $tgl_penerimaan;
    public $user_id;
    public $total_penerimaan, $total_penerimaan_rupiah;
    public $keterangan;

    // payment attribute
    public $akun_id, $nominal;

    public $dataDetail = [];

    public $data;

    public function mount($penerimaan_penjualan_id = null): void
    {
        $this->user_id = auth()->id();
        $this->tgl_penerimaan = tanggalan_format(now('ASIA/JAKARTA'));
        // load akun for akuntansi
        if ($penerimaan_penjualan_id){
            $this->mode = 'update';
            $penerimaan_penjualan = (new PenerimaanPenjualanService())->handleGetData($penerimaan_penjualan_id);
            $this->penerimaan_penjualan_id = $penerimaan_penjualan_id;
            $this->tgl_penerimaan = $penerimaan_penjualan->tgl_penerimaan;
            $this->customer_id = $penerimaan_penjualan->customer_id;
            $this->customer_nama = $penerimaan_penjualan->customer->nama;
            $this->customer_saldo = $penerimaan_penjualan->customer->saldoPiutangPenjualan->saldo;
            $this->total_penerimaan = $penerimaan_penjualan->total_penerimaan;
            $this->keterangan = $penerimaan_penjualan->keterangan;

            foreach ($penerimaan_penjualan->penerimaanPenjualanDetail as $detail){
                $piutangPenjualan = $detail->piutangPenjualan;
                $this->dataDetail[] = [
                    'piutang_penjualan_id'=>$detail->piutang_penjualan_id,
                    'status_bayar'=>$piutangPenjualan->status_bayar,
                    'nominal_dibayar'=>$detail->nominal_dibayar,
                    'kurang_bayar'=>$detail->kurang_bayar,
                    'kurang_bayar_sebelumnya'=>$detail->kurang_bayar + $detail->nominal_dibayar,
                    'kode_penjualan'=>$piutangPenjualan->piutangablePenjualan->kode,
                    'jenis_penjualan'=>class_basename($piutangPenjualan->penjualan_type)
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

    public function piutangPenjualanShow()
    {
        $this->validate(['customer_nama'=>'required']);
        $this->emit('refresh_customer', $this->customer_id);
        $this->emit('showPiutangPenjualanModal');
    }

    public function openPayment()
    {
        $this->total_penerimaan = $this->total_dibayar;
        $this->data = $this->validate([
            'penerimaan_penjualan_id'=>($this->mode == 'update') ? 'required' : 'nullable',
            'tgl_penerimaan'=>'required',
            'customer_id'=>'required',
            'total_penerimaan'=>'required',
            'keterangan'=>'nullable',
            'dataDetail'=>'required'
        ]);
        $this->emit('showPayment');
    }

    public function store()
    {
        $this->data['dataPayment'] = $this->dataPayment;
        $store = (new PenerimaanPenjualanService())->handleStore($this->data);
        if ($store['status']){
            return redirect()->to(route('kasir.penerimaan.penjualan'));
        }
        session()->flash('message', $store['keterangan']);
        return null;
    }

    public function update()
    {
        $this->data['dataPayment'] = $this->dataPayment;
        $store = (new PenerimaanPenjualanService())->handleUpdate($this->data);
        if ($store['status']){
            return redirect()->to(route('kasir.penerimaan.penjualan'));
        }
        session()->flash('message', $store['keterangan']);
        return null;
    }

    public function render()
    {
        return view('livewire.keuangan.kasir.penerimaan-penjualan-form')
            ->layout('layouts.metronics-811', ['minimize' => 'on']);
    }
}
