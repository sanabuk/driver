<?php

namespace sanabuk\driver\models;

use Illuminate\Database\Eloquent\Model;

/**
 * HistoryDriverVehicle Model
 * 
 * Review the history of associations between drivers and vehicles
 */

class HistoryDriverVehicle extends Model
{
    protected $table = 'vehicle_driver_history';

    /**
     * Get relation with driver
     * @return BelongsToRelation
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get relation with vehicle
     * @return BelongsToRelation
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Scope filterByDriverId
     * @param integer $id
     * @return QueryBuilder
     */
    public function scopeFilterByDriver($id)
    {
        return $this->where('driver_id', $id);
    }

    /**
     * Scope filterByVehicleId
     * @param integer $id
     * @return QueryBuilder
     */
    public function scopeFilterByVehicle($id)
    {
        return $this->where('vehicle_id', $id);
    }

    /**
     * Scope filterByDate
     * @param Carbon\Carbon $from
     * @param Carbon\Carbon|Null ?$to
     * @return QueryBuilder
     */
    public function scopeFilterByDate($from, $to = null)
    {
        $to = is_null($to) ? Carbon\Carbon::now() : $to;
        return $this->where('created_at', '>=', $from)->where('created_at', '<=', $to);
    }

}
