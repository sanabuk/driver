<?php

namespace sanabuk\driver\models;

use Illuminate\Database\Eloquent\Model;
use sanabuk\driver\scopes\UserScope;

/**
 * Group Model
 * 
 * GlobalScope : Only a driver's user can review it (UserScope)
 * 
 * Available Relations:
 * - drivers
 * - vehicles
 */
class Group extends Model
{
    protected $table = 'groups';

    protected $fillable = ['name', 'user_id', 'type'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new UserScope);
    }

    public function drivers()
    {
    	$query = $this->morphedByMany(Driver::class,'groupment');
    	return $query;
    }

    public function vehicles()
    {
    	return $this->morphedByMany(Vehicle::class, 'groupment');
    }
}
