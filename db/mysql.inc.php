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

final class MySQL extends Database
{
	public function __construct(API $base, $hostname, $username, $password, $database, $use_unix_socket = false) {
		parent::__construct($base);
		
		try {
			$this->pdo = new \PDO("mysql:dbname={$database};" . (@ $use_unix_socket ? "unix_socket={$hostname}" : "host={$hostname}"), $username, $password/*, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'")*/);
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this->pdo->exec("SET NAMES 'UTF8'");
		} catch(\PDOException $e) {
			throw new DatabaseException($e);
		}
	}
}
