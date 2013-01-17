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

final class ModelManager
{
	private $table_name;
	private $model_name;
	private $field_prefix;
	private $db = NULL;
	
	public function tableNameFromEntity($model_name) {
		return "{$this->field_prefix}s";
	}
	
	public function __construct(Database &$db, $model_name) {
		$this->db = & $db;
		$this->model_name = $model_name;
		$this->field_prefix = strtolower($model_name);
		$this->table_name = property_exists($model_name, 'table_name') ? $model_name::$table_name : $this->tableNameFromEntity($model_name);
	}

	public function objects() {
		$retval = array();
		
		$select = $this->db->select($this->table_name, '*');
		$select->execute();
		while($row = $select->fetch()) {
			// TODO (new $this->model_name …)
		}
		return $retval;
	}
	
	public function records($record_filter = NULL) {
		$retval = array();
		
		$select = $this->db->select($this->table_name, '*');
		$select->execute();
		
		for($i = 0; $row = $select->fetch(); $i ++) {
			foreach($row as $k => $v) {
				$retval[$i][str_replace("{$this->field_prefix}_", '', $k)] = $v;
			}
		}
		
		return $retval;
	}
}

