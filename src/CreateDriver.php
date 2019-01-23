<?php
namespace sanabuk\driver;

use sanabuk\driver\Driver;

/**
 * Create Driver
 */
class CreateDriver extends Driver
{
	public function handler(array $array)
	{
		return Driver::create($array);
	}
}