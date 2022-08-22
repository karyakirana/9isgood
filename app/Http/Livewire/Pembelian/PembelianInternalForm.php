<?php

namespace App\Http\Livewire\Pembelian;

use App\Haramain\Repository\Pembelian\PembelianCobaRepo;
use App\Haramain\Repository\Pembelian\PembelianInternalRepo;
use App\Haramain\Service\SistemPembelian\PembelianInternalService;
use App\Models\Keuangan\HargaHppALL;
use App\Models\KonfigurasiJurnal;
use App\Models\Master\Gudang;
use App\Models\Master\Produk;
use App\Models\Master\Supplier;
use App\Models\Purchase\Pembelian;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class PembelianInternalForm extends Component
{
    protected $listeners = [
        'set_supplier'=>'setSupplier',
        'set_produk'=>'setProduk'
    ];

    // initiate
    protected $pembelianInternalService;
    public $update = false;
    public $mode = 'create';

    // var pembelian
    public $pembelianId;
    public $nomorNota = '-', $suratJalan = '-';
    public $supplierId, $supplierNama;
    public $gudangId;
    public $userId;
    public $tglNota, $tglTempo;
    public $jenisBayar = 'tunai';
    public $totalBarang;
    public $totalPembelian;
    public $ppn, $biayaLain;
    public $totalBayar;
    public $keterangan;

    public $dataDetail = [];

    // var pembelian internal
    public $jenis = 'INTERNAL', $kondisi='baik';

    // var pembelian detail
    public $produk_id, $harga, $jumlah, $diskon, $sub_total;
    public $produk_kode_lokal, $produk_nama, $produk_harga;
    public $hpp, $harga_setelah_hpp;

    // var jurnal transaksi
    public $akunHutangPembelianId, $akunPembelianId, $akunPPNPembelianId, $akunBiayaLainPembelianId;
    public $akunPersediaanId;

    // var stock masuk
    public $tglMasuk, $nomorPo;

    // var persediaan
    public $jenisPersediaan = 'masuk';
    public $tglInput;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->pembelianInternalService = new PembelianInternalService();
    }

    public function mount($pembelianId = null)
    {
        // set tanggal
        $this->tglNota = tanggalan_format(now('ASIA/JAKARTA'));
        $this->tglTempo = tanggalan_format(now('ASIA/JAKARTA')->addMonth(2));
        // set akun
        $this->akunHutangPembelianId = KonfigurasiJurnal::query()->find('biaya_pembelian')->akun_id ?? '';
        $this->akunPPNPembelianId = KonfigurasiJurnal::query()->find('ppn_pembelian')->akun_id ?? '';
        if ($this->jenis == 'internal'){
            $this->akunHutangPembelianId = KonfigurasiJurnal::query()->find('hutang_dagang_internal')->akun_id ?? '';
        } else {
            $this->akunHutangPembelianId = KonfigurasiJurnal::query()->find('hutang_dagang')->akun_id ?? '';
        }
        $this->akunPersediaanId = KonfigurasiJurnal::query()->find('persediaan')->akun_id ?? '';

        // load hpp
        $this->hpp = HargaHppALL::query()->latest()->first()->persen;
        if ($pembelianId){
            // untuk kepentingan edit
            $pembelian = $this->pembelianInternalService->handleEdit($pembelianId);
        }
    }

    public function setProduk(Produk $produk)
    {
        $this->produk_id = $produk->id;
        $this->produk_nama = $produk->nama."\n".$produk->kategoriHarga->nama."\n".$produk->cover;
        $this->produk_kode_lokal = $produk->kode_lokal;
        $this->produk_harga = $produk->harga;
        $this->hitungHpp();
        //dd($this->produk_harga);
    }

    public function setSupplier(Supplier $supplier)
    {
        $this->supplierId = $supplier->id;
        $this->supplierNama = $supplier->nama;
    }

    public function hitungHpp()
    {
        (int)$this->harga_setelah_hpp = $this->produk_harga * (float)$this->hpp;
    }

    public function hitungSubTotal()
    {
        $this->sub_total = $this->harga_setelah_hpp * $this->jumlah;
    }

    public function resetFormDetail()
    {
        $this->reset([
            'produk_id', 'produk_kode_lokal', 'produk_harga', 'diskon',
            'harga', 'harga_setelah_hpp', 'jumlah', 'sub_total'
        ]);
    }

    public function addLine()
    {
        $this->validate([
            'produk_nama'=>'required',
            'jumlah'=>'required',
            'hpp'=>'required'
        ]);
        $this->hitungSubTotal();
        $this->dataDetail[] = [
            'produk_id'=>$this->produk_id,
            'produk_kode_lokal'=>$this->produk_kode_lokal,
            'produk_nama'=>$this->produk_nama,
            'produk_harga'=>$this->produk_harga,
            'diskon'=>0,
            'harga'=>$this->harga_setelah_hpp,
            'jumlah'=>$this->jumlah,
            'sub_total'=>$this->sub_total
        ];
        // dd($this->harga_setelah_hpp);
        $this->reset(['produk_id', 'produk_kode_lokal', 'produk_nama', 'produk_harga', 'harga_setelah_hpp', 'jumlah', 'sub_total']);
    }

    public function editLine($index)
    {
        $this->update = true;
        $this->index_detail = $index;
        $this->produk_id = $this->dataDetail[$index]['produk_id'];
        $this->produk_kode_lokal = $this->dataDetail[$index]['produk_kode_lokal'];
        $this->produk_nama = $this->dataDetail[$index]['produk_nama'];
        $this->produk_harga = $this->dataDetail[$index]['produk_harga'];
        $this->harga_setelah_hpp = $this->dataDetail[$index]['harga'];
        $this->jumlah = $this->dataDetail[$index]['jumlah'];
        $this->sub_total = $this->dataDetail[$index]['sub_total'];
    }

    public function updateLine()
    {
        $this->validate([
            'produk_nama'=>'required',
            'jumlah'=>'required'
        ]);
        $this->hitungSubTotal();
        $index = $this->index_detail;
        $this->dataDetail[$index]['produk_id'] = $this->produk_id;
        $this->dataDetail[$index]['produk_kode_lokal'] = $this->produk_kode_lokal;
        $this->dataDetail[$index]['produk_nama'] = $this->produk_nama;
        $this->dataDetail[$index]['produk_harga'] = $this->produk_harga;
        $this->dataDetail[$index]['harga'] = $this->harga_setelah_hpp;
        $this->dataDetail[$index]['jumlah'] = $this->jumlah;
        $this->dataDetail[$index]['sub_total'] = $this->sub_total;
        $this->update = false;
        $this->reset(['produk_id', 'produk_kode_lokal', 'produk_nama', 'produk_harga', 'harga_setelah_hpp', 'jumlah', 'sub_total']);
    }

    public function destroyLine($index)
    {
        // remove line transaksi
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
    }

    protected function setDataValidate()
    {
        $this->totalBarang = array_sum(array_column($this->dataDetail, 'jumlah'));
        $this->totalPembelian = array_sum(array_column($this->dataDetail, 'sub_total'));
        $this->totalBayar = $this->totalPembelian + (int) $this->ppn + (int) $this->biayaLain;
        $this->userId = \Auth::id();
        $this->tglInput = $this->tglNota;
        $this->tglMasuk = $this->tglNota;
        return $this->validate([
            'pembelianId'=>($this->pembelianId) ? 'required' : 'nullable',
            'nomorNota'=>'nullable',
            'suratJalan'=>'nullable',
            'jenis'=>'required',
            'supplierId'=>'required',
            'supplierNama'=>'required',
            'gudangId'=>'required',
            'userId'=>'required',
            'tglNota'=>'required|date:d-M-Y',
            'tglTempo'=>($this->jenisBayar == 'tempo') ? 'required|date:d-M-Y' : 'nullable',
            'jenisBayar'=>'required',
            'totalBarang'=>'required',
            'totalPembelian'=>'required',
            'ppn'=>((int)$this->ppn > 0) ? 'required' : 'nullable',
            'biayaLain'=>((int)$this->biayaLain > 0) ? 'required' : 'nullable',
            'totalBayar'=>'required',
            'keterangan'=>'nullable',

            // data detail
            'dataDetail'=>'required',

            // akun
            'akunHutangPembelianId'=>'required',
            'akunPersediaanId'=>'required',
            'akunPPNPembelianId'=>((int)$this->ppn > 0) ? 'required' : 'nullable',
            'akunBiayaLainPembelianId'=>((int)$this->biayaLain > 0) ? 'required' : 'nullable',

            // stock masuk
            'kondisi'=>'required',
            'nomorPo'=>'nullable',
            'tglMasuk'=>'required|date:d-M-Y',

            // jenis persediaan
            'tglInput'=>'required|date:d-M-Y',
            'jenisPersediaan'=>'required',
        ]);
    }

    public function store()
    {
        $data = $this->setDataValidate();
        //dd($data);
        $store = $this->pembelianInternalService->handleStore($data);
        if ($store->status){
            // redirect
            session()->flash('storeMessage', $store->keterangan);
            return redirect()->to(route('stock.masuk'));
        }
        session()->flash('storeMessage', $store->keterangan);
        return null;
    }
    public function update()
    {
        //dd($this->data_detail);
        $data = $this->setDataValidate();
        try {
            $pembelian = (new PembelianInternalRepo())->update($data);
            \DB::commit();
            return redirect()->to(route('stock.masuk'));
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
        return null;
    }

    public function render()
    {
        return view('livewire.pembelian.pembelian-internal-form');
    }

}
