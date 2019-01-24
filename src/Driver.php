<?php

namespace sanabuk\driver;

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
}
