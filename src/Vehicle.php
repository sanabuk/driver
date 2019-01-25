<?php

namespace sanabuk\driver;

use Illuminate\Database\Eloquent\Model;

/**
 * Vehicle Model
 */
class Vehicle extends Model
{
	protected $table = 'vehicles';

	protected $fillable = ['license_number','color','brand','driver_id'];

	public function driver()
	{
		return $this->belongsTo(Driver::class);
	}
}