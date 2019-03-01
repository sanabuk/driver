<?php

namespace sanabuk\driver\models;

use Illuminate\Database\Eloquent\Model;
use sanabuk\driver\scopes\UserScope;
use sanabuk\driver\Groupable;

/**
 * Driver Model
 * 
 * GlobalScope : Only a driver's user can review it
 * 
 * Relations:
 * - Driver can only be associated with one vehicle at a time
 * - Driver can be associated with several groups (PolymorphicRelation) at a time
 */
class Driver extends Model
{
    use Groupable;

	protected $fillable = ['name','user_id'];
	protected $primaryKey = 'id';
    protected $_foreignKeys = [
        'vehicle' => 'driver_id',
        'historic' => 'driver_id'
    ];
	protected $table = 'drivers';

    protected $hidden = ['created_at','updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new UserScope);
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function vehicle()
    {
        //return $this->historic()->where('updated_at',null);
    	return $this->hasOne(Vehicle::class);
        //return $this->hasOne(HistoryDriverVehicle::class)->where('updated_at',null);
    }

    public function historic()
    {
        return $this->hasMany(HistoryDriverVehicle::class)->with('vehicle')->latest();
    }
}
