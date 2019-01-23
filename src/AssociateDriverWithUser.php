<?php

namespace sanabuk\driver;

use App\User;
use sanabuk\driver\Driver;
use Exception;

class AssociateDriverWithUser extends Driver
{
	public function handler(Driver $driver, User $model)
	{
		try{
			$this->testEligibilityUser($model)
			$driver->user()->associate($model);
			$driver->save();
		} catch (Exception $e){
			throw $e;	
		}
	}

	/**
	 * Un user ne peut pas avoir plus de X drivers
	 */
	public function testEligibilityUser(User $model)
	{
		if($model->drivers->count > 1) throw new Exception("Error Processing Request", 422);
	}

}