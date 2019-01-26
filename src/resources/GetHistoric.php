<?php

namespace sanabuk\driver\resources;

use Illuminate\Database\Eloquent\Model;
use sanabuk\driver\models\HistoryDriverVehicle;

/**
 * 
 */
class GetHistoric
{
	public function handler(Model $model)
	{
		return $model->historic;
	}
}