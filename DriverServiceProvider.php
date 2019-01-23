<?php
namespace sanabuk\driver;

use Illuminate\Support\ServiceProvider;
use sanabuk\driver\Driver;
use sanabuk\driver\CreateDriver;

class DriverServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadMigrationsFrom('/sanabuk/driver/src/migrations');
    }

    public function register()
    {
        $this->app->bind('Driver', function ($app) {
            return new Driver();
        });

        $this->app->bind('CreateDriver', function ($app) {
            return new CreateDriver();
        });
    }

}
