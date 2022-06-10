<?php

namespace App\Http\Livewire\Penjualan;

use App\Models\Master\Customer;
use App\Models\Master\Produk;
use Livewire\Component;

class PenjualanFormNew extends Component
{
    // attribute form utama
    public int|null $customer_id;
    public float|null $customer_diskon;
    public string|null $customer_nama;

    public string|null $tgl_nota, $tgl_tempo, $jenis_bayar, $keterangan;

    public int|null $produk_id;
    public string|null $produk_nama, $produk_kode_lokal;
    public int|null $produk_harga;
    public float|null $diskon = 0;
    public int|null $diskon_rupiah;
    public int $jumlah = 0;
    public int $sub_total = 0;
    public string|null $sub_total_rupiah;

    public array $data_detail;
    public int|null $indexDetail;
    public bool $update = false;

    public int $total = 0;
    public string|null $total_rupiah;
    public int|null $biaya_lain;
    public int|null $ppn;
    public int $total_bayar = 0;
    public string|null $total_bayar_rupiah;

    public function render()
    {
        return view('livewire.penjualan.penjualan-form-new');
    }

    public function mount()
    {
        //
    }

    public function getCustomer(Customer $customer):void
    {
        $this->customer_id = $customer->id;
        $this->customer_nama = $customer->nama;
        $this->customer_diskon = $customer->diskon;
    }

    public function getProduk(Produk $produk):void
    {
        $this->produk_id = $produk->id;
        $this->produk_kode_lokal = $produk->kode_lokal;
        $this->produk_nama = $produk->nama."\n".$produk->cover."\n".$produk->hal;
        $this->produk_harga = $produk->harga;
    }

    protected function hitungDiskon(int $harga, float $diskon):int
    {
        return $harga - ($harga * $diskon / 100);
    }

    protected function hitungSubTotal(int $hargaSetelahDiskon, int $jumlah):int
    {
        return $hargaSetelahDiskon * $jumlah;
    }

    public function setSubTotal():void
    {
        $hargaSetelahDiskon = $this->hitungDiskon($this->produk_harga, $this->diskon);
        $this->sub_total = $this->hitungSubTotal($hargaSetelahDiskon, $this->jumlah);
        $this->sub_total_rupiah = rupiah_format($this->sub_total);
    }

    public function setTotal():void
    {
        $this->total = array_sum(array_column($this->data_detail, 'sub_total'));
        $this->total_rupiah = rupiah_format($this->total);
    }

    public function setTotalBayar():void
    {
        $this->total_bayar = $this->total + (int) $this->biaya_lain + (int) $this->ppn;
        $this->total_bayar_rupiah = rupiah_format($this->total_bayar);
    }

    public function addLine():void
    {
        $this->validate([
            'produk_nama'=>'required',
            'produk_harga'=>'required',
            'jumlah'=>'required'
        ]);

        $this->dataDetail[] = [
            'kode_lokal'=>$this->produk_kode_lokal,
            'produk_id'=>$this->produk_id,
            'produk_nama'=>$this->produk_nama,
            'harga'=>$this->produk_harga,
            'jumlah'=>$this->jumlah,
            'diskon'=>$this->diskon,
            'sub_total'=>$this->sub_total,
        ];
    }

    public function editLine($index):void
    {
        $this->update = true;
        $this->indexDetail = $index;
        $this->produk_id = $this->data_detail[$index]['produk_id'];
        $this->produk_kode_lokal = $this->data_detail[$index]['kode_lokal'];
        $this->produk_harga = $this->data_detail[$index]['produk_harga'];
        $this->jumlah = $this->data_detail[$index]['jumlah'];
        $this->sub_total = $this->data_detail[$index]['sub_total'];
    }

    public function updateLine():void
    {
        $index = $this->indexDetail;
        $this->data_detail[$index]['produk_id'] = $this->produk_id;
        $this->data_detail[$index]['kode_lokal'] = $this->produk_kode_lokal;
        $this->data_detail[$index]['produk_harga'] = $this->produk_harga;
        $this->data_detail[$index]['jumlah'] = $this->jumlah;
        $this->data_detail[$index]['sub_total'] = $this->sub_total;
        $this->setSubTotal();
        $this->update = false;
    }

    public function removeLine($index):void
    {
        unset($this->data_detail[$index]);
        $this->data_detail = array_values($this->data_detail);
    }
}
