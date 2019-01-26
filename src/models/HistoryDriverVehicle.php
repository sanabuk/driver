<?php

namespace sanabuk\driver\models;

use Illuminate\Database\Eloquent\Model;

class HistoryDriverVehicle extends Model
{
	protected $table = 'vehicle_driver_history';

    public function scopeFilterByDriver($id)
    {
        return $this->where('driver_id',$id);
    }

    public function scopeFilterByVehicle($id)
    {
        return $this->where('vehicle_id',$id);
    }
}
