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
	
	public function __construct(API $base, Database $database, $key_prefix) {
		parent::__construct($key_prefix, $base);
		$this->database = $database;
		
		if($database instanceof MySQL) {
			$sql = 'CREATE TABLE IF NOT EXISTS spindash_cache (item_key VARCHAR(255) NOT NULL, item_expires INT(11) UNSIGNED NOT NULL, item_value LONGTEXT NOT NULL, UNIQUE KEY (item_key)) ENGINE = InnoDB DEFAULT CHARSET=UTF8';
			$database->exec($sql);
		}
		
		if($database instanceof SQLite) {
			$sql = 'CREATE TABLE IF NOT EXISTS spindash_cache (item_key TEXT, item_expires INTEGER, item_value TEXT, UNIQUE (item_key))';
			$database->exec($sql);
		}
		
		if($database instanceof PostgreSQL) {
			$sql = 'CREATE TABLE IF NOT EXISTS spindash_cache (item_key VARCHAR(255) NOT NULL, item_expires INTEGER NOT NULL, item_value TEXT NOT NULL, UNIQUE (item_key))';
			$database->exec($sql);
		}
	}
	
	public function puts($key, $value, $lifetime = 0) {
		$key = $this->database->escape($key);
		$value = $this->database->escape($value);
		if($this->database->countRows('spindash_cache', "item_key = $key")) {
			$this->database->exec("UPDATE spindash_cache SET item_value = $value, item_expires = " . (SPINDASH_NOW + $lifetime) . " WHERE item_key = $key");
		} else {
			$this->database->exec("INSERT INTO spindash_cache VALUES ($key, " . (SPINDASH_NOW + $lifetime) . ", $value)");
		}
	}
	
	public function ada($key) {
		return (bool) $this->gets($key, false);
	}
	
	public function gets($key, $default_value = NULL) {
		$key = $this->database->escape($key);
		$row = $this->database->selectOneRow('spindash_cache', '*', "item_key = $key AND item_expires > " . SPINDASH_NOW);
		return $row ? $row['item_value'] : $default_value;
	}
}
