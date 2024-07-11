<?php

namespace App\Config;

class Config
{
	const SERVICE_NAME = "APP_NAME";
	const SERVICE_HOST = "APP_WEBAPP_URL";
	const API_SERVICE = "APP_API_URL";

	const DATABASE = [
		'user' => "root",
		'password' => "",
		'database' => "facturer"
	];

	const MAIL = [
		"email" => "",
		"secret" => "",
		"name" => "",
	];

}