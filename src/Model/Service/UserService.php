<?php

namespace App\Model\Service;

use App\Model\Data\UserData;
use App\Model\Interfaces\UserInterface;

class UserService implements UserInterface
{
	private static UserData $userData;

	public function __construct()
	{
		self::$userData = new UserData();
	}

	public function retrieveAll($page, $size) : array
	{
		return self::$userData->findAll(size : $size, page : $page);
	}

	public function create(array $user)
	{
		// TODO: Implement create() method.
	}
}