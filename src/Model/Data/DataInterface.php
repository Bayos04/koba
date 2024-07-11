<?php

namespace App\Model\Data;

interface DataInterface
{
	public function save(array $object):void;

	public function findAll(string $view = "*", int $size = 10, int $page = 0):array;

	public function findById(int $id, string $view = "*"):?array;

	public function findBy(string $field, int | string $value, string $view = "*"):?array;

	public function findAllBy(string $field, int | string $value, string $view = "*", int $size = 10, int $page = 0):array;

	public function count():int;

	public function countBy($field, $value):int;

	public function update(array $object):void;

	public function updateBy(string $identifier, string $field, int|string $value):void;
}