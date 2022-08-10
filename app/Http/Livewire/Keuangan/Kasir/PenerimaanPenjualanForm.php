<?php

namespace App\Http\Livewire\Keuangan\Kasir;

use App\Models\Keuangan\KasirPenjualan;
use App\Models\Keuangan\PiutangPenjualan;
use App\Models\Keuangan\SaldoPiutangPenjualan;
use App\Models\KonfigurasiJurnal;
use App\Models\Master\Customer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PenerimaanPenjualanForm extends Component
{
    protected $listeners = [
        'set_customer'=>'setCustomer',
        'setPenjualan',
        'setPenjualanRetur'
    ];

    public $mode = 'create';
    public $update = false;

    // customer
    public $customer_id, $customer_nama;

    // saldo piutang
    public $saldo_piutang;

    // penerimaan penjualan
    public $penerimaan_penjualan_id;
    public $tgl_penerimaan;
    public $akun_kas_id, $nominal_kas = 0;
    public $akun_piutang_id, $nominal_piutang;

    // penerimaan penjualan detail
    public $piutang_penjualan_id;
    public $akun_biaya_lain, $nominal_biaya_lain;
    public $akun_ppn, $nominal_ppn;
    public $tagihan; // total_bayar penjualan atau retur penjualan
    public $nominal_bayar; // yang akan dibayarkan
    public $kurang_bayar; // sisa yang belum dibayarkan

    // piutang penjualan update
    public $jenis; // retur or penjualan
    public $status_bayar; // lunas, kurang, belum

    // interface

    public $data_detail = [];
    public $id_nota;
    public $tgl_nota;
    public $kode_nota;

    public function render(): Factory|View|Application
    {
        return view('livewire.keuangan.kasir.penerimaan-penjualan-form')
            ->layout('layouts.metronics-811', ['minimize' => 'on']);
    }

    public function mount($penerimaan_penjualan_id = null): void
    {
        // load akun for akuntansi
        $this->setAkun();
        if ($penerimaan_penjualan_id){
            $penerimaan_penjualan = KasirPenjualan::query()->find($penerimaan_penjualan_id);
        }
    }

    public function removeLine($index)
    {
        // remove line transaksi
        unset($this->detail[$index]);
        $this->detail = array_values($this->detail);
    }

    public function store()
    {
        //
    }

    public function update()
    {
        //
    }

    protected function setAkun()
    {
        $this->akun_piutang_id = KonfigurasiJurnal::query()->find('piutang_usaha')->akun_id;
    }

    public function setCustomer($customer_id)
    {
        $customer = Customer::query()->find($customer_id);
        $this->customer_id = $customer->id;
        $this->customer_nama = $customer->nama;

        // saldo piutang
        $this->saldo_piutang = SaldoPiutangPenjualan::query()->find($customer_id)->saldo;
    }

    public function piutangPenjualanShow()
    {
        $this->validate(['customer_nama'=>'required']);
        $this->emit('showPiutangPenjualanModal');
    }

    public function setPiutangPenjualan($piutangPenjualanId)
    {
        $piutangPenjualan = PiutangPenjualan::query()->find($piutangPenjualanId);
        $piutangablePenjualan = $piutangPenjualan->piutangablePenjualan();
        $this->kurang_bayar = $piutangablePenjualan->kurang_bayar;
        $this->id_nota = $piutangablePenjualan->id_nota;
    }

    public function addLine()
    {
        $this->data_detail[] = [
            'piutang_penjualan_id'=>$this->piutang_penjualan_id,
            'status_bayar'=>$this->setStatusBayar(),
            'kurang_bayar'=>$this->kurang_bayar,
            'total_bayar'=>$this->nominal_bayar,
            // for item detail
            'id_nota'=>$this->id_nota,
            'kode_nota'=>$this->kode_nota,
            // for jurnal
            'akun_biaya_lain'=>$this->akun_ppn,
            'nominal_biaya_lain'=>$this->nominal_biaya_lain,
            'akun_ppn'=>$this->akun_ppn,
            'nominal_ppn'=>$this->nominal_ppn,
        ];
        $this->addNominalKas($this->nominal_bayar);
    }

    protected function setStatusBayar()
    {
        if ($this->status_bayar == 0){
            return 'lunas';
        } elseif ($this->status_bayar > 0 || $this->status_bayar < 0){
            return 'kurang_bayar';
        } else {
            return null;
        }
    }

    protected function addNominalKas($nominalKas)
    {
        $this->nominal_kas += $nominalKas;
    }
}
