<?php namespace App\Haramain\Service\SystemCore;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SessionScope implements Scope
{
    /**
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model):void
    {
        $builder->where('active_cash', session('ClosedCash'));
    }
}
