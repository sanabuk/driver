<?php

namespace sanabuk\driver\actions;

use Illuminate\Database\Eloquent\Model;
use sanabuk\driver\models\Driver;
use Exception;

class AssociateDriverWithUser extends Driver
{
	public function handler(Driver $driver, Model $model)
	{
		try{
			$this->eligibilityUser($model);
			$driver->user()->associate($model);
			$driver->save();
		} catch (Exception $e){
			throw $e;	
		}
	}

	/**
	 * On peut imaginer ici énoncer les contraintes spécifiques
	 * fake example :
	 */

	#region Constrains

	/**
	 * Un user ne peut pas avoir plus de X drivers
	 * @param Model
	 * @mixed
	 */
	public function eligibilityUser(Model $model)
	{
		if($model->drivers->count() > 1000) throw new Exception("Cet utilisateur a déjà le maximum de drivers.(".$model->drivers->count().")", 403);
	}

	#endregion
}