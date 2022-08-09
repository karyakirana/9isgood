<?php

namespace App\Http\Livewire\Keuangan\Kasir;

use App\Haramain\Repository\Jurnal\PiutangPenjualanRepo;
use App\Models\Keuangan\JurnalSetPiutangAwal;
use App\Models\KonfigurasiJurnal;
use App\Models\Master\Customer;
use App\Models\Penjualan\Penjualan;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class PiutangPenjualanForm extends Component
{
    public function render()
    {
        return view('livewire.keuangan.kasir.piutang-penjualan-form');
    }

    protected $listeners = [
        'setPenjualan',
        'set_customer'=>'setCustomer'
    ];
    public $setPiutangId, $mode = 'create';

    // var customer
    public $customer_id, $customer_nama;
    public $tgl_jurnal, $keterangan;

    // var detail
    public $data_detail = [];

    // var jurnal transaksi
    public $modal_piutang_awal; // debet
    public $piutang_usaha; // kredit
    public $ppn_penjualan; // kredit
    public $biaya_penjualan; // kredit

    // sum of total_bayar
    public $penjualan_sum_total_bayar;

    public function mount($jurnalSetPiutangId = null)
    {
        $this->tgl_jurnal = tanggalan_format(now('ASIA/JAKARTA'));
        $this->setAkunJurnal();

        if($jurnalSetPiutangId){
            // load data piutang penjualan
            $this->mode = 'update';
            $jurnalSetPiutang = JurnalSetPiutangAwal::query()->find($jurnalSetPiutangId);
            $this->setPiutangId = $jurnalSetPiutang->id;
            $this->customer_id = $jurnalSetPiutang->customer_id;
            $this->customer_nama = $jurnalSetPiutang->customer->nama;
            $this->tgl_jurnal = tanggalan_format($jurnalSetPiutang->tgl_jurnal);
            $this->keterangan = $jurnalSetPiutang->keterangan;
            foreach ($jurnalSetPiutang->piutang_penjualan as $item) {
                $this->data_detail [] = [
                    'penjualan_id'=>$item->penjualan_id,
                    'penjualan_kode'=>$item->penjualan->kode,
                    'penjualan_total_bayar'=>$item->penjualan->total_bayar,
                    'penjualan_biaya_lain'=>$item->penjualan->biaya_lain,
                    'penjualan_ppn'=>$item->penjualan->ppn,
                    'penjualan_total'=>$item->penjualan->total_bayar - (int) $item->penjualan->biaya_lain - (int) $item->penjualan->ppn
                ];
            }
            $this->penjualan_sum_total_bayar = array_sum(array_column($this->data_detail, 'penjualan_total_bayar'));
        }
    }

    protected function setAkunJurnal()
    {
        // set aku from config
        $this->modal_piutang_awal = KonfigurasiJurnal::query()->find('modal_piutang_awal')->akun_id;
        $this->piutang_usaha = KonfigurasiJurnal::query()->find('piutang_usaha')->akun_id;
        $this->ppn_penjualan = KonfigurasiJurnal::query()->find('ppn_penjualan')->akun_id;
        $this->biaya_penjualan = KonfigurasiJurnal::query()->find('biaya_penjualan')->akun_id;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer_id = $customer->id;
        $this->customer_nama = $customer->nama;
    }

    public function validateButton()
    {
        $this->validate([
            'customer_id'=>'required'
        ]);
    }

    public function setPenjualan(Penjualan $penjualan)
    {
        $this->data_detail [] = [
            'penjualan_id'=>$penjualan->id,
            'penjualan_kode'=>$penjualan->kode,
            'penjualan_total_bayar'=>$penjualan->total_bayar,
            'penjualan_biaya_lain'=>$penjualan->biaya_lain,
            'penjualan_ppn'=>$penjualan->ppn,
            'penjualan_total'=>$penjualan->total_bayar - (int) $penjualan->biaya_lain - (int) $penjualan->ppn
        ];
    }

    public function destroyLine($index)
    {
        // remove line transaksi
        unset($this->data_detail[$index]);
        $this->data_detail = array_values($this->data_detail);
    }

    public function store()
    {
        $this->penjualan_sum_total_bayar = array_sum(array_column($this->data_detail, 'penjualan_total_bayar'));
        $data = $this->validate([
            'setPiutangId'=>'nullable',
            'customer_id'=>'required',
            'tgl_jurnal'=>'required',
            'keterangan'=>'nullable',
            'data_detail'=>'required',
            'modal_piutang_awal'=>'required',
            'piutang_usaha'=>'required',
            'ppn_penjualan'=>'required',
            'biaya_penjualan'=>'required',
            'penjualan_sum_total_bayar'=>'required'
        ]);

        DB::beginTransaction();
        try {
            $piutang_penjualan = (new PiutangPenjualanRepo())->store((object)$data);
            DB::commit();
            return redirect()->to(route('penjualan.piutang'));
        } catch (ModelNotFoundException $e){
            session()->flash('message', $e);
            DB::rollBack();
            return null;
        }
    }

    public function update()
    {
        $this->penjualan_sum_total_bayar = array_sum(array_column($this->data_detail, 'penjualan_total_bayar'));
        $data = $this->validate([
            'setPiutangId'=>'nullable',
            'customer_id'=>'required',
            'tgl_jurnal'=>'required',
            'keterangan'=>'nullable',
            'data_detail'=>'required',
            'modal_piutang_awal'=>'required',
            'piutang_usaha'=>'required',
            'ppn_penjualan'=>'required',
            'biaya_penjualan'=>'required',
            'penjualan_sum_total_bayar'=>'required'
        ]);

        DB::beginTransaction();
        try {
            $piutang_penjualan = (new PiutangPenjualanRepo())->update((object)$data);
            DB::commit();
            return redirect()->to(route('penjualan.piutang'));
        } catch (ModelNotFoundException $e){
            session()->flash('message', $e);
            DB::rollBack();
        }
    }
}
