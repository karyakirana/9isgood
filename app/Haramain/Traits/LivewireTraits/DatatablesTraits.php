<?php

namespace App\Haramain\Traits\LivewireTraits;

trait DatatablesTraits
{
    public function setTableClass(): ?string
    {
        return 'table table-striped gx-7 border';
    }

    public function setTableRowClass(): ?string
    {
        return 'border align-middle';
    }

    public function setFooterRowClass($rows): ?string
    {
        return 'text-end fw-bolder border fs-4';
    }
}
