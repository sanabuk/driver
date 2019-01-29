<?php

namespace sanabuk\driver\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Group Model
 */
class Group extends Model
{
    protected $table = 'groups';

    protected $fillable = ['name', 'user_id', 'type'];

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
