<?php

namespace App\Http\Livewire\Master;

use App\Models\Master\PersonRelation;
use Livewire\Component;

class PersonRelationIndex extends Component
{
    protected $listeners = [
        'edit',
        'destroy'
    ];

    public $person_relation_id;
    public $kode;
    public $nama;
    public $telepon;
    public $alamat;
    public $keterangan;

    public $update = false;

    protected function kode()
    {
        $pegawai = PersonRelation::latest('kode')->first();
        if (!$pegawai){
            $num = 1;
        } else {
            $lastNum = (int) $pegawai->last_num_master;
            $num = $lastNum + 1;
        }
        return "P".sprintf("%05s", $num);
    }

    public function store()
    {
        PersonRelation::create([
            'kode' => $this->kode(),
            'nama' => $this->nama,
            'telepon' => $this->telepon,
            'alamat' => $this->alamat,
            'keterangan' => $this->keterangan
        ]);
        $this->emit('hideModalPerson');
        $this->emit('refreshDatatable');
    }

    public function edit($id)
    {
        $person = PersonRelation::find($id);
        $this->person_relation_id = $person->id;
        $this->nama = $person->nama;
        $this->telepon = $person->telepon;
        $this->alamat = $person->alamat;
        $this->keterangan = $person->keterangan;
        $this->update = true;
        $this->emit('showModalPerson');
    }

    public function update()
    {
        $person = PersonRelation::find($this->person_relation_id);
        $person->update([
            'nama' => $this->nama,
            'telepon' => $this->telepon,
            'alamat' => $this->alamat,
            'keterangan' => $this->keterangan
        ]);
        $this->emit('hideModalPerson');
        $this->emit('refreshDatatable');
    }

    public function destroy($id)
    {
        PersonRelation::destroy($id);
        $this->emit('refreshDatatable');
    }

    public function render()
    {
        return view('livewire.master.person-relation');
    }
}
