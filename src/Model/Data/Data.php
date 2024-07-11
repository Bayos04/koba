<?php

namespace App\Model\Data;

use App\Config\Data\DataBase;

class Data
{
	protected static \PDO $con;

	public function __construct()
	{
		self::$con = DataBase::connect();
	}

}