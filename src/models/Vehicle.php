<?php

namespace sanabuk\driver\models;

use Illuminate\Database\Eloquent\Model;
use sanabuk\driver\Groupable;

/**
 * Vehicle Model
 */
class Vehicle extends Model
{
	use Groupable;
	
	protected $table = 'vehicles';

	protected $fillable = ['license_number','color','brand','driver_id'];

	public function driver()
	{
		return $this->belongsTo(Driver::class);
	}

	public function historic()
    {
        return $this->hasMany(HistoryDriverVehicle::class)->orderBy('updated_at');
    }
}