<?php

namespace App\Model\Interfaces;

interface UserInterface
{
	public function retrieveAll($page, $size);

	public function create(array $user);
}