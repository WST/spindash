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

final class DatabaseCacheEngine extends CacheEngine
{
	private $database = NULL;
	
	public function __construct(API $base, Database $database, $key_prefix, $hostname, $port) {
		parent::__construct($key_prefix, $base);
		$this->database = $database;
		
		if($database instanceof MySQL) {
			
		}
		
		if($database instanceof SQLite) {
			
		}
	}
	
	public function puts($key, $value) {
		// TODO
	}
	
	public function ada($key) {
		// TODO
	}
	
	public function gets($key, $default_value = NULL) {
		// TODO
	}
}
