<?php
namespace sanabuk\driver;

use Illuminate\Support\ServiceProvider;

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

        $this->app->bind('AssociateDriverWithUser', function ($app) {
            return new AssociateDriverWithUser();
        });
    }

}
