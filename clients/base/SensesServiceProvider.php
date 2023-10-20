<?php

namespace Clients\base;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SensesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        View::addNamespace('Client', __DIR__.'/Resources/views');

        $this->loadRoutesFrom(__DIR__.'/Routes/client-api.php');
        $this->loadRoutesFrom(__DIR__.'/Routes/client-web.php');
    }

}
