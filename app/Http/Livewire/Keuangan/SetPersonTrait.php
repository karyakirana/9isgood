<?php namespace App\Http\Livewire\Keuangan;

use App\Models\Master\PersonRelation;

trait SetPersonTrait
{
    public $person_relation_id, $person_relation_nama;

    public function setPerson(PersonRelation $personRelation)
    {
        $this->person_relation_id = $personRelation->id;
        $this->person_relation_nama = $personRelation->nama;
        $this->emit('hideModalPerson');
    }
}
