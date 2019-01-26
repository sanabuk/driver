<?php

namespace sanabuk\driver\models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
	protected $fillable = ['name','user_id'];

	protected $primaryKey = 'id';
	protected $table = 'drivers';

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function vehicle()
    {
    	return $this->hasOne(Vehicle::class);
    }

    public function historic()
    {
        return $this->hasMany(HistoryDriverVehicle::class)->orderBy('updated_at');
    } 
}
