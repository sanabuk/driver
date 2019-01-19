<?php
namespace sanabuk\driver;

use Illuminate\Support\ServiceProvider;

class DriverServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadMigrationsFrom('/sanabuck/driver/src/migrations');
    }

    public function register()
    {
        $this->app->bind('Driver', function ($app) {
            return new Driver();
        });
    }

}
