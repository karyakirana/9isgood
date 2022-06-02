<?php namespace App\Http\Livewire\Penjualan;

use App\Haramain\Traits\LivewireTraits\SetCustomerTraits;
use App\Haramain\Traits\LivewireTraits\SetProdukTraits;
use App\Models\Master\Customer;
use App\Models\Master\Gudang;
use App\Models\Master\Produk;
use Livewire\Component;

class Transaksi extends Component
{
    use SetProdukTraits, SetCustomerTraits;
    // listeners
    protected $listeners = [
        'set_customer' => 'setCustomer',
        'set_produk' => 'setProduk'
    ];

    public string $mode= 'create';

    // enable update proses
    public bool $update = false;

    // array get data from gudang
    public mixed $gudang_data;

    // master form properties
    public $gudang_id;
    public $tgl_nota, $tgl_tempo;
    public $jenis_bayar, $status_bayar;
    public $total_barang, $ppn, $biaya_lain, $total_bayar;
    public $keterangan;

    // master form manipulate interface (rupiah format)
    public int $total;
    public string $total_rupiah, $total_bayar_rupiah;

    // for store data details
    public int $indexDetail;
    public array $data_detail = [];

    // detail form properties
    public $idProduk = null;
    public $halProduk, $subTotalHarga;
    public $jumlahProduk, $hargaProduk;
    public $namaProduk, $kodeLokalProduk, $coverProduk;
    public $diskonProduk;

    // detail form manipulate properties
    public $hargaSetelahDiskon;
    public $hargaSetelahDiskonRupiah, $hargaRupiah, $subTotalHargaRupiah;

    /**
     * set for construct
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->gudang_data = Gudang::all();
        $this->tgl_nota = tanggalan_format(now('ASIA/JAKARTA'));
        $this->tgl_tempo = tanggalan_format(now('ASIA/JAKARTA')->addMonth(2));
    }

    public function forMount($mode, $data, $data_detail)
    {
        $this->mode = $mode;

        // mount customer data
        $this->customer_id = $data->customer_id;
        $this->customer_nama = $data->customer->nama;
        $this->customer_diskon = $data->customer->diskon;

        // mount single data
        $this->jenis_bayar = $data->jenis_bayar;
        $this->tgl_nota = tanggalan_format($data->tgl_nota);
        $this->tgl_tempo = tanggalan_format($data->tgl_tempo);
        $this->gudang_id = $data->gudang_id;
        $this->biaya_lain = $data->biaya_lain;
        $this->ppn = $data->ppn;
        $this->keterangan = $data->keterangan;

        // mount detail
        foreach ($data_detail as $row)
        {
            $this->data_detail [] = [
                'produk_id'=>$row->produk_id,
                'kode_lokal'=>$row->produk->kode_lokal,
                'nama_produk'=>$row->produk->nama."\n".$row->produk->cover."\n".$row->produk->hal,
                'harga'=>$row->harga,
                'jumlah'=>$row->jumlah,
                'diskon'=>$row->diskon,
                'sub_total'=>$row->sub_total
            ];
        }
    }

    public function setProduk(Produk $produk)
    {
        $produk = $this->setProduk_sales($produk);
        $this->hargaProduk = $produk->harga;
        $this->hargaRupiah = rupiah_format($this->hargaProduk);
        $this->diskonProduk = $this->customer_diskon;
        $this->hitungSubTotal();
    }

    /**
     * Helper for table
     */
    public function hitungDiskon(): void
    {
        // hitung harga setelah diskon
        $this->hargaSetelahDiskon = (int) $this->hargaProduk - ($this->hargaProduk * ((float) $this->diskonProduk)/100);
        $this->hargaSetelahDiskonRupiah = rupiah_format((int) $this->hargaSetelahDiskon);
    }

    public function hitungSubTotal(): void
    {
        $this->hitungDiskon();
        $this->subTotalHarga = $this->hargaSetelahDiskon * (int) $this->jumlahProduk;
        $this->subTotalHargaRupiah = rupiah_format($this->subTotalHarga);
    }

    /**
     * Hitung total dari sub_total
     */
    public function hitungTotal() : void
    {
        $this->total = array_sum(array_column($this->data_detail, 'sub_total'));
        $this->total_rupiah = rupiah_format($this->total);
    }

    /**
     * Hitung total bayar
     */
    public function hitungTotalBayar() : void
    {
        $this->total_bayar = (int)$this->total + (int)$this->biaya_lain + (int)$this->ppn;
        $this->total_bayar_rupiah = rupiah_format($this->total_bayar);
    }

    /*****************************
     * Reset Variable from content
     *****************************/

    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset([
            'idProduk', 'namaProduk',
            'hargaProduk', 'hargaRupiah', 'diskonProduk',
            'hargaSetelahDiskon', 'hargaSetelahDiskonRupiah',
            'jumlahProduk',
            'subTotalHarga', 'subTotalHargaRupiah'
        ]);
    }

    /***********************
     * CRUD Table
     ***********************/

    public function addLine(): void
    {
        $this->validate([
            'namaProduk'=>'required',
            'hargaRupiah'=>'required',
            'jumlahProduk'=>'required'
        ]);

        $this->data_detail[] = [
            'produk_id'=>$this->idProduk,
            'kode_lokal'=>$this->kodeLokalProduk,
            'nama_produk'=>$this->namaProduk,
            'harga'=>$this->hargaProduk,
            'jumlah'=>$this->jumlahProduk,
            'diskon'=>$this->diskonProduk,
            'sub_total'=>$this->subTotalHarga
        ];
        $this->resetForm();
        $this->hitungTotal();
        $this->hitungTotalBayar();
    }

    public function editLine($index): void
    {
        $this->update = true;
        $this->indexDetail = $index;
        $this->idProduk = $this->data_detail[$index]['produk_id'];
        $this->namaProduk = $this->data_detail[$index]['nama_produk'];
        $this->hargaProduk = $this->data_detail[$index]['harga'];
        $this->hargaRupiah = rupiah_format($this->hargaProduk);
        $this->jumlahProduk = $this->data_detail[$index]['jumlah'];
        $this->diskonProduk = $this->data_detail[$index]['diskon'];
        $this->subTotalHarga = $this->data_detail[$index]['sub_total'];
        $this->hitungSubTotal();
    }

    public function updateLine(): void
    {
        $this->validate([
            'idProduk'=>'required',
            'jumlahProduk'=>'required'
        ]);

        $index = $this->indexDetail;
        $this->data_detail[$index]['produk_id'] = $this->idProduk;
        $this->data_detail[$index]['nama_produk'] = $this->namaProduk;
        $this->data_detail[$index]['harga'] = $this->hargaProduk;
        $this->data_detail[$index]['jumlah'] = $this->jumlahProduk;
        $this->data_detail[$index]['diskon'] = $this->diskonProduk;
        $this->data_detail[$index]['sub_total'] = $this->subTotalHarga;
        $this->hitungSubTotal();
        $this->resetForm();
        $this->update = false;
        $this->hitungTotal();
        $this->hitungTotalBayar();
    }

    public function destroyLine($index): void
    {
        // remove line transaksi
        unset($this->data_detail[$index]);
        $this->data_detail = array_values($this->data_detail);
        $this->hitungTotal();
        $this->hitungTotalBayar();
    }

}
