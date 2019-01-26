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
}
