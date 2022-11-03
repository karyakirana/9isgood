<?php /** @noinspection PhpLackOfCohesionInspection */

namespace App\Http\Livewire\Penjualan;

use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanRepository;
use App\Haramain\SistemPenjualan\PenjualanReturService;
use App\Haramain\Traits\LivewireTraits\SetCustomerTraits;
use App\Http\Livewire\Master\LivewireProdukTrait;
use App\Http\Livewire\Master\SetCustomerTrait;
use App\Models\Master\Produk;
use Livewire\Component;

class ReturPenjualanForm extends Component
{
    // trait
    use LivewirePenjualanTrait;
    use LivewireProdukTrait;
    use SetCustomerTraits;

    protected $listeners = [
        'set_customer'=>'setCustomer',
        'set_produk'=>'setProduk'
    ];

    // service
    protected $penjualanReturService;

    // global attributes
    public $dataDetail = []; // detail umum
    public $mode = 'create';

    // penjualan attributes
    public $penjualan_retur_id;
    public $kondisi;
    public $jenis_retur;
    public $gudang_id;
    public $user_id;
    public $jenis_bayar;
    public $tgl_nota, $tgl_tempo;
    public $status_bayar = 'belum';
    public $total_barang;
    public $biaya_lain;
    public $ppn;
    public $total_bayar;
    public $keterangan;

    // stock masuk attributes
    public $tglMasuk;

    // persediaan transaksi attributes
    public $tglInput;
    public $jenisPersediaan = 'masuk';

    // hpp atributes
    public $hpp;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->penjualanReturService = new PenjualanReturService();

        // initiate default date
        $this->tgl_nota = tanggalan_format(now('ASIA/JAKARTA'));
        $this->tgl_tempo = tanggalan_format(now('ASIA/JAKARTA')->addMonth(2));
    }

    public function mount($kondisi = 'baik', $penjualanReturId = null)
    {
        $this->kondisi = $kondisi;
        $this->penjualan_retur_id = $penjualanReturId;
        //dd($penjualanReturId);
        if ($penjualanReturId){
            $penjualanRetur = $this->penjualanReturService->handleGetData($penjualanReturId);
            $this->mode = 'update';
            //dd($penjualanRetur);
            $this->gudang_id = $penjualanRetur->gudang_id;
            $this->jenis_bayar = ($penjualanRetur->tgl_tempo) ? 'tempo' : 'cash';
            $this->tgl_nota = $penjualanRetur->tgl_nota;
            $this->tgl_tempo = ($penjualanRetur->tgl_tempo) ? tanggalan_format($penjualanRetur->tgl_tempo) : tanggalan_format(now('ASIA/JAKARTA')->addMonth(2));
            $this->total_barang = $penjualanRetur->total_barang;
            $this->biaya_lain = $penjualanRetur->biaya_lain;
            $this->ppn = $penjualanRetur->ppn;
            $this->total_bayar = $penjualanRetur->total_bayar;
            $this->keterangan = $penjualanRetur->keterangan;

            $this->customer_id = $penjualanRetur->customer_id;
            $this->customer_nama = $penjualanRetur->customer->nama;
            $this->customer_diskon = $penjualanRetur->customer->diskon;

            // penjualan_detail
            $this->setDataDetail($penjualanRetur->returDetail);

            // helper atteribute
            $this->total_penjualan = (int) $this->total_bayar - (int) $this->ppn - (int) $this->biaya_lain;
            $this->total_penjualan_rupiah = rupiah_format($this->total_penjualan);
            $this->total_bayar_rupiah = rupiah_format($this->total_bayar);
        }
    }

    public function updatedPpn()
    {
        $this->setTotalForm();
    }

    public function updatedBiayaLain()
    {
        $this->setTotalForm();
    }

    /** start store and update */
    public function validatedData()
    {
        $this->user_id = auth()->id();
        $this->jenis_retur = $this->kondisi;
        return $this->validate([
            'penjualan_retur_id'=>($this->mode == 'update') ? 'required' : 'nullable',
            'customer_id'=>'required',
            'kondisi'=>'required',
            'jenis_retur'=>'required',
            'customer_nama'=>'required',
            'user_id'=>'required',
            'gudang_id'=>'required',
            'tgl_nota'=>'required',
            'tgl_tempo'=>($this->jenis_bayar == 'tempo') ? 'required' : 'nullable',
            'jenis_bayar'=>'required',
            'status_bayar'=>'nullable',
            'total_barang'=>'required',
            'total_penjualan'=>'required',
            'total_bayar'=>'required',
            'dataDetail'=>'required',
            'keterangan'=>'nullable',
            'biaya_lain'=>( (int)$this->biaya_lain > 0) ?'required' : 'nullable',
            'ppn'=>( (int)$this->ppn > 0) ?'required' : 'nullable',
        ]);
    }

    public function store()
    {
        $data = $this->validatedData();
        // dd($data);
        $store = $this->penjualanReturService->handleStore($data);
        if ($store->status){
            // redirect
            session()->flash('storeMessage', $store->keterangan);
            return redirect()->to('penjualan/retur/print/'.$store->keterangan->id);
        }
        session()->flash('storeMessage', $store->keterangan);
        return null;
    }

    public function update()
    {
        $data = $this->validatedData();
        //dd($data);
        $store = $this->penjualanReturService->handleUpdate($data);
        if ($store->status){
            // redirect
            session()->flash('storeMessage', $store->keterangan);
            return redirect()->to('penjualan/retur/print/'.$store->keterangan->id);
        }
        session()->flash('storeMessage', $store->keterangan);
        return null;
    }

    public function render()
    {
        return view('livewire.penjualan.retur-penjualan-form');
    }
}
