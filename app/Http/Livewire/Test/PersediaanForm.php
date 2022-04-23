<?php

namespace App\Http\Livewire\Test;

use App\Haramain\Repository\Persediaan\PersediaanRepository;
use Livewire\Component;

class PersediaanForm extends Component
{
    public function render()
    {
        return view('livewire.test.persediaan-form');
    }

    // var form master
    public $persediaan_transaksi_id;
    public $mode = 'create';
    public $kondisi;
    public $gudang_id, $gudang_nama;
    public $jenis, $total_barang;

    // var form detail
    public $index;
    public $update = false;
    public $data_detail = [];
    public $produk_id, $produk_nama, $produk_kode_lokal;
    public $produk_harga;
    public $harga, $jumlah, $sub_total;

    public function mount($persediaan = null)
    {
        //
    }

    protected function resetForm()
    {
        $this->reset([
            'produk_id', 'produk_nama', 'produk_harga', 'harga', 'jumlah', 'sub_total'
        ]);
    }

    public function hitungSubTotal()
    {
        $this->sub_total = (int) $this->harga * (int) $this->jumlah;
    }

    public function validateFormDetail()
    {
        $this->validate([
            'gudang_id'=>'required',
            'kondisi'=>'required',
            'produk_nama'=>'required',
            'harga'=>'required',
            'jumlah'=>'required'
        ]);
    }

    public function add()
    {
        // validate
        $this->validateFormDetail();
        // check jumlah
        $checkValidate = (new PersediaanRepository())->check($this->produk_id, $this->gudang_id, $this->kondisi, $this->jumlah);
        if ($checkValidate->status){
            // jika berhasil maka akan nambah line
            $this->data_detail[] = [
                'produk_id'=>$this->produk_id,
                'produk_nama'=>$this->produk_nama,
                'produk_kode_lokal'=>$this->produk_kode_lokal,
                'produk_harga'=>$this->produk_harga,
                'harga'=>$this->harga,
                'jumlah'=>$this->jumlah,
                'sub_total'=>$this->sub_total
            ];
            $this->resetForm();
        } else {
            session()->flash('error jumlah', $checkValidate->keterangan);
        }
    }

    public function edit($index)
    {
        $this->index = $index;
        $this->produk_id = $this->data_detail[$index]['produk_id'];
        $this->produk_nama = $this->data_detail[$index]['produk_nama'];
        $this->produk_kode_lokal = $this->data_detail[$index]['produk_kode_lokal'];
        $this->produk_harga = $this->data_detail[$index]['produk_harga'];
        $this->harga = $this->data_detail[$index]['harga'];
        $this->jumlah = $this->data_detail[$index]['jumlah'];
        $this->sub_total = $this->data_detail[$index]['sub_total'];
    }

    public function update()
    {
        // validate
        $this->validateFormDetail();
        // check jumlah
        $checkValidate = (new PersediaanRepository())->check($this->produk_id, $this->gudang_id, $this->kondisi, $this->jumlah);
        if ($checkValidate->status){
            $index = $this->index;
            $this->data_detail[$index]['produk_id'] = $this->produk_id;
            $this->data_detail[$index]['produk_nama'] = $this->produk_nama;
            $this->data_detail[$index]['produk_kode_lokal'] = $this->produk_kode_lokal;
            $this->data_detail[$index]['produk_harga'] = $this->produk_harga;
            $this->data_detail[$index]['harga'] = $this->harga;
            $this->data_detail[$index]['jumlah'] = $this->jumlah;
            $this->data_detail[$index]['sub_total'] = $this->sub_total;
            $this->resetForm();
        } else {
            session()->flash('error jumlah', $checkValidate->keterangan);
        }

    }

    public function destroy($index)
    {
        unset($this->data_detail[$index]);
        $this->data_detail = array_values($this->data_detail);
    }
}
