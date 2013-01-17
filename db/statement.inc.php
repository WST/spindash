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

final class Statement
{
	private $pdo_statement = NULL;
	private $executed = false;
	private $database = NULL;
	private $closed = false;
	
	public function __construct(Database $database, \PDOStatement $pdo_statement) {
		$this->database = $database;
		$this->pdo_statement = $pdo_statement;
	}
	
	public function bindParam($name, & $value) {
		return $this->pdo_statement->bindParam($name, $value);
	}
	
	public function execute() {
		try {
			$this->pdo_statement->execute();
		} catch(\PDOException $e) {
			throw new DatabaseException($e);
		}
		$this->executed = true;
		$this->closed = false;
		$this->database->incCounter();
	}
	
	public function fetch($fetch_style = \PDO::FETCH_ASSOC) {
		if(!$this->executed) {
			$this->execute();
		}
		return $this->pdo_statement->fetch($fetch_style);
	}
	
	public function fetchAll() {
		return $this->pdo_statement->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	// Better to use fetchAll
	public function rows($fetch_style = \PDO::FETCH_ASSOC) {
		$retval = array();
		while($retval[] = $this->pdo_statement->fetch($fetch_style));
		return $retval;
	}
	
	public function error() {
		$error = $this->pdo_statement->errorInfo();
		return $error[2];
	}
	
	public function close() {
		if(!$this->closed) @ $this->pdo_statement->closeCursor();
		$this->closed = true;
	}
	
	public function __destruct() {
		$this->close();
		unset($this->pdo_statement);
	}
	
	public function numRows() {
		// NOTE: If the last SQL statement executed by the associated PDOStatement was a SELECT statement,
		// some databases may return the number of rows returned by that statement.
		// However, this behaviour is not guaranteed for all databases and should not be relied on for portable applications.
		return $this->pdo_statement->rowCount();
	}
}

