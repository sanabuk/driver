<?php

namespace sanabuk\driver\actions;

use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use sanabuk\driver\models\Driver;
use sanabuk\driver\models\Vehicle;

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
     * @param Model|Nullable $model
     * @throws Exception $e
     * @return void
     * */
    public function handler($vehicle, $model)
    {
        try {
            $this->detachDriverToVehicle($model);
            $this->attachDriverToVehicle($vehicle, $model);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function detachDriverToVehicle(Driver $model)
    {
        if (count($model->vehicle) > 0) {
            $model->vehicle()->dissociate();
        }
    }

    public function attachDriverToVehicle(Vehicle $vehicle, $model)
    {
        if(!is_null($vehicle) && !is_null($model->id)){
            $model->vehicle()->associate($model->id);
            $model->save();
        }
    }

    /**
     * On peut imaginer ici énoncer les contraintes d'association spécifiques
     */

    #region Constrains

    #endregion
}
