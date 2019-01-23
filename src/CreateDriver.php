<?php

namespace sanabuk/driver;

/**
 * Create Driver
 */
class CreateDriver extends Driver
{
	
	function __construct(argument)
	{
		# code...
	}

	public function handler(array $array)
	{
		return Driver::create($array);
	}
}