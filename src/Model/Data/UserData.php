<?php

namespace App\Model\Data;

use App\Config\Data\DataBase;
use function date;
use function getPagingOffset;

class UserData extends Data implements DataInterface
{

	public function save(array $object) : void
	{
		extract($object);
		$query = self::$con->prepare("INSERT INTO user(identifier, first_name, last_name, email, phone, serial_number, gender, role, status, password, profile, reset_token, activation_code, double_auth, tfa_code, last_update)
    									VALUE (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$query->execute(array($identifier, $first_name, $last_name, $email, $phone, $serial_number, $gender, $role, $status, $password, $profile, $reset_token, $activation_code, $double_auth, $tfa_code, $last_update));
	}

	public function findAll(string $view = "*", int $size = 10, int $page = 0) : array
	{
		$offset = getPagingOffset($page,$size);
		$result = self::$con->query("SELECT $view FROM user WHERE status != 'DELETED' ORDER BY id DESC LIMIT $offset,$size");
		$result->execute();
		return ["users" => DataBase::fetchAll($result),
				"size"=>$size,
				"page"=>$page,
				"total"=>$this->count()];
	}

	public function findById(int $id, string $view = "*") : ?array
	{
		$query = self::$con->prepare("SELECT $view FROM user WHERE id = ? AND status != 'DELETED'");
		$query->execute(array($id));
		return DataBase::fetch($query);
	}

	public function findBy(string $field, int|string $value, string $view = "*") : ?array
	{
		$query = self::$con->prepare("SELECT $view FROM user WHERE $field = ? AND status != 'DELETED'");
		$query->execute(array($value));
		return DataBase::fetch($query);
	}

	public function findAllBy(string $field, int|string $value, string $view = "*", int $size = 10, int $page = 0) : array
	{
		$offset = getPagingOffset($page,$size);
		$query = self::$con->prepare("SELECT $view FROM user WHERE $field = ? AND status != 'DELETED' ORDER BY id DESC LIMIT $offset,$size");
		$query->execute(array($value));
		return ["products" => DataBase::fetchAll($query),
				"size"=>$size,
				"page"=>$page,
				"total"=>$this->count()];
	}

	public function count() : int
	{
		$query = self::$con->query("SELECT COUNT(*) FROM user WHERE status != 'DELETED'");
		return DataBase::fetch($query)["COUNT(*)"];
	}

	public function countBy($field, $value) : int
	{
		$query = self::$con->prepare("SELECT COUNT(*) FROM user WHERE $field = ? AND status != 'DELETED'");
		$query->execute(array($value));
		return DataBase::fetch($query)["COUNT(*)"];
	}

	public function update(array $object) : void
	{
		extract($object);
		$query = self::$con->prepare("UPDATE user SET last_name = ?, first_name = ?, phone = ?, gender = ?, last_update = NOW() WHERE identifier = ?");
		$query->execute(array($last_name, $first_name, $phone, $gender, $identifier));
	}

	public function updateBy(string $identifier, string $field, int|string $value) : void
	{
		$query = self::$con->prepare("UPDATE user SET $field = ? WHERE identifier = ?");
		$query->execute(array($value, $identifier));
	}
}