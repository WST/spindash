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

abstract class Database extends CoreModule
{
	protected $pdo = NULL;
	protected $queries = 0;
	
	
	public function __construct(API $base) {
		parent::__construct($base);
		if(!class_exists('PDO')) {
			throw new CoreException('PDO is not installed, but it is required');
		}
	}
	
	public function exec($query) {
		try {
			$this->pdo->exec($query);
			$this->queries ++;
		} catch(\PDOException $e) {
			throw new DatabaseException($e, $query);
		}
	}
	
	public function prepare($query) {
		try {
			return new Statement($this, $this->pdo->prepare($query), $query);
		} catch(\PDOException $e) {
			throw new DatabaseException($e, $query);
		}
	}
	
	public function incCounter() {
		$this->queries ++;
	}
	
	public function countRows($table, $what = '*', $where = '') {
		if($where = trim($where)) {
			$where = " WHERE $where";
		}
		$sql = "SELECT count($what) AS cnt FROM $table $where";
		$statement = $this->pdo->prepare($sql);
		$statement->execute();
		$this->queries ++;
		$row = $statement->fetch();
		$statement->closeCursor();
		return $row['cnt'];
	}
	
	public function select($from, $fields = '*', $where = '', $order = '', $limit = 0, $offset = 0) {
		if($where = trim($where)) {
			$where = " WHERE $where";
		}
		if($order = trim($order)) {
			$order = " ORDER BY $order";
		}
		if($limit > 0 || $offset > 0) {
			if($limit <= 0) {
				$limit = -1;
			}
			$limit = ' LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset);
		} else {
			$limit = '';
		}
		return $this->prepare("SELECT $fields FROM $from$where$order$limit");
	}
	
	public function selectOneRow($from, $fields = '*', $where = '', $order = '', $limit = 0, $offset = 0) {
		$statement = $this->select($from, $fields, $where, $order, $limit, $offset);
		$statement->execute();
		$row = $statement->fetch();
		$statement->close();
		
		return $row;
	}
	
	public function move($table, $id_column_name, $id_column_value, $position_column_name, $direction, $where_appendix = NULL) {
		
		$this->begin();
		
		$row1 = $this->selectOneRow($table, $position_column_name, "$id_column_name = $id_column_value" . (is_null($where_appendix) ? '' : " AND $where_appendix"));
		$row2 = $this->selectOneRow($table, "$id_column_name, $position_column_name", "$position_column_name " . ($direction == 'up' ? '< ' : '> ') . $row1[$position_column_name] . (is_null($where_appendix) ? '' : " AND $where_appendix"), "$position_column_name " . ($direction == 'up' ? 'DESC' : 'ASC'), 1, 0);
		
		if(!$row2) {
			$this->rollback();
			return false;
		}
		
		$this->exec("UPDATE $table SET $position_column_name = " . $row2[$position_column_name] . " WHERE $id_column_name = $id_column_value");
		$this->exec("UPDATE $table SET $position_column_name = " . $row1[$position_column_name] . " WHERE $id_column_name = " . $row2[$id_column_name]);
		
		$this->commit();
		
		return true;
	}
	
	public function moveUp($table, $id_column_name, $id_column_value, $position_column_name, $where_appendix = NULL) {
		return $this->move($table, $id_column_name, $id_column_value, $position_column_name, 'up', $where_appendix);
	}
	
	public function moveDown($table, $id_column_name, $id_column_value, $position_column_name, $where_appendix = NULL) {
		return $this->move($table, $id_column_name, $id_column_value, $position_column_name, 'down', $where_appendix);
	}
	
	public function begin() {
		$this->queries ++;
		return $this->pdo->beginTransaction();
	}
	
	public function commit() {
		$this->queries ++;
		return $this->pdo->commit();
	}
	
	public function rollback() {
		$this->queries ++;
		return $this->pdo->rollback();
	}
	
	public function escape($arg) {
		return $this->pdo->quote($arg);
	}
	
	public function queries() {
		return $this->queries;
	}
	
	public function lastInsertId() {
		return $this->pdo->lastInsertId();
	}
	
	public function __destruct() {
		unset($this->pdo);
	}
}
