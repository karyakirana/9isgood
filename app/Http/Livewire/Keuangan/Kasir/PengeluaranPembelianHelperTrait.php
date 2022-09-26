<?php namespace App\Http\Livewire\Keuangan\Kasir;

use App\Models\Keuangan\HutangPembelian;

trait PengeluaranPembelianHelperTrait
{
    public $dataDetail = [];
    public $update = false;
    public $indexDetail;

    // detail attributes
    public $hutang_pembelian_id;
    public $kurang_bayar;
    public $kode_pembelian;
    public $jenis_pembelian;
    public $pembelian;

    // attribute pengeluaran_pembelian_detail
    public $status_bayar;
    public $nominal_dibayar;

    public function setHutangPembelian(HutangPembelian $hutangPembelian)
    {
        $this->hutang_pembelian_id = $hutangPembelian->id;
        $this->status_bayar = $hutangPembelian->status_bayar;
        $this->kurang_bayar = $hutangPembelian->kurang_bayar;
        $this->kode_pembelian = $hutangPembelian->hutangablePembelian->kode;
        $this->jenis_pembelian = class_basename($hutangPembelian->pembelian_type);
        $this->pembelian = class_basename($hutangPembelian->hutangablePembelian->jenis);
        $this->emit('showFormHutangPembelian');
    }

    protected function resetFormDetail()
    {
        $this->reset(['hutang_pembelian_id', 'nominal_dibayar', 'kurang_bayar', 'kode_pembelian', 'jenis_pembelian', 'pembelian']);
    }

    public $total_dibayar;

    public function setTotalDibayar()
    {
        $this->total_dibayar = array_sum(array_column($this->dataDetail, 'nominal_dibayar'));
        $this->supplier_saldo = $this->supplier_saldo - $this->total_dibayar;
    }

    public function addLine()
    {
        $this->status_bayar = ($this->nominal_dibayar == 0) ? 'lunas' : 'kurang';
        $this->dataDetail[] = [
            'hutang_pembelian_id'=>$this->hutang_pembelian_id,
            'status_bayar'=>$this->status_bayar,
            'nominal_dibayar'=>$this->nominal_dibayar,
            'kurang_bayar'=>$this->kurang_bayar,
            'kode_pembelian'=>$this->kode_pembelian,
            'jenis_pembelian'=>$this->jenis_pembelian,
            'pembelian'=>$this->pembelian
        ];
        $this->emit('hideFormHutangPembelian');
        $this->resetFormDetail();
        $this->setTotalDibayar();
    }

    public function editLine($index)
    {
        $this->update = true;
        $this->indexDetail = $index;
        $this->hutang_pembelian_id = $this->dataDetail[$index]['hutang_pembelian_id'];
        $this->status_bayar = $this->dataDetail[$index]['status_bayar'];
        $this->nominal_dibayar = $this->dataDetail[$index]['nominal_dibayar'];
        $this->kurang_bayar = $this->dataDetail[$index]['kurang_bayar'];
        $this->kode_pembelian = $this->dataDetail[$index]['kode_pembelian'];
        $this->jenis_pembelian = $this->dataDetail[$index]['jenis_pembelian'];
        $this->pembelian = $this->dataDetail[$index]['pembelian'];
        $this->emit('showFormHutangPembelian');
        $this->supplier_saldo = $this->supplier_saldo + $this->total_dibayar;
    }

    public function updateLine()
    {
        $this->status_bayar = ($this->nominal_dibayar == 0) ? 'lunas' : 'kurang';
        $index = $this->indexDetail;
        $this->dataDetail[$index]['hutang_pembelian_id'] = $this->hutang_pembelian_id;
        $this->dataDetail[$index]['status_bayar'] = $this->status_bayar;
        $this->dataDetail[$index]['nominal_dibayar'] = $this->nominal_dibayar;
        $this->dataDetail[$index]['kurang_bayar'] = $this->kurang_bayar;
        $this->dataDetail[$index]['kode_pembelian'] = $this->kode_pembelian;
        $this->dataDetail[$index]['jenis_pembelian'] = $this->jenis_pembelian;
        $this->dataDetail[$index]['pembelian'] = $this->pembelian;
        $this->update = false;
        $this->emit('hideFormHutangPembelian');
        $this->resetFormDetail();
        $this->setTotalDibayar();
    }

    public function destroyLine($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
    }

    public $dataPayment = [];

    public function openPayment()
    {
        $this->data = $this->formValidate();
        $this->dataPayment[] = [
            'akun_id'=>null,
            'nominal'=> 0
        ];
        $this->emit('showPayment');
    }

    public function addPayment()
    {
        $this->dataPayment[] = [
            'akun_id'=>'',
            'nominal'=> 0
        ];
    }

    public function deletePayment($index)
    {
        unset($this->dataPayment[$index]);
        $this->dataPayment = array_values($this->dataPayment);
    }
}
