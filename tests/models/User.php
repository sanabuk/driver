<?php

namespace sanabuk\driver\tests\models;

use Illuminate\Database\Eloquent\Model;

/**
 * User Test
 */
class User extends Model
{
	protected $permission;
	
	public function __construct()
	{
		parent::__construct();
		$this->permission = true;
	}
}