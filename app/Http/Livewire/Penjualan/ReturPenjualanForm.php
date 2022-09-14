<?php /** @noinspection PhpLackOfCohesionInspection */

namespace App\Http\Livewire\Penjualan;

use App\Haramain\SistemKeuangan\SubPersediaan\PersediaanRepository;
use App\Haramain\SistemPenjualan\PenjualanReturService;
use App\Models\Master\Produk;
use Livewire\Component;

class ReturPenjualanForm extends Component
{
    // trait
    use LivewirePenjualanTrait;

    protected $listeners = [
        'set_customer'=>'setCustomer',
        'set_produk'=>'setProduk'
    ];

    // service
    protected $penjualanReturService;

    // global attributes
    public $dataDetail = []; // detail umum
    public $dataDetailHpp = []; // detail persediaan
    public $mode = 'create';

    // penjualan attributes
    public $penjualanReturId;
    public $kondisi;
    public $gudangId;
    public $userId;
    public $jenisBayar;
    public $tglNota, $tglTempo;
    public $statusBayar = 'belum';
    public $totalBarang;
    public $biayaLain;
    public $ppn;
    public $totalBayar;
    public $keterangan;

    // penjualan retur detail attributes
    public $update = false;
    public $index;
    public $jumlah;
    public $sub_total;

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
        $this->tglNota = tanggalan_format(now('ASIA/JAKARTA'));
        $this->tglTempo = tanggalan_format(now('ASIA/JAKARTA')->addMonth(2));
    }

    public function mount($kondisi = 'baik', $penjualanReturId = null)
    {
        $this->kondisi = $kondisi;
        $this->penjualanReturId = $penjualanReturId;
        //dd($penjualanReturId);
        if ($penjualanReturId){
            $penjualanRetur = $this->penjualanReturService->handleGetData($penjualanReturId);
            $this->mode = 'update';
            //dd($penjualanRetur);
            $this->gudangId = $penjualanRetur->gudang_id;
            $this->jenisBayar = ($penjualanRetur->tgl_tempo) ? 'tempo' : 'cash';
            $this->tglNota = $penjualanRetur->tgl_nota;
            $this->tglTempo = ($penjualanRetur->tgl_tempo) ? tanggalan_format($penjualanRetur->tgl_tempo) : tanggalan_format(now('ASIA/JAKARTA')->addMonth(2));
            $this->totalBarang = $penjualanRetur->total_barang;
            $this->biayaLain = $penjualanRetur->biaya_lain;
            $this->ppn = $penjualanRetur->ppn;
            $this->totalBayar = $penjualanRetur->total_bayar;
            $this->keterangan = $penjualanRetur->keterangan;

            $this->customerId = $penjualanRetur->customer_id;
            $this->customerNama = $penjualanRetur->customer->nama;
            $this->customerDiskon = $penjualanRetur->customer->diskon;

            foreach ($penjualanRetur->returDetail as $row){
                //dd($row);
                $this->harga = $row->harga;
                $this->produkId = $row->produk_id;
                $this->setHpp();
                $this->dataDetail[] = [
                    'produk_id'=>$row->produk_id,
                    'produk_nama'=>$row->produk->nama,
                    'produk_kode_lokal'=>$row->produk->kode_lokal,
                    'produk_kategori'=>$row->produk->kategori->deskripsi,
                    'produk_kategori_harga'=>$row->produk->kategoriHarga->deskripsi,
                    'harga'=>$row->harga,
                    'harga_rupiah'=>rupiah_format($row->harga),
                    'diskon'=>$row->diskon,
                    'jumlah'=>$row->jumlah,
                    'sub_total'=>$row->sub_total
                ];
                $this->dataDetailHpp[] = [
                    'produk_id'=>$row->produk_id,
                    'harga'=>$this->hargaHpp,
                    'diskon'=>0,
                    'jumlah'=>$row->jumlah,
                    'sub_total'=>$row->jumlah * $this->hargaHpp
                ];
                $this->reset(['harga', 'hargaHpp', 'produkId']);
            }
            $this->setTotalItem();
        }
    }

    public function setProduk($produkId)
    {
        $produk = Produk::query()->findOrFail($produkId);
        $this->setDetailFromProduk($produk); // from trait
        $this->setSubTotal(); // from trait
        $this->update = false;
    }

    public $hargaHpp, $subTotalHpp;

    /** set for data detail hpp */
    public function setHpp()
    {
        $persediaanRepo = new PersediaanRepository();
        $produk = $persediaanRepo->getDataLatest($this->produkId, 'baik');
        $this->hargaHpp = $produk->harga;
    }

    public function addLine()
    {
        $this->validateFormDetail();
        $this->setDataDetail(); // from trait
        $this->dataDetailHpp[] = [
            'produk_id'=>$this->produkId,
            'harga'=>$this->hargaHpp,
            'diskon'=>0,
            'jumlah'=>$this->jumlah,
            'sub_total'=>$this->jumlah * $this->hargaHpp
        ];
        $this->setTotalItem();
        $this->resetFormDetail();
    }

    public function editLine($index)
    {
        $this->update = true;
        $this->index = $index;
        $this->getDataDetail($index); // from trait
        $this->hargaHpp = $this->dataDetailHpp[$index]['harga'];
        $this->setSubTotal();
    }

    public function updateLine():void
    {
        $this->validateFormDetail();
        $index = $this->index;
        $this->updateDataDetail($index); // from trait
        $this->dataDetailHpp[$index]['harga'] = $this->hargaHpp;
        $this->dataDetailHpp[$index]['jumlah'] = $this->jumlah;
        $this->dataDetailHpp[$index]['sub_total'] = $this->jumlah * $this->hargaHpp;
        $this->setTotalItem();
        $this->update = false;
        $this->resetFormDetail();
    }

    public function setRemoveLineIndex($index)
    {
        $this->index = $index;
        $this->emit('showConfirmation');
        $this->setTotalItem();
    }

    public function removeLine($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
        $this->dataDetailHpp = array_values($this->dataDetailHpp);
        $this->emit('hideConfirmation');
    }

    public $totalPenjualanRetur;
    public $totalPenjualanReturRupiah;
    public $totalPenjualanReturHpp;
    public $totalBayarRupiah;

    public function setTotalItem():void
    {
        // jumlah total barang
        $this->totalBarang = array_sum(array_column($this->dataDetail, 'jumlah'));
        // jumlah total dari sub_total
        $this->totalPenjualanRetur = array_sum(array_column($this->dataDetail, 'sub_total'));
        $this->totalPenjualanReturRupiah = rupiah_format($this->totalPenjualanRetur);
        // jumlah total dari sub_total_hpp
        $this->totalPenjualanReturHpp = array_sum(array_column($this->dataDetailHpp, 'sub_total'));
        // jumlah total bayar
        $this->totalBayar = (int)$this->totalPenjualanRetur + (int)$this->biayaLain + (int)$this->ppn;
        $this->totalBayarRupiah = rupiah_format($this->totalBayar);
    }

    protected function resetFormDetail():void
    {
        $this->reset([
            'index',
            'produkId', 'produkNama', 'produkKodeLokal', 'produkKategori', 'produkKategoriHarga', 'produkCover',
            'harga', 'hargaHpp', 'diskon', 'jumlah', 'subTotal', 'subTotalHpp',
            // ginmick interface
            'hargaRupiah', 'hargaDiskon', 'hargaDiskonRupiah', 'subTotalRupiah'
        ]);
    }

    /** start store and update */
    public function validatedData()
    {
        $this->tglInput = $this->tglNota;
        $this->userId = auth()->id();
        return $this->validate([
            'penjualanReturId'=>($this->mode == 'update') ? 'required' : 'nullable',
            'customerId'=>'required',
            'customerNama'=>'required',
            'userId'=>'required',
            'gudangId'=>'required',
            'tglNota'=>'required',
            'tglTempo'=>($this->jenisBayar == 'tempo') ? 'required' : 'nullable',
            'jenisBayar'=>'required',
            'statusBayar'=>'nullable',
            'totalBarang'=>'required',
            'totalPenjualanRetur'=>'required',
            'totalPenjualanReturHpp'=>'required',
            'totalBayar'=>'required',
            'dataDetail'=>'required',
            'dataDetailHpp'=>'required',
            'keterangan'=>'nullable',

            // stock
            'kondisi'=>'required',

            // persediaan
            'jenisPersediaan'=>'required',
            'tglInput'=>'required',

            // akuntansi
            'biayaLain'=>( (int)$this->biayaLain > 0) ?'required' : 'nullable',
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
