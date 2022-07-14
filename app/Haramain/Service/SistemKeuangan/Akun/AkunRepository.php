<?php namespace App\Haramain\Service\SistemKeuangan\Akun;

use App\Models\Keuangan\Akun;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AkunRepository
{
    protected function builderAkunTipe($deskripsi): Builder
    {
        return Akun::query()
            ->whereRelation('akunTipe', 'deskripsi', 'like', "%{$deskripsi}%");
    }

    public function getAkunByTipe($type): Collection|array
    {
        return $this->builderAkunTipe($type)->get();
    }
}
