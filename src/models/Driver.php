<?php

namespace sanabuk\driver\models;

use Illuminate\Database\Eloquent\Model;
use sanabuk\driver\scopes\UserScope;

class Driver extends Model
{
	protected $fillable = ['name','user_id'];
	protected $primaryKey = 'id';
	protected $table = 'drivers';

    protected static function boot()
    {
        parent::boot();
        //static::addGlobalScope(new UserScope);
    }

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

    public function groups()
    {
        return $this->morphToMany(Group::class,'groupment');
    } 
}
