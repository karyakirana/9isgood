<?php

namespace App\Http\Livewire\Testing;

use App\Models\KonfigurasiJurnal;
use App\Models\Master\Customer;
use App\Models\Master\Produk;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanRetur;
use Livewire\Component;

class TestingPenjualanForm extends Component
{
    protected $listeners = [
        'set_produk'=>'setProduk'
    ];

    // initiation
    protected $customer;
    protected $produk;
    protected $penjualan;
    protected $penjualanRetur;
    protected $konfigAkun;
    protected $jenisTransaksi; // penjualan, retur baik, retur rusak
    protected $mode = 'create'; // default create dan bisa update

    // form penjualan
    public $penjualanId;
    public $penjualanReturId;
    public $customerId, $customerNama, $customerDiskon;
    public $gudangId;
    public $tglNota;
    public $tglTempo;
    public $jenisBayar;
    public $statusBayar;
    public $totalBarang;
    public $totalPenjualan, $totalPenjualanRupiah; // total penjualan = pendapatan
    public $totalBayar, $totalBayarRupiah; // total bayar = piutang
    public $print;

    // akuntansi
    public $akunHutangPPNId, $ppn;
    public $akunPendapatanPenjualan, $pendapatan;
    public $akunPiutangId; // total bayar
    public $akunHutangBiayaLainId, $biayaLain;
    public $akunHppId, $akunPersediaanId;

    // detail
    public $dataDetail = [];
    public $update = false;
    public $index;
    public $produkId, $produkKodeLokal, $produkNama, $produkKategori, $produkKategoriHarga, $produkCover;
    public $diskon, $hargaDiskon, $hargaDiskonRupiah;
    public $harga, $hargaRupiah;
    public $jumlah;
    public $subTotal, $subTotalRupiah;

    public function __construct($id = null)
    {
        parent::__construct($id);
        // initaite
        $this->customer = new Customer();
        $this->produk = new Produk();
        $this->penjualan = new Penjualan();
        $this->penjualanRetur = new PenjualanRetur();
        $this->konfigAkun = new KonfigurasiJurnal();
        // initiate default date
        $this->tglNota = tanggalan_format(now('ASIA/JAKARTA'));
        $this->tglTempo = tanggalan_format(now('ASIA/JAKARTA')->addMonth(2));
        // get akun
        $this->getAkun();
    }

    public function mount($jenisTransaksi = 'penjualan', $transaksiId = null)
    {
        $this->jenisTransaksi = $jenisTransaksi;
    }

    protected function getPenjualan($penjualanId)
    {
        // load penjualan
        $penjualan = $this->penjualan->newQuery()->find($penjualanId);
    }

    protected function getPenjualanRetur($returId)
    {
        // load penjualan retur
        $penjualanRetur = $this->penjualanRetur->newQuery()->find($returId);
    }

    protected function getAkun()
    {
        $this->akunPiutangId = KonfigurasiJurnal::query()->find('piutang_usaha')->akun_id;
        $this->akunPendapatanPenjualan = KonfigurasiJurnal::query()->find('penjualan')->akun_id;
        $this->akunHutangBiayaLainId = KonfigurasiJurnal::query()->find('biaya_penjualan')->akun_id;
        $this->akunHutangPPNId = KonfigurasiJurnal::query()->find('ppn_penjualan')->akun_id;
    }

    protected function getAkunPersediaan()
    {
        $this->akunHppId = KonfigurasiJurnal::query()->find('hpp_internal')->akun_id;
        $this->akunPersediaanId = KonfigurasiJurnal::query()->find('persediaan')->akun_id;
    }

    public function setCustomer($customerId)
    {
        $customer = $this->customer->newQuery()->find($customerId);
        $this->customerId = $customerId;
        $this->customerNama = $customer->nama;
        $this->customerDiskon = $customer->diskon;
    }

    public function setProduk($produkId)
    {
        $produk = $this->produk->newQuery()->find($produkId);
        // dd($produk);
        $this->produkId = $produkId;
        $this->produkNama = $produk->nama."\n".$produk->kode_lokal."\n".$produk->kategoriHarga->deskripsi."\n".$produk->cover;
        $this->produkKodeLokal = $produk->kode_lokal;
        $this->produkKategori = $produk->kategori->nama;
        $this->produkKategoriHarga = $produk->kategoriHarga->deskripsi;
        $this->harga = $produk->harga;
        $this->hargaRupiah = rupiah_format((int)$this->harga);
        $this->diskon = $this->customerDiskon ?? 0;
        $this->setSubTotal();
        $this->update = false;
    }

    public function addLine()
    {
        $this->validateFormDetail();
        $this->dataDetail[] = [
            'produk_id'=>$this->produkId,
            'produk_nama'=>$this->produkNama,
            'produk_kode_lokal'=>$this->produkKodeLokal,
            'produk_kategori'=>$this->produkKategori,
            'produk_kategori_harga'=>$this->produkKategoriHarga,
            'harga'=>$this->harga,
            'harga_rupiah'=>$this->hargaRupiah,
            'diskon'=>$this->diskon,
            'jumlah'=>$this->jumlah,
            'sub_total'=>$this->subTotal
        ];
        $this->setTotalItem();
        $this->resetFormDetail();
    }

