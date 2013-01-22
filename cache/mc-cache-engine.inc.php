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

final class MCCacheEngine extends CacheEngine
{
	private $memcached = NULL;
	
	public function __construct(API $base, $key_prefix, $hostname, $port) {
		parent::__construct($key_prefix, $base);
		if(!class_exists('Memcached')) {
			throw new CoreException('Memcached PHP extension was not found');
		}
		$this->memcached = new \Memcached();
		$this->memcached->addServer($hostname, $port);
	}
	
	public function puts($key, $value, $lifetime = 0) {
		return $this->memcached->set($key, $value, $lifetime);
	}
	
	public function ada($key) {
		return (bool) $this->memcached->get($key);
	}
	
	public function gets($key, $default_value = NULL) {
		if(!$value = $this->memcached->get($key)) {
			return $default_value;
		} else {
			return $value;
		}
	}
}
