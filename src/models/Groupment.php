<?php

namespace sanabuk\driver\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Groupment Model
 */
class Groupment extends Model
{
	protected $table = 'groupments';

	protected $fillable = ['group_id', 'groupment_id', 'groupment_type'];

	public function groupment()
	{
		return $this->morphTo();
	}
}