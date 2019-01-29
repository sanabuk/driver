<?php

namespace sanabuk\driver\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Groupment Model
 * 
 * 'Groupment' (grouping) represents the association between a group and the various elements that compose it
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