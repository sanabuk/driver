<?php
namespace sanabuk\driver;

use Illuminate\Support\ServiceProvider;
use sanabuk\driver\models\Driver;
use sanabuk\driver\actions\CreateDriver;
use sanabuk\driver\actions\AssociateDriverWithUser;
use sanabuk\driver\actions\AssociateDriverWithVehicle;
use sanabuk\driver\resources\GetHistoric;

class DriverServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }

    public function register()
    {
        $this->app->bind('Driver', function ($app) {
            $driver = new Driver();
        });

        $this->app->bind('CreateDriver', function ($app) {
            return new CreateDriver();
        });

        $this->app->bind('AssociateDriverWithUser', function ($app) {
            return new AssociateDriverWithUser();
        });

        $this->app->bind('AssociateDriverWithVehicle', function ($app) {
            return new AssociateDriverWithVehicle();
        });

        $this->app->bind('AssociationDriverVehicleStrategy', function ($app) {
            return new AssociationDriverVehicleStrategy();
        });

        $this->app->bind('GetHistoric', function ($app) {
            return new GetHistoric();
        });
    }

}
