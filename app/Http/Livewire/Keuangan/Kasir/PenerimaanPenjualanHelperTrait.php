<?php namespace App\Http\Livewire\Keuangan\Kasir;

use App\Models\Keuangan\PiutangPenjualan;

trait PenerimaanPenjualanHelperTrait
{
    public $dataDetail = [];
    public $update = false;
    public $indexDetail;

    // detail attributes
    public $piutang_penjualan_id;
    public $kurang_bayar;
    public $kode_penjualan;
    public $jenis_penjualan;
    public $kurang_bayar_sebelumnya;

    // attribute penerimaan_penjualan_detail
    public $status_bayar;
    public $nominal_dibayar;

    public function setPiutangPenjualan(PiutangPenjualan $piutangPenjualan)
    {
        $this->piutang_penjualan_id = $piutangPenjualan->id;
        $this->status_bayar = $piutangPenjualan->status_bayar;
        $this->kurang_bayar_sebelumnya = $piutangPenjualan->kurang_bayar;
        $this->kode_penjualan = $piutangPenjualan->piutangablePenjualan->kode;
        $this->jenis_penjualan = class_basename($piutangPenjualan->penjualan_type);
        $this->emit('showFormPiutangPenjualan');
    }

    protected function resetFormDetail()
    {
        $this->reset(['piutang_penjualan_id', 'nominal_dibayar', 'kurang_bayar', 'kurang_bayar_sebelumnya','kode_penjualan', 'jenis_penjualan']);
    }

    public function addLine()
    {
        $this->status_bayar = ($this->nominal_dibayar == 0) ? 'lunas' : 'kurang';
        $this->dataDetail[] = [
            'piutang_penjualan_id'=>$this->piutang_penjualan_id,
            'status_bayar'=>$this->status_bayar,
            'nominal_dibayar'=>$this->nominal_dibayar,
            'kurang_bayar_sebelumnya'=>$this->kurang_bayar_sebelumnya,
            'kurang_bayar'=>$this->kurang_bayar_sebelumnya - $this->nominal_dibayar,
            'kode_penjualan'=>$this->kode_penjualan,
            'jenis_penjualan'=>$this->jenis_penjualan
        ];
        $this->emit('hideFormPiutangPenjualan');
        $this->resetFormDetail();
        $this->setTotalDibayar();
    }

    public function editLine($index)
    {
        $this->update = true;
        $this->indexDetail = $index;
        $this->piutang_penjualan_id = $this->dataDetail[$index]['piutang_penjualan_id'];
        $this->status_bayar = $this->dataDetail[$index]['status_bayar'];
        $this->nominal_dibayar = $this->dataDetail[$index]['nominal_dibayar'];
        $this->kurang_bayar_sebelumnya = $this->dataDetail[$index]['kurang_bayar_sebelumnya'];
        $this->kode_penjualan = $this->dataDetail[$index]['kode_penjualan'];
        $this->jenis_penjualan = $this->dataDetail[$index]['jenis_penjualan'];
        $this->emit('showFormPiutangPenjualan');
        $this->customer_saldo = $this->customer_saldo + $this->total_dibayar;
    }

    public function updateLine()
    {
        $this->status_bayar = ($this->nominal_dibayar == 0) ? 'lunas' : 'kurang';
        $index = $this->indexDetail;
        $this->dataDetail[$index]['hutang_penjualan_id'] = $this->piutang_penjualan_id;
        $this->dataDetail[$index]['status_bayar'] = $this->status_bayar;
        $this->dataDetail[$index]['nominal_dibayar'] = $this->nominal_dibayar;
        $this->dataDetail[$index]['kurang_bayar_sebelumhya'] = $this->kurang_bayar_sebelumnya;
        $this->dataDetail[$index]['kurang_bayar'] = $this->kurang_bayar_sebelumnya - $this->nominal_dibayar;
        $this->dataDetail[$index]['kode_penjualan'] = $this->kode_penjualan;
        $this->dataDetail[$index]['jenis_penjualan'] = $this->jenis_penjualan;
        $this->update = true;
        $this->emit('hideFormPiutangPenjualan');
        $this->resetFormDetail();
        $this->setTotalDibayar();
    }

    public function destroyLine($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
    }
}
