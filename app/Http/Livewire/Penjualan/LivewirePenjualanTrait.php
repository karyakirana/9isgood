<?php namespace App\Http\Livewire\Penjualan;

use App\Models\Master\Customer;

trait LivewirePenjualanTrait
{
    public $customerId, $customerNama, $customerDiskon;

    public function setCustomer($customerId)
    {
        $customer = Customer::query()->find($customerId);
        $this->customerId = $customerId;
        $this->customerNama = $customer->nama;
        $this->customerDiskon = $customer->diskon;
    }

    public $produkId;
    public $produkNama, $produkKodeLokal, $produkKategori, $produkKategoriHarga, $produkCover;
    public $harga, $hargaRupiah;
    public $diskon;

    protected function setDetailFromProduk($produk)
    {
        $this->produkId = $produk->id;
        $this->produkNama = $produk->nama."\n".$produk->kode_lokal."\n".$produk->kategoriHarga->deskripsi."\n".$produk->cover;
        $this->produkKodeLokal = $produk->kode_lokal;
        $this->produkKategori = $produk->kategori->nama;
        $this->produkKategoriHarga = $produk->kategoriHarga->deskripsi;
        $this->harga = $produk->harga;
        $this->hargaRupiah = rupiah_format((int)$this->harga);
        $this->diskon = $this->customerDiskon ?? 0;
    }

    public $hargaDiskon, $hargaDiskonRupiah;

    protected function setDiskon(): void
    {
        $this->hargaDiskon = (int)$this->harga - ((int)$this->harga * (float)$this->diskon/100);
        $this->hargaDiskonRupiah = rupiah_format((int)$this->hargaDiskon);
    }

    public $subTotal, $subTotalRupiah;

    public function setSubTotal():void
    {
        $this->setDiskon();
        $this->subTotal = $this->hargaDiskon * (int)$this->jumlah;
        $this->subTotalRupiah = rupiah_format($this->subTotal);
    }

    /**
     * validate form detail
     * @return void
     */
    protected function validateFormDetail():void
    {
        $this->validate([
            'produkNama'=>'required',
            'jumlah'=>'required|integer',
            'diskon'=>'required',
        ]);
    }

    protected function setDataDetail():void
    {
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
    }

    protected function getDataDetail($index):void
    {
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
    }

    protected function updateDataDetail($index)
    {
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
    }

    /** reset form validation */
    protected function resetForm()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