    public function editLine($index)
    {
        //dd( $this->dataDetail[$index]);
        $this->update = true;
        $this->index = $index;
        $this->produkId = $this->dataDetail[$index]['produk_id'];
        $this->produkNama = $this->dataDetail[$index]['produk_nama'];
        $this->produkKodeLokal = $this->dataDetail[$index]['produk_kode_lokal'];
        $this->produkKategori = $this->dataDetail[$index]['produk_kategori'];
        $this->produkKategoriHarga = $this->dataDetail[$index]['produk_kategori_harga'];
        $this->harga = $this->dataDetail[$index]['harga'];
        $this->hargaRupiah = $this->dataDetail[$index]['harga_rupiah'];
        $this->diskon = $this->dataDetail[$index]['diskon'];
        $this->jumlah = $this->dataDetail[$index]['jumlah'];
        $this->subTotal = $this->dataDetail[$index]['sub_total'];
        $this->setSubTotal();
    }

    public function updateLine()
    {
        $this->validateFormDetail();
        $index = $this->index;
        $this->dataDetail[$index]['produk_id'] = $this->produkId;
        $this->dataDetail[$index]['produk_nama'] = $this->produkNama;
        $this->dataDetail[$index]['produk_kode_lokal'] = $this->produkKodeLokal;
        $this->dataDetail[$index]['produk_kategori'] = $this->produkKategori;
        $this->dataDetail[$index]['produk_kategori_harga'] = $this->produkKategoriHarga;
        $this->dataDetail[$index]['produk_cover'] = $this->produkCover;
        $this->dataDetail[$index]['harga'] = $this->harga;
        $this->dataDetail[$index]['diskon'] = $this->diskon;
        $this->dataDetail[$index]['jumlah'] = $this->jumlah;
        $this->dataDetail[$index]['sub_total'] = $this->subTotal;
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
        $this->emit('hideConfirmation');
    }

    public function setSubTotal()
    {
        $this->setDiskon();
        $this->subTotal = $this->hargaDiskon * (int)$this->jumlah;
        $this->subTotalRupiah = rupiah_format($this->subTotal);
    }

    public function setTotalItem()
    {
        // jumlah total barang
        $this->totalBarang = array_sum(array_column($this->dataDetail, 'jumlah'));
        // jumlah total dari sub_total
        $this->totalPenjualan = array_sum(array_column($this->dataDetail, 'sub_total'));
        $this->pendapatan = $this->totalPenjualan;
        // jumlah total bayar
        $this->totalBayar = (int)$this->totalPenjualan + (int)$this->biayaLain + (int)$this->ppn;
        $this->totalBayarRupiah = rupiah_format($this->totalBayar);
    }

    protected function resetFormDetail()
    {
        $this->reset([
            'index',
            'produkId', 'produkNama', 'produkKodeLokal', 'produkKategori', 'produkKategoriHarga', 'produkCover',
            'harga', 'diskon', 'jumlah', 'subTotal',
            // gimmick interface
            'hargaRupiah', 'hargaDiskon', 'hargaDiskonRupiah', 'subTotalRupiah'
        ]);
    }

    protected function resetForm()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    protected function validateFormDetail()
    {
        $this->validate([
            'produkNama'=>'required',
            'jumlah'=>'required|integer',
            'diskon'=>'required',
        ]);
    }

    protected function setDiskon()
    {
        $this->hargaDiskon = (int)$this->harga - ((int)$this->harga * (float)$this->diskon/100);
        $this->hargaDiskonRupiah = rupiah_format((int)$this->hargaDiskon);
    }

    public function store()
    {
        $data = $this->validate([
            'penjualanId'=>($this->mode == 'update' && $this->jenisTransaksi == 'penjualan') ? 'required' : 'nullable',
            'penjualanReturId'=>($this->mode == 'update' && $this->jenisTransaksi == 'retur') ? 'required' : 'nullable',
            'customerId'=>'required',
            'customerNama'=>'required',
            'gudangId'=>'required',
            'tglNota'=>'required',
            'tglTempo'=>($this->jenisBayar == 'tempo') ? 'required' : 'nullable',
            'jenisBayar'=>'required',
            'statusBayar'=>'nullable',
            'totalBarang'=>'required',
            'totalPenjualan'=>'required',
            'totalBayar'=>'required',
            'dataDetail'=>'required',
            // akuntansi
            'akunPiutangId'=>'required',
            'akunPendapatanPenjualan'=>'required',
            'pendapatan'=>'required',
            'baiayaLain'=>( (int)$this->biayaLain > 0) ?'required' : 'nullable',
            'akunHutangBiayaLain'=>( (int)$this->biayaLain > 0) ?'required' : 'nullable',
            'ppn'=>( (int)$this->ppn > 0) ?'required' : 'nullable',
            'akunHutangPPNId'=>( (int)$this->ppn > 0) ?'required' : 'nullable',
            'akunHppId'=>'required',
            'akunPersediaanId'=>'required',
        ]);
    }

    public function render()
    {
        return view('livewire.testing.testing-penjualan-form')
            ->layout('layouts.metronics-811', ['minimize' => 'on']);
    }
}
