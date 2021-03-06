<?php

namespace App\Http\Livewire\Keuangan\Kasir;

use App\Haramain\Traits\LivewireTraits\SetCustomerTraits;
use App\Models\Keuangan\Akun;
use App\Models\Keuangan\KasirPenjualan;
use App\Models\Keuangan\SaldoPiutangPenjualan;
use App\Models\Master\Customer;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanRetur;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use phpDocumentor\Reflection\Types\Integer;

class PenerimaanPenjualanForm extends Component
{
    use SetCustomerTraits;

    /**
     * Goals :
     * Melakukan pembayaran tunai atau yg lain untuk membayar piutang
     * membayar piutang berdasarkan nota penjualan
     * ketika jumlah nota kurang dari total nota yang terbayarkan,
     * maka akan menandai salah satu nota dengan status kurang bayar
     *
     * dibutuhkan :
     * daftar piutang penjualan
     * daftar retur piutang penjualan (digenerate terlebih dahulu retur mengurangi piutang)
     * unit test
     */

    /**
     * listener for emit
     * @var string[]
     */
    protected $listeners = [
        'set_customer'=>'customer',
        'setPenjualan',
        'setPenjualanRetur'
    ];

    /**
     * status form update or create
     * @var string
     */
    public $mode = 'create';

    /**
     * status mode update or create detail
     * @var bool
     */
    public $update= false;

    /**
     * penerimaan penjualan id (berguna untuk update)
     * @var
     */
    public $penerimaan_penjualan_id; // if needed

    /**
     * akun_kas untuk field akun kas
     * akun_piutang untuk field akun piutang
     * @var
     */
    public $akun_kas;

    /**
     * $nominal_tunai untuk field nominal kas
     * $nominal_piutang untuk field nominal piutang
     * @var
     */
    public $nominal_kas;

    /**
     * array untuk menampilkan tabel item-item penerimaan penjualan
     * @var
     */
    public $detail =[];

    /**
     * index for an array
     * @var
     */
    public $index;

    /**
     * id penjualan
     * @var
     */
    public $penjualan_id;

    /**
     * kode penjualan
     * @var
     */
    public $penjualan_kode;

    /**
     * @var
     */
    public $penjualan_type;

    /**
     * total penjualan sebelum biaya dan ppn
     * @var int|null
     */
    public $total_penjualan;

    /**
     * id akun untuk biaya
     * nominal biaya
     * @var
     */
    public $akun_biaya, $biaya_lain;

    /**
     * id akun untuk ppn
     * nominal ppn
     * @var
     */
    public $akun_ppn, $ppn;

    /**
     * total_bayar adalah jumlah total_penjualan + biaya_lain + ppn
     * @var
     */
    public $total_bayar;

    /**
     * rekayasa tampilan penggunaan format rupiah
     * @var
     */
    public $total_penjualan_rupiah, $total_bayar_rupiah;

    /**
     * Saldo piutang customer
     * @var int|null
     */
    public $saldo_piutang;

    /**
     * $total_nota seluruh jumlah nota yang dibayar
     * $total_tunai seluruh jumlah yang dibayarkan
     * $total_piutang seluruh jumlah piutang yang belum dibayar
     * $sisa_piutang adalah selisih dari piutang dan nominal kas
     * @var int|null
     */
    public ?int $total_nota, $total_tunai, $total_piutang, $sisa_piutang;

    /**
     * variabel manipulasi dalam bentuk rupiah (string) dari integer
     * @var
     */
    public $totalPiutangRupiah, $totalSisaPiutangRupiah;

    /**
     * variabel for table footer
     * @var
     */
    public $totalTagihan, $dibayar, $sisa;

    /**
     * @return Factory|View|Application
     */
    public function render(): Factory|View|Application
    {
        return view('livewire.keuangan.kasir.penerimaan-penjualan-form')
            ->layout('layouts.metronics-811', ['minimize'=>'on']);
    }

