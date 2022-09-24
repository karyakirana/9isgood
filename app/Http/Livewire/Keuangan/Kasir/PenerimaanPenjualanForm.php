<?php /** @noinspection PhpLackOfCohesionInspection */

namespace App\Http\Livewire\Keuangan\Kasir;

use App\Http\Livewire\Master\SetCustomerTrait;
use App\Models\Keuangan\KasirPenjualan;
use App\Models\Keuangan\PiutangPenjualan;
use Livewire\Component;

class PenerimaanPenjualanForm extends Component
{
    use SetCustomerTrait, PaymentTransaksiTrait;

    protected $listeners = [
        'set_customer'=>'setCustomer',
        'setPiutangPenjualan',
        'setPenjualanRetur',
        'closeFormDetail'
    ];

    public $mode = 'create';

    // penerimaan penjualan attribute
    public $penerimaan_penjualan_id;
    public $tgl_penerimaan;
    public $total_penerimaan, $total_penerimaan_rupiah;
    public $keterangan;

    // penerimaan penjualan detail attribute
    public $piutang_penjualan_id, $kode_nota;
    public $nominal_dibayar;
    public $kurang_bayar, $kurang_bayar_sebelumnya;
    public $kode_penjualan;
    public $jenis_penjualan;

    // payment attribute
    public $akun_id, $nominal;

    public $dataDetail = [];
    public $indexDetail;
    public $updateDetail = false;

    public $data;

    public function render()
    {
        return view('livewire.keuangan.kasir.penerimaan-penjualan-form')
            ->layout('layouts.metronics-811', ['minimize' => 'on']);
    }

    public function mount($penerimaan_penjualan_id = null): void
    {
        // load akun for akuntansi
        if ($penerimaan_penjualan_id){
            $penerimaan_penjualan = KasirPenjualan::query()->find($penerimaan_penjualan_id);
        }

        // finitiate data payment
        $this->dataPayment[] = [
            'akun_id'=>'',
            'nominal'=>0
        ];
    }

    public function piutangPenjualanShow()
    {
        $this->validate(['customer_nama'=>'required']);
        $this->emit('showPiutangPenjualanModal');
    }

    public function updatedDataDetail()
    {
        // listen after data detail changed
        $this->total_penerimaan = array_sum(array_column($this->dataDetail, 'nominal_dibayar'));
    }

    public function setPiutangPenjualan(PiutangPenjualan $piutangPenjualan)
    {
        $this->piutang_penjualan_id = $piutangPenjualan->id;
        $this->kurang_bayar = $piutangPenjualan->kurang_bayar;
        $this->kode_penjualan = $piutangPenjualan->piutangablePenjualan->kode;
        $this->jenis_penjualan = class_basename($piutangPenjualan->penjualan_type);
        $this->emit('showFormPiutangPenjualan');
    }

    public function closeFormDetail()
    {
        // listen for emit closeFormDetail
        // todo reset detail and piutang penjualan attribute
        $this->reset(['piutang_penjualan_id', 'kurang_bayar', 'nominal_dibayar']);
    }

    public function setDetail()
    {
        // todo set detail
        $this->dataDetail[] = [
            'piutang_penjualan_id'=>$this->piutang_penjualan_id,
            'nominal_dibayar'=>$this->nominal_dibayar,
            'kurang_bayar_sebelumnya'=>$this->kurang_bayar_sebelumnya,
            'kurang_bayar'=>$this->kurang_bayar_sebelumnya - $this->nominal_dibayar,
            'kode_penjualan'=>$this->kode_penjualan,
            'jenis_penjualan'=>$this->jenis_penjualan
        ];
    }

    public function editDetail($index)
    {
        $this->indexDetail = $index;
        $this->piutang_penjualan_id = $this->dataDetail[$index]['piutang_penjualan_id'];
        $this->nominal_dibayar = $this->dataDetail[$index]['nominal_diabayar'];
        $this->kurang_bayar_sebelumnya = $this->dataDetail[$index]['kurang_bayar_sebelumnya'];
        $this->kurang_bayar = $this->dataDetail[$index]['kurang_bayar'];
        $this->kode_penjualan = $this->dataDetail[$index]['kode_penjualan'];
        $this->jenis_penjualan = $this->dataDetail[$index]['jenis_penjualan'];
        $this->updateDetail = true;
        $this->emit('showDetail');
    }

    public function updateDetail()
    {
        $index = $this->indexDetail;
        $this->dataDetail[$index]['nominal_dibayar'] = $this->nominal_dibayar;
        $this->dataDetail[$index]['kurang_bayar_sebelumnya'] = $this->kurang_bayar_sebelumnya;
        $this->dataDetail[$index]['kurang_bayar'] = $this->kurang_bayar_sebelumnya - $this->nominal_dibayar;
        $this->updateDetail = false;
        $this->emit('closeDetail');
    }

    public function removeDetail($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
    }

    public function openPayment()
    {
        $this->data = $this->validate([
            'penerimaan_penjualan_id'=>($this->mode == 'update') ? 'required' : 'nullable',
            'tgl_penerimaan'=>'required',
            'customer_id'=>'required',
            'total_penerimaan'=>'required',
            'keterangan'=>'nullable'
        ]);
    }

    public function store()
    {
        $this->data['dataPayment'] = $this->dataPayment;
        // todo store in service
    }
}
