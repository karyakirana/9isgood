<?php

namespace App\Models\Master;

use App\Haramain\Traits\ModelTraits\KodeTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonRelation extends Model
{
    use HasFactory, KodeTraits;
    protected $table = 'haramainv2.person_relation';
    protected $fillable = [
        'kode',
        'nama',
        'telepon',
        'alamat',
        'keterangan'
    ];
}
