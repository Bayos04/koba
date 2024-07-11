<?php

namespace App\Config\Data;

use \PDO;
use \Exception;
use \PDOStatement;
use App\Config\Config;

class DataBase
{
	public static function connect(): PDO
	{
		try {
			$database = Config::DATABASE;

			$db = new PDO("mysql:host=localhost;dbname="
						  . $database['database'] . ";charset=utf8", $database['user'], $database['password']);
		} catch (Exception $e) {
			throwError($e->getCode(), $e->getMessage());
		}
		return $db;
	}

	public static function fetch(PDOStatement $statement): null|array
	{
		$response = $statement->fetch(PDO::FETCH_ASSOC);
		if (!$response)
			return null;
		return $response;
	}

	public static function fetchAll(PDOStatement $statement): array
	{
		$response =  $statement->fetchAll(PDO::FETCH_ASSOC);
		if (!$response)
			return [];
		return $response;
	}
}