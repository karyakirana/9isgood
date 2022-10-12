<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // role
        Gate::define('SuperAdmin', function (User $user){
            return $user->role == 'SuperAdmin';
        });

        Gate::define('guest', function (User $user){
            return $user->role == 'guest';
        });

        Gate::define('Sales', function (User $user){
            return count(array_intersect(['SuperAdmin', 'Sales'], [$user->role]));
        });

        Gate::define('Keuangan', function (User $user){
            return count(array_intersect(['SuperAdmin', 'Keuangan'],[$user->role]) );
        });

        Gate::define('Kasir', function (User $user){
            return count(array_intersect(['SuperAdmin', 'Keuangan', 'Kasir'], [$user->role]));
        });

        Gate::define('Stock', function (User $user){
            return count(array_intersect(['SuperAdmin', 'Stock'], [$user->role]));
        });

        Gate::define('CheckStock', function (User $user){
            return count(array_intersect(['SuperAdmin', 'Keuangan', 'Kasir', 'Stock'], [$user->role]));
        });
    }
}
