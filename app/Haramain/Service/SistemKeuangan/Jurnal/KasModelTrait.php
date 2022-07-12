<?php namespace App\Haramain\Service\SistemKeuangan\Jurnal;

use App\Models\Keuangan\JurnalKas;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait KasModelTrait
{
    public function jurnalKas(): MorphOne
    {
        return $this->morphOne(JurnalKas::class, 'jurnalable_kas', 'cash_type', 'cash_id');
    }
}
