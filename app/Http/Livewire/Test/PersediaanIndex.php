<?php

namespace App\Http\Livewire\Test;

use App\Models\Keuangan\PersediaanTransaksi;
use Livewire\Component;

class PersediaanIndex extends Component
{
    public function render()
    {
        return view('livewire.test.persediaan-index');
    }

    protected $listeners = [
        'detail',
        'detailReset'
    ];

    // var master
    // var detail
    public $data_detail = [];

    public function detail(PersediaanTransaksi $persediaanTransaksi)
    {
        $data_detail = [];
        foreach ($persediaanTransaksi->persediaan_transaksi_detail as $item) {
            $data_detail [] = (object) [
                'kode_lokal'=>$item->produk->kode_lokal,
                'produk'=>$item->produk->nama,
                'produk_kategori_harga'=>$item->produk->kategoriHarga->nama,
                'cover'=>$item->produk->cover,
                'hal'=>$item->produk->hal,
                'harga'=>$item->harga,
                'jumlah'=>$item->jumlah,
                'sub_total'=>$item->sub_total
            ];
        }
        $this->data_detail = $data_detail;
        //dd($persediaanTransaksi->persediaan_transaksi_detail);
    }

    public function detailReset()
    {
        $this->reset(['data_detail']);
    }
}
