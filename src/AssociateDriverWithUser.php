<?php

namespace sanabuk\driver;

use Illuminate\Database\Eloquent\Model;
use sanabuk\driver\Driver;
use Exception;

class AssociateDriverWithUser extends Driver
{
	public function handler(Driver $driver, Model $model)
	{
		try{
			$this->testEligibilityUser($model);
			$driver->user()->associate($model);
			$driver->save();
		} catch (Exception $e){
			throw $e;	
		}
	}

	/**
	 * Un user ne peut pas avoir plus de X drivers
	 * @param Model
	 * @mixed
	 */
	public function testEligibilityUser(Model $model)
	{
		if($model->drivers->count() > 10) throw new Exception("Cet utilisateur a déjà le maximum de drivers.(".$model->drivers->count().")", 403);
	}

}