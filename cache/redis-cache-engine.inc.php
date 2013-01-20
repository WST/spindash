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

final class RedisCacheEngine extends CacheEngine
{
	private $redis = NULL;
	
	public function __construct(API $base, $key_prefix, $hostname, $port) {
		parent::__construct($key_prefix, $base);
		if(!class_exists('Redis')) {
			throw new CoreException('Redis PHP extension (phpredis) was not found');
		}
		try {
			$this->redis = new \Redis();
			$this->redis->connect($hostname, $port);
			$this->redis->select(0);
		} catch(\RedisException $e) {
			throw new CoreException("Redis failed: {$e->getMessage()}");
		}
	}
	
	public function puts($key, $value) {
		$this->redis->set($key, $value);
	}
	
	public function ada($key) {
		return (bool) $this->redis->get($key);
	}
	
	public function gets($key, $default_value = NULL) {
		if(!$value = $this->redis->get($key)) {
			return $default_value;
		} else {
			return $value;
		}
	}
}
