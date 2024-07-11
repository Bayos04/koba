<?php

namespace App\Controller;

use App\Attribute\Route;
use App\Model\Service\UserService;
use App\Model\Interfaces\UserInterface;

#[Route("/user")]
class UserController implements UserInterface
{
	private static UserService $userService;

	public function __construct()
	{
		self::$userService = new UserService();
	}

	#[Route("/retrieve-all","GET")]
	public function retrieveAll($page = 0, $size = 10)
	{
		return self::$userService->retrieveAll($page, $size);
	}

	#[Route("/create","POST", ["ADMIN"])]
	public function create(array $user)
	{
		self::$userService->create($user);
	}
}