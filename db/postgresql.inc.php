<?php

/**
* SpinDash — A web development framework
* © 2007–2013 Ilya I. Averkov <admin@jsmart.web.id>
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

namespace SpinDash;

final class PostgreSQL extends Database
{
	public function __construct(API $base, $hostname, $username, $password, $database) {
		parent::__construct($base);
		
		try {
			$this->pdo = new \PDO("pgsql:dbname={$database};host={$hostname}", $username, $password);
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch(\PDOException $e) {
			throw new DatabaseException($e);
		}
	}
}
