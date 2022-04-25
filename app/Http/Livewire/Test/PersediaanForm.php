<?php

namespace App\Http\Livewire\Test;

use App\Haramain\Repository\Persediaan\PersediaanRepository;
use App\Haramain\Repository\Persediaan\PersediaanTransaksiRepo;
use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Master\Gudang;
use App\Models\Master\Produk;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PersediaanForm extends Component
{
    public function render()
    {
        return view('livewire.test.persediaan-form');
    }

    protected $listeners = [
        'set_produk'
    ];

    // car elements
    public $disabled = false;

    // var form master
    public $persediaan_transaksi_id;
    public $mode = 'create';
    public $kondisi;
    public $gudang_id, $gudang_nama, $gudang_data;
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
        $this->gudang_data = Gudang::oldest()->get();
        if ($persediaan)
        {
            $persediaan = PersediaanTransaksi::query()->find($persediaan);
            $this->persediaan_transaksi_id = $persediaan->id;
            $this->mode = 'update';
            $this->kondisi = $persediaan->kondisi;
            $this->gudang_id = $persediaan->gudang_id;
            $this->jenis = $persediaan->jenis;

            // data detail
            foreach ($persediaan->persediaan_transaksi_detail as $item) {
                $this->data_detail[] = [
                    'produk_id'=>$item->produk_id,
                    'produk_nama'=>$item->produk->nama."\n".$item->produk->kategoriHarga->nama." ".$item->cover,
                    'produk_kode_lokal'=>$item->produk->kode_lokal,
                    'produk_harga'=>$item->produk->harga,
                    'harga'=>$item->harga,
                    'jumlah'=>$item->jumlah,
                    'sub_total'=>$item->sub_total
                ];
            }
        }
    }

    public function set_produk(Produk $produk)
    {
        $this->produk_id =$produk->id;
        $this->produk_kode_lokal = $produk->kode_lokal;
        $this->produk_nama = $produk->nama."\n".$produk->kategoriHarga->nama." ".$produk->cover;
        $this->produk_harga = $produk->harga;
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
            'jenis'=>'required',
            'produk_nama'=>'required',
            'harga'=>'required',
            'jumlah'=>'required'
        ]);
    }

    public function checkAvailability()
    {
        if ($this->jenis == 'keluar'){
            return (new PersediaanRepository())->check($this->produk_id, $this->gudang_id, $this->kondisi, $this->jumlah);
        }
        return (object)['status'=>true];
    }

    public function add()
    {
        // validate
        $this->validateFormDetail();
        // check jumlah
        $checkValidate = $this->checkAvailability();
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
            $this->disabled = true;
            $this->resetForm();
        } else {
            session()->flash('error jumlah', $checkValidate->keterangan);
        }
    }

    public function edit($index)
    {
        $this->update = true;
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
        $checkValidate = $this->checkAvailability();
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

    protected function validateFormMaster(){
        return $this->validate([
            'persediaan_transaksi_id'=>'nullable',
            'kondisi'=>'required',
            'jenis'=>'required',
            'gudang_id'=>'required',
            'jenis'=>'required',
            'data_detail'=>'required'
        ]);
    }

    public function store()
    {
        $data = $this->validateFormMaster();
        DB::beginTransaction();
        try {
            (new PersediaanTransaksiRepo())->store((object)$data);
            DB::commit();
            return redirect()->route('test.persediaan.index');
        } catch (ModelNotFoundException $e){
            DB::rollBack();
        }
        return null;
    }

    public function put()
    {
        $data = $this->validateFormMaster();
        DB::beginTransaction();
        try {
            (new PersediaanTransaksiRepo())->update((object)$data);
            DB::commit();
            return redirect()->route('test.persediaan.index');
        } catch (ModelNotFoundException $e){
            DB::rollBack();
        }
        return null;
    }
}
