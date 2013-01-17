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

abstract class Model
{
	private $data = array();
	
	public function __construct() {
		switch(func_num_args()) {
			case 0:
				$this->loadDefaultData();
			break;
			case 1:
				$args = func_get_args();
				$this->data = $args[0];
			break;
		}
	}
	
	public function __destruct() {
		
	}
	
	private function loadDefaultData() {
		foreach(self::getFields() as $k => $v) {
			$this->data[$k] = isset($v['default']) ? $v['default'] : NULL;
		}
	}
	
	public static function fromRequest(Request & $request) {
		// Создать объект модели на основе поступившего HTTP-запроса (удобно в обработчиках форм)
	}
	
	public static function parseEntityName($name) {
		return ucfirst(str_replace('_', ' ', $name));
	}
	
	protected static function getFields() {
		return get_class_vars(get_called_class());
	}
	
	public function & value($field) {
		return $this->data[$field];
	}
	
	public static function createTable(Database & $db) {
		$sql = 'CREATE TABLE ' . self::pluralForm(get_called_class()) . ' (';
		foreach(self::getFields() as $k => $v) {
			echo "$k -> $v\n";
		}
		$sql .= ')';
		return $db->execute($sql);
	}
	
	public function save() {
		
	}
}

