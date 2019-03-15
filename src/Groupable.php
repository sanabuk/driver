<?php

namespace sanabuk\driver;

use sanabuk\driver\models\Group;

Trait Groupable
{
	public function groups()
	{
		return $this->morphToMany(Group::class, 'groupment');
	}

	/**
	 * Get Groups By Type where Model is included
	 * @param QueryBuilder $query
	 * @param string $type  
	 * @return QueryBuilder;
	 */
	public function scopeByType($query, $type)
	{
		return $this->groups->where('type', $type);
	}
}