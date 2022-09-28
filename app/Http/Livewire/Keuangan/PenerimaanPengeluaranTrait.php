<?php namespace App\Http\Livewire\Keuangan;

trait PenerimaanPengeluaranTrait
{
    public $nominal;
    public $nominal_detail;

    public $dataDetail = [];

    public $update = false;
    public $index;

    protected function resetFormDetail()
    {
        $this->reset(['akun_id', 'akun_nama', 'akun_kode', 'nominal_detail']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    protected function setNominal()
    {
        $this->nominal = array_sum(array_column($this->dataDetail, 'nominal'));
    }

    public function addLine()
    {
        $this->validate([
            'akun_nama'=>'required',
            'nominal_detail'=>'required'
        ]);
        $this->dataDetail[] = [
            'akun_id'=>$this->akun_id,
            'akun_nama'=>$this->akun_nama,
            'akun_kode'=>$this->akun_kode,
            'nominal'=>$this->nominal_detail
        ];
        $this->setNominal();
        $this->resetFormDetail();
    }

    public function editLine($index)
    {
        $this->index = $index;
        $this->akun_id = $this->dataDetail[$index]['akun_id'];
        $this->akun_nama = $this->dataDetail[$index]['akun_nama'];
        $this->akun_kode = $this->dataDetail[$index]['akun_kode'];
        $this->nominal_detail = $this->dataDetail[$index]['nominal'];
        $this->update = true;
    }

    public function updateLine()
    {
        $this->validate([
            'akun_nama'=>'required',
            'nominal_detail'=>'required'
        ]);
        $index = $this->index;
        $this->dataDetail[$index]['akun_id'] = $this->akun_id;
        $this->dataDetail[$index]['akun_nama'] = $this->akun_nama;
        $this->dataDetail[$index]['akun_kode'] = $this->akun_kode;
        $this->dataDetail[$index]['nominal'] = $this->nominal_detail;
        $this->update = false;
        $this->setNominal();
        $this->resetFormDetail();
    }

    public function destroyLine($index)
    {
        unset($this->dataDetail[$index]);
        $this->dataDetail = array_values($this->dataDetail);
    }
}
