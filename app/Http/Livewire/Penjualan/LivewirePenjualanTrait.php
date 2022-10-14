<?php namespace App\Http\Livewire\Penjualan;

use App\Models\Master\Customer;

trait LivewirePenjualanTrait
{
    // detail attributes
    public $diskon;
    public $jumlah;
    public $sub_total, $sub_total_rupiah;

    public $dataDetail = [];
    public $update = false;
    public $index;

    protected function setDataDetail($dataDetail)
    {
        foreach ($dataDetail as $item) {
            $this->dataDetail[] = [
                'produk_id'=>$item->produk_id,
                'kode_lokal'=>$item->produk->kode_lokal,
                'produk_nama'=>$item->produk->nama."\n"
                    .$item->produk->kode_lokal."\n"
                    .$item->produk->kategoriHarga->deskripsi."\n"
                    .$item->produk->cover,
                'harga'=>$item->harga,
                'diskon'=>$item->diskon,
                'jumlah'=>$item->jumlah,
                'sub_total'=>$item->sub_total
            ];
        }
    }

    public function setSubTotal()
    {
        $this->sub_total = (int) $this->harga_setelah_diskon * (int) $this->jumlah;
        $this->sub_total_rupiah = rupiah_format($this->sub_total);
    }

    public function updatedJumlah()
    {
        $this->setSubTotal();
    }

    public function updatedDiskon($value)
    {
        $this->setSubTotal();
        $this->harga_setelah_diskon = (int)$this->harga - (int) ( $this->harga * ((float)$this->diskon / 100));
    }

    public function addLine()
    {
        $this->dataDetail[] = [
            'produk_id'=>$this->produk_id,
            'kode_lokal'=>$this->kode_lokal,
            'produk_nama'=>$this->produk_nama,
            'harga'=>$this->harga,
            'diskon'=>$this->diskon,
            'jumlah'=>$this->jumlah,
            'sub_total'=>$this->sub_total
        ];
        $this->setTotalForm();
        $this->resetFormValidation();
        $this->resetFormDetailAttribute();
    }

    public function editLine($index)
    {
        $this->resetFormDetailAttribute();
        $this->update = true;
        $this->index = $index;
        $this->produk_id = $this->dataDetail[$index]['produk_id'];
        $this->kode_lokal = $this->dataDetail[$index]['kode_lokal'];
        $this->produk_nama = $this->dataDetail[$index]['produk_nama'];
        $this->harga = $this->dataDetail[$index]['harga'];
        $this->harga_rupiah = rupiah_format($this->harga);
        $this->jumlah = $this->dataDetail[$index]['jumlah'];
        $this->sub_total = $this->dataDetail[$index]['sub_total'];
        $this->subTotalRupiah = $this->sub_total;
    }

    public function updateLine()
    {
        $index = $this->index;
        $this->dataDetail[$index]['produk_id'] = $this->produk_id;
        $this->dataDetail[$index]['kode_lokal'] = $this->kode_lokal;
        $this->dataDetail[$index]['produk_nama'] = $this->produk_nama;
        $this->dataDetail[$index]['harga'] = $this->harga;
        $this->dataDetail[$index]['jumlah'] = $this->jumlah;
        $this->dataDetail[$index]['sub_total'] = $this->sub_total;
        $this->update = false;
        $this->setTotalForm();
        $this->resetFormDetailAttribute();
    }

    public function destroyLine($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
    }

    protected function setTotalForm()
    {
        $this->total_barang = array_sum(array_column($this->dataDetail, 'jumlah'));
        $this->total_penjualan = array_sum(array_column($this->dataDetail, 'sub_total'));
        $this->total_penjualan_rupiah = $this->total_penjualan;
        $this->total_bayar = (int) $this->total_penjualan + (float) $this->ppn + (int) $this->biaya_lain;
        $this->total_bayar_rupiah = rupiah_format($this->total_bayar);
    }

    protected function resetFormDetailAttribute()
    {
        $this->resetFormValidation();
        $this->reset([
            'index',
            'produk_id', 'kode_lokal', 'produk_nama', 'harga', 'diskon',
            'harga_rupiah',
            'harga_setelah_diskon',
            'jumlah',
            'sub_total', 'sub_total_rupiah'
        ]);
    }

    protected function resetFormValidation()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