    /**
     * @param null $penerimaan_penjualan_id
     */
    public function mount($penerimaan_penjualan_id = null): void
    {
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

    /**
     * set data customer
     * @param $customer
     */
    public function customer($customer):void
    {
        // dd($customer);
        $this->setCustomer($customer);

        // get_saldo_piutang_penjualan
        $this->saldo_piutang = SaldoPiutangPenjualan::query()
                ->firstWhere('customer_id', $this->customer_id)->saldo ?? 0;
        $this->totalPiutangRupiah = rupiah_format($this->saldo_piutang);
    }

    /**
     * menghitung sisa piutang
     */
    public function setSisaPiutang():void
    {
        $this->sisa_piutang = (int) $this->saldo_piutang - (int) $this->nominal_kas;
        $this->totalSisaPiutangRupiah = (is_int($this->sisa_piutang)) ? rupiah_format($this->sisa_piutang) : '0';
    }

    public function setSisaBayar(): void
    {
        $this->sisa = (int) $this->totalTagihan - (int) $this->dibayar;
    }

    public function setDibayar(): void
    {
        $this->dibayar = $this->nominal_kas;
    }

    /**
     * set data penjualan
     * @param Penjualan $penjualan
     */
    public function setPenjualan(Penjualan $penjualan):void
    {
        $this->penjualan_type = 'penjualan';
        $this->penjualan_id = $penjualan->id;
        $this->penjualan_kode = $penjualan->kode;
        $this->biaya_lain = ($penjualan->biaya_lain > 0) ? $penjualan->biaya_lain : null;
        $this->ppn =( $penjualan->ppn > 0) ? $penjualan->ppn : null;
        $this->total_penjualan = $penjualan->total_bayar - ($penjualan->biaya_lain ?? 0) - ($penjualan->ppn ?? 0);
        $this->total_penjualan_rupiah = rupiah_format($this->total_penjualan);
        $this->total_bayar = $penjualan->total_bayar;
        $this->total_bayar_rupiah = rupiah_format($this->total_bayar);
    }

    /**
     * set data penjualan_retur
     * @param PenjualanRetur $penjualanRetur
     */
    public function setPenjualanRetur(PenjualanRetur $penjualanRetur):void
    {
        $this->penjualan_type = 'penjualan_retur';
        $this->penjualan_id = $penjualanRetur->id;
        $this->penjualan_kode = $penjualanRetur->kode;
        $this->biaya_lain = ($penjualanRetur->biaya_lain > 0) ? $penjualanRetur->biaya_lain : null;
        $this->ppn =( $penjualanRetur->ppn > 0) ? $penjualanRetur->ppn : null;
        $this->total_penjualan = $penjualanRetur->total_bayar - ($penjualanRetur->biaya_lain ?? 0) - ($penjualanRetur->ppn ?? 0);
        $this->total_penjualan_rupiah = rupiah_format($this->total_penjualan);
        $this->total_bayar = 0 - $penjualanRetur->total_bayar;
        $this->total_bayar_rupiah = rupiah_format($this->total_bayar);
    }

    /**
     * reset form
     */
    public function resetForm()
    {
        $this->reset([
            'penjualan_id', 'penjualan_kode', 'akun_biaya', 'biaya_lain', 'akun_ppn', 'ppn', 'total_bayar',
            'total_penjualan_rupiah', 'total_bayar_rupiah'
        ]);
    }

    public function addLine():void
    {
        $this->detail[] = [
            'penjualan_kode'=>$this->penjualan_kode,
            'penjualan_type'=>$this->penjualan_type,
            'total_penjualan'=>$this->total_penjualan,
            'total_penjualan_rupiah'=>$this->total_penjualan_rupiah,
            'akun_biaya'=>$this->akun_biaya,
            'biaya_lain'=>$this->biaya_lain,
            'akun_ppn'=>$this->akun_ppn,
            'ppn'=>$this->ppn,
            'total_bayar'=>$this->total_bayar,
            'total_bayar_rupiah'=>$this->total_bayar_rupiah,
        ];
        $this->resetForm();
    }

    public function editLine($index)
    {
        $this->update = true;
        $this->index = $index;
        $this->penjualan_kode = $this->detail[$index]['penjualan_kode'];
        $this->penjualan_type = $this->detail[$index]['penjualan_type'];
        $this->total_penjualan = $this->detail[$index]['total_penjualan'];
        $this->total_penjualan_rupiah = $this->detail[$index]['total_penjualan_rupiah'];
        $this->akun_biaya = $this->detail[$index]['akun_biaya'];
        $this->biaya_lain = $this->detail[$index]['biaya_lain'];
        $this->akun_ppn = $this->detail[$index]['akun_ppn'];
        $this->ppn = $this->detail[$index]['ppn'];
        $this->total_bayar = $this->detail[$index]['total_bayar'];
        $this->total_bayar_rupiah = $this->detail[$index]['total_bayar_rupiah'];
    }

    public function updateLine()
    {
        $this->update = false;
        $index = $this->index;
        $this->detail[$index]['penjualan_kode'] = $this->penjualan_kode;
        $this->detail[$index]['penjualan_type'] = $this->penjualan_type;
        $this->detail[$index]['total_penjualan'] = $this->total_penjualan;
        $this->detail[$index]['total_penjualan_rupiah'] = $this->total_penjualan_rupiah;
        $this->detail[$index]['akun_biaya'] = $this->akun_biaya;
        $this->detail[$index]['biaya_lain'] = $this->biaya_lain;
        $this->detail[$index]['akun_ppn'] = $this->akun_ppn;
        $this->detail[$index]['ppn'] = $this->ppn;
        $this->detail[$index]['total_bayar'] = $this->total_bayar;
        $this->detail[$index]['total_bayar_rupiah'] = $this->total_bayar_rupiah;
        $this->resetForm();
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
}
