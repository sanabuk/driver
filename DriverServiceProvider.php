<?php
namespace sanabuk\driver;

use Illuminate\Support\ServiceProvider;
use sanabuk\driver\models\Driver;
use sanabuk\driver\actions\CreateDriver;
use sanabuk\driver\actions\AssociateDriverWithUser;

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
