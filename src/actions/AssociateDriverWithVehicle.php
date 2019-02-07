<?php

namespace sanabuk\driver\actions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use sanabuk\driver\models\Driver;
use sanabuk\driver\models\Vehicle;
use sanabuk\driver\strategies\AssociationDriverVehicleStrategy;

/**
 * Handling the association between Driver and Vehicle
 *
 * Driver can onby be associated with one Vehicle at time
 * Must be disassociate before a new association
 * */
class AssociateDriverWithVehicle extends Vehicle
{
    /**
     * Handling OneToOne Relation (Driver->Vehicle)
     * TODO Refactoring model->model
     *
     * @param Model|Nullable $vehicle
     * @param Model|Nullable $driver
     * @param array $data
     * @throws Exception $e
     * @return void
     * */
    public function handler($vehicle, $driver, $data = null)
    {
        try {
            $associationDriverVehicleStrategy = new AssociationDriverVehicleStrategy();
            $associationDriverVehicleStrategy($data, $driver, $vehicle);
            $this->detachDriverToVehicle($driver, $vehicle);
            $this->attachDriverToVehicle($vehicle, $driver);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function detachDriverToVehicle($driver, $vehicle)
    {
        if (count($driver->vehicle) > 0) {
            $driver->vehicle()->update(['driver_id' => null]);
        }
    }

    public function attachDriverToVehicle($vehicle, $driver)
    {
        if (!is_null($vehicle) && !is_null($driver->id)) {
            $vehicle->driver()->associate($driver->id);
            $vehicle->save();
        }
    }

    /**
     * On peut imaginer ici énoncer les contraintes d'association spécifiques
     */

    #region Constrains

    #endregion
}
