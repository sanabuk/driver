<?php

namespace sanabuk\driver;

class AssociateDriverWithModel extends Driver
{
	protected $model;

	public function __construct(Model $model)
	{
		parent::construct();
		$this->model = model;
	}

}