<?php
namespace sanabuk\driver\actions;

use sanabuk\driver\models\Driver;

/**
 * Create Driver
 */
class CreateDriver extends Driver
{
	public function handler(array $array)
	{
		return $this->create($array);
	}
}