<?php

namespace App\Http\Livewire\Keuangan;

use App\Models\KonfigurasiJurnal;
use App\Models\Master\Customer;
use App\Models\Penjualan\PenjualanRetur;
use Livewire\Component;

class JurnalSetPiutangReturForm extends Component
{
    public function render()
    {
        return view('livewire.keuangan.jurnal-set-piutang-retur-form');
    }

    protected $listeners = [];

    public $mode = 'create';

    // var master
    public $jurnal_set_piutang_retur_id;
    public $tgl_jurnal, $customer_id, $customer_nama, $total_piutang;

    // var detail
    public $data_detail = [];

    // var jurnal transaksi
    public $modal_piutang_awal;
    public $piutang_usaha;

    public function mount($jurnalSetPiutangRetur = null)
    {
        $this->tgl_jurnal = tanggalan_format(now('ASIA/JAKARTA'));
    }

    public function setAkunJurnal()
    {
        $this->modal_piutang_awal = KonfigurasiJurnal::query()->find('modal_piutang_awal')->akun_id;
        $this->piutang_usaha = KonfigurasiJurnal::query()->find('piutang_usaha')->akun_id;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer_id = $customer->id;
        $this->customer_nama = $customer->nama;
    }

    public function setPenjualanRetur(PenjualanRetur $penjualanRetur)
    {
        $this->data_detail[] = [
            'retur_id'=>$penjualanRetur->id,
            'retur_kode'=>$penjualanRetur->kode,
            'retur_total_bayar'=>$penjualanRetur->total_bayar,
            'retur_biaya_lain'=>$penjualanRetur->biaya_lain,
            'retur_ppn'=>$penjualanRetur->ppn,
            'retur_total'=>$penjualanRetur->total_bayar - (int)$penjualanRetur->biaya_lain - (int)$penjualanRetur->ppn,
        ];
    }
}
