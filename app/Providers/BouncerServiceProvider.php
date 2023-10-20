<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\Ability;
use Bouncer;
use Illuminate\Support\ServiceProvider;

class BouncerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Bouncer::cache();
        Bouncer::useAbilityModel(Ability::class);
        Bouncer::useRoleModel(Role::class);
    }
}
