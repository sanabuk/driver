<?php

namespace sanabuck\driver;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
	protected $fillable = ['name'];

	protected $primaryKey = 'id';
	protected $table = 'drivers2';
	/*
    public function __construct()
    {
    	$this->name = 'bob';
    }*/
}
