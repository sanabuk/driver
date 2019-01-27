<?php

namespace sanabuk\driver\actions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use sanabuk\driver\models\Vehicle;
use sanabuk\driver\models\Driver;

class AssociateDriverWithVehicle extends Vehicle
{
    public function handler(Vehicle $vehicle, Model $model)
    {
        try {
            $this->detachDriverToVehicle($model);
            $vehicle->update(['driver_id' => $model->id]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function detachDriverToVehicle(Driver $model)
    {
    	if(count($model->vehicle) > 0){
    		$model->vehicle->update(['driver_id' => null]);
    	}
    }

    /**
     * On peut imaginer ici énoncer les contraintes d'association spécifiques
     */

    #region Constrains

    #endregion
}
