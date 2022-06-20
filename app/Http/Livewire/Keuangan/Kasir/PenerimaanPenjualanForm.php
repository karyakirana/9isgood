<?php

namespace App\Http\Livewire\Keuangan\Kasir;

use App\Haramain\Traits\LivewireTraits\SetCustomerTraits;
use App\Models\Keuangan\Akun;
use App\Models\Keuangan\KasirPenjualan;
use App\Models\Keuangan\SaldoPiutangPenjualan;
use App\Models\Master\Customer;
use App\Models\Penjualan\Penjualan;
use Livewire\Component;

class PenerimaanPenjualanForm extends Component
{
    use SetCustomerTraits, SetCustomerTraits;

    protected $listeners = [
        'set_customer'=>'customer',
        'setPenjualan'
    ];

    public $update= false;

    public $penerimaan_penjualan_id;
    public $customer_id;
    public $total_nota, $total_tunai, $total_piutang;

    public $akun_kas_data, $akun_kas, $akun_kas_nominal;
    public $akun_piutang_data, $akun_piutang, $akun_piutang_nominal;
    public $akun_biaya_data, $akun_ppn_data;

    public $detail = [], $penjualan_id;
    public $kode_penjualan;
    public $total_penjualan, $akun_biaya, $biaya_lain, $akun_ppn, $ppn, $total_bayar;
    public $total_penjualan_rupiah, $total_bayar_rupiah;

    // saldo piutang
    public $saldo_piutang;

    public function render()
    {
        return view('livewire.keuangan.kasir.penerimaan-penjualan-form');
    }

    public function mount($penerimaan_penjualan_id = null)
    {
        $this->akun_kas_data = Akun::query()
            ->whereRelation('akunTipe', 'deskripsi', 'like', '%kas%')
            ->get();
        $this->akun_piutang_data = Akun::query()->whereRelation('akunTipe', 'deskripsi', 'like', '%piutang usaha%')->get();
        $this->akun_biaya_data = Akun::query()
            ->whereRelation('akunTipe', 'deskripsi', 'like', '%piutang usaha%')
            ->orWhereRelation('akunTipe', 'deskripsi', 'like', '%Hutang Jangka Pendek%')
            ->get();
        $this->akun_ppn_data = Akun::query()
            ->whereRelation('akunTipe', 'deskripsi', 'like', '%Hutang Jangka Menengah%')
            ->get();

        if ($penerimaan_penjualan_id){
            $penerimaan_penjualan = KasirPenjualan::query()->find($penerimaan_penjualan_id);
            $this->penerimaan_penjualan_id = $penerimaan_penjualan_id;
            $this->total_nota = $penerimaan_penjualan->total_nota;
            $this->total_tunai = $penerimaan_penjualan->total_tunai;
            $this->total_piutang = $penerimaan_penjualan->total_piutang;
            $this->setCustomer($penerimaan_penjualan->customer_id);

            $this->detail = $penerimaan_penjualan->kasir_penjualan_detail();
        }
    }

    public function customer(Customer $customer)
    {
        $this->setCustomer($customer);

        // get_saldo_piutang_penjualan
        $this->saldo_piutang = SaldoPiutangPenjualan::query()
                ->firstWhere('customer_id', $customer->id)->saldo ?? 0;
    }

    public function setPenjualan(Penjualan $penjualan)
    {
        $this->penjualan_id = $penjualan->id;
        $this->kode_penjualan = $penjualan->kode;
        $this->biaya_lain = ($penjualan->biaya_lain > 0) ? $penjualan->biaya_lain : null;
        $this->ppn =( $penjualan->ppn > 0) ? $penjualan->ppn : null;
        $this->total_penjualan = $penjualan->total_bayar - ($penjualan->biaya_lain ?? 0) - ($penjualan->ppn ?? 0);
        $this->total_penjualan_rupiah = rupiah_format($this->total_penjualan);
        $this->total_bayar = $penjualan->total_bayar;
        $this->total_bayar_rupiah = rupiah_format($this->total_bayar);
    }

    public function resetForm()
    {
        $this->reset([
            ''
        ]);
    }

    public function addLine()
    {
        //
    }

    public function removeLine()
    {
        //
    }

    public function store()
    {
        //
    }

    public function update()
    {
        //
    }
}
