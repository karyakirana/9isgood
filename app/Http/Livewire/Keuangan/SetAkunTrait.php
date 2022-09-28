<?php namespace App\Http\Livewire\Keuangan;

use App\Models\Keuangan\Akun;

trait SetAkunTrait
{
    public $akun_id, $akun_nama, $akun_kode;

    public function setAkun(Akun $akun)
    {
        $this->akun_id = $akun->id;
        $this->akun_nama = $akun->deskripsi;
        $this->akun_kode = $akun->kode;
        $this->emit('hideModalAkun');
    }
}
