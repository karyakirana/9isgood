<?php /** @noinspection PhpClassHasTooManyDeclaredMembersInspection */

namespace App\Http\Livewire\Pembelian;

use App\Haramain\SistemPembelian\PembelianService;
use App\Http\Livewire\Master\SetMasterTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

abstract class PembelianLivewire extends Component
{
    use SetMasterTrait;

    protected $listeners = [
        'set_supplier'=>'setSupplier',
        'set_produk'=>'setProduk'
    ];

    protected $pembelianService;

    // general attributes
    public $update = false;
    public $mode = 'create';
    public $jenis;
    public $kondisi;

    // pembelian attributes
    public $pembelianId;
    public $tglNota, $tglTempo;
    public $gudangId;
    public $userId;
    public $jenisBayar = 'tunai';
    public $statusBayar = 'belum';
    public $totalBarang;
    public $totalPembelian;
    public $ppn, $biayaLain;
    public $totalBayar;
    public $keterangan;

    public $nomorNota = '-', $suratJalan = '-';

    // stock masuk attrributes
    public $nomorPo;

    // pembelian detail attributes
    public $produk_id, $harga, $jumlah, $diskon, $sub_total;

    // pembelian detail helper attributes
    public $index_detail, $dataDetail = [];

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->pembelianService = new PembelianService();

        // set tanggal
        $this->tglNota = tanggalan_format(now('ASIA/JAKARTA'));
        $this->tglTempo = tanggalan_format(now('ASIA/JAKARTA')->addMonths(2));
    }

    protected function mountPembelian($pembelianId)
    {
        // untuk kepentingan edit
        $this->mode = 'update';
        $pembelian = $this->pembelianService->handleGetData($pembelianId);
        $this->pembelianId = $pembelian->id;
        $this->supplierId = $pembelian->supplier_id;
        $this->supplierNama = $pembelian->supplier->nama;
        $this->gudangId = $pembelian->gudang_id;
        $this->userId = $pembelian->users->id;
        $this->tglNota = $pembelian->tgl_nota;
        $this->tglTempo = ($pembelian->jenis_bayar == 'tempo') ? $pembelian->tgl_tempo : $this->tglTempo;
        $this->jenisBayar = $pembelian->jenis_bayar;
        $this->ppn = $pembelian->ppn;
        $this->biayaLain = $pembelian->biaya_lain;
        $this->totalBayar = $pembelian->total_bayar;
        $this->keterangan = $pembelian->keterangan;

        $this->suratJalan = $pembelian->stockMasukMorph->nomor_surat_jalan;
        $this->nomorPo = $pembelian->stockMasukMorph->nomor_po;

        foreach ($pembelian->pembelianDetail as $item) {
            $this->dataDetail[] = [
                'produk_id'=>$item->produk_id,
                'produk_kode_lokal'=>$item->produk->kode_lokal,
                'produk_nama'=>$item->produk->nama,
                'produk_harga'=>$item->produk->harga,
                'diskon'=>$item->diskon,
                'harga'=>$item->harga,
                'jumlah'=>$item->jumlah,
                'sub_total'=>$item->sub_total
            ];
        }

        $this->totalBarang = array_sum(array_column($this->dataDetail, 'jumlah'));
        $this->totalPembelian = array_sum(array_column($this->dataDetail, 'sub_total'));
        $this->totalBayar = $this->totalPembelian + (int) $this->ppn + (int) $this->biayaLain;
    }

    public function resetFromDetail()
    {
        $this->reset([
            'produk_id', 'produk_nama', 'produk_kode_lokal', 'produk_harga', 'diskon',
            'harga', 'jumlah', 'sub_total'
        ]);
    }

    public abstract function mount($pembelianId = null);

    public function hitungSubTotal()
    {
        $this->sub_total = (int) $this->harga * (int) $this->jumlah;
    }

    public function addLine()
    {
        $this->hitungSubTotal();
        $this->dataDetail[] = [
            'produk_id'=>$this->produk_id,
            'produk_kode_lokal'=>$this->produk_kode_lokal,
            'produk_nama'=>$this->produk_nama,
            'produk_harga'=>$this->produk_harga,
            'diskon'=>0,
            'harga'=>$this->harga,
            'jumlah'=>$this->jumlah,
            'sub_total'=>$this->sub_total
        ];
        $this->resetFromDetail();
    }

    public function editLine($index)
    {
        $this->update = true;
        $this->index_detail = $index;
        $this->produk_id = $this->dataDetail[$index]['produk_id'];
        $this->produk_kode_lokal = $this->dataDetail[$index]['produk_kode_lokal'];
        $this->produk_nama = $this->dataDetail[$index]['produk_nama'];
        $this->produk_harga = $this->dataDetail[$index]['produk_harga'];
        $this->harga = $this->dataDetail[$index]['harga'];
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
        $this->dataDetail[$index]['harga'] = $this->harga;
        $this->dataDetail[$index]['jumlah'] = $this->jumlah;
        $this->dataDetail[$index]['sub_total'] = $this->sub_total;
        $this->update = false;
        $this->resetFromDetail();
    }

    public function destroyLine($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
    }

    protected function setDataValidate():? array
    {
        $this->totalBarang = array_sum(array_column($this->dataDetail, 'jumlah'));
        $this->totalPembelian = array_sum(array_column($this->dataDetail, 'sub_total'));
        $this->totalBayar = $this->totalPembelian + (int) $this->ppn + (int) $this->biayaLain;
        $this->userId = Auth::id();
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
            'statusBayar'=>'required',
            'totalBarang'=>'required',
            'totalPembelian'=>'required',
            'ppn'=>((int)$this->ppn > 0) ? 'required' : 'nullable',
            'biayaLain'=>((int)$this->biayaLain > 0) ? 'required' : 'nullable',
            'totalBayar'=>'required',
            'keterangan'=>'nullable',

            // data detail
            'dataDetail'=>'required',

            // stock masuk
            'kondisi'=>'required',
            'nomorPo'=>'nullable',
        ]);
    }

    public abstract function store();
    public abstract function update();

    public abstract function render();
}
