<?php namespace App\Http\Livewire\Stock;

use App\Haramain\Traits\LivewireTraits\SetProdukTraits;
use App\Haramain\Traits\LivewireTraits\SetSupplierTraits;
use App\Models\Master\Gudang;
use App\Models\Master\Pegawai;
use App\Models\Master\Produk;
use App\Models\Master\Supplier;
use Livewire\Component;

class StockTransaksi extends Component
{
    use SetProdukTraits, SetSupplierTraits;

    protected $listeners = [
        'set_produk'=>'setProduk',
        'set_supplier'=>'setSupplier',
        'set_pegawai'
    ];

    // first initiate properties
    public $mode = 'create', $update = false;

    // for mounting
    public $gudang_data = [];

    // form master properties
    public $stock_id;
    public $supplier_id, $supplier_nama;
    public $kondisi;
    public $gudang_id, $tgl_keluar, $tgl_masuk, $tgl_input,$keterangan;

    // form detail
    public $data_detail = [], $indexDetail;
    public $idDetail, $idProduk, $namaProduk, $kodeLokalProduk;
    public $coverProduk, $halProduk, $jumlahProduk;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->gudang_data = Gudang::all();
        $this->tgl_input = tanggalan_format(now('ASIA/JAKARTA'));
        $this->tgl_keluar = tanggalan_format(now('ASIA/JAKARTA'));
        $this->tgl_masuk = tanggalan_format(now('ASIA/JAKARTA'));
    }

    public function forMount($mode, $data, $data_detail)
    {
        $this->mode = $mode;
        $this->stock_id = $data->id;
        $this->supplier_id = $data->supplier_id;
        $this->supplier_nama = $data->supplier->nama ?? '';
        $this->kondisi = $data->kondisi ?? $data->jenis;
        $this->gudang_id = $data->gudang_id;
        $this->tgl_keluar = ($data->tgl_keluar) ? tanggalan_format($data->tgl_keluar) : null;
        $this->tgl_masuk = ($data->tgl_masuk) ? tanggalan_format($data->tgl_masuk) : null;
        $this->keterangan = $data->keterangan;

        foreach ($data_detail as $row)
        {
            $this->data_detail [] = [
                'produk_id'=>$row->produk_id,
                'kode_lokal'=>$row->produk->kode_lokal,
                'nama_produk'=>$row->produk->nama."\n".$row->produk->cover."\n".$row->produk->hal,
                'jumlah'=>$row->jumlah,
            ];
        }
    }

    protected function resetForm()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset([
            'idProduk', 'namaProduk', 'jumlahProduk'
        ]);
    }

    public $pegawai_id, $pegawai_nama;

    public function set_pegawai(Pegawai $pegawai)
    {
        $this->pegawai_id = $pegawai->id;
        $this->pegawai_nama = $pegawai->nama;
    }

    public function set_produk(Produk $produk)
    {
        $produk = $this->setProduk_sales($produk);
    }

    public function validatedToTable()
    {
        $this->validate([
            'idProduk'=>'required',
            'jumlahProduk'=>'required'
        ]);
    }

    public function addLine()
    {
        $this->validatedToTable();

        $this->data_detail [] = [
            'produk_id'=>$this->idProduk,
            'kode_lokal'=>$this->kodeLokalProduk,
            'nama_produk'=>$this->namaProduk,
            'supplier_id'=>$this->supplier_id,
            'jumlah'=>$this->jumlahProduk,
        ];
        $this->resetForm();
    }

    public function editLine($index)
    {
        // edit line transaksi
        $this->update = true;
        $this->indexDetail = $index;
        $this->idProduk = $this->data_detail[$index]['produk_id'];
        $this->namaProduk = $this->data_detail[$index]['nama_produk'];
        $this->jumlahProduk = $this->data_detail[$index]['jumlah'];
    }

    public function updateLine()
    {
        // update line transaksi
        $this->validatedToTable();

        $index = $this->indexDetail;
        $this->data_detail[$index]['produk_id'] = $this->idProduk;
        $this->data_detail[$index]['nama_produk'] = $this->namaProduk;
        $this->data_detail[$index]['jumlah'] = $this->jumlahProduk;
        $this->resetForm();
        $this->update = false;
    }

    public function removeLine($index)
    {
        // remove line transaksi
        unset($this->data_detail[$index]);
        $this->data_detail = array_values($this->data_detail);
    }
}
