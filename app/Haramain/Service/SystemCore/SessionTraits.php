<?php namespace App\Haramain\Service\SystemCore;

trait SessionTraits
{
    public function sessionActive()
    {
        return session('ClosedCash');
    }
}
