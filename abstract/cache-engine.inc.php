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

abstract class CacheEngine extends CoreModule implements ICacheEngine
{
	private $key_prefix = '';
	
	public function __construct($key_prefix, API $base) {
		parent::__construct($base);
		$this->key_prefix = $key_prefix;
	}
	
	public function handleRequest(Request $request, Response & $response) {
		if($request->method() != 'get') return false;
		
		$stored_response = $this->gets($key = "{$this->key_prefix}:{$request->host()}:{$request->path()}", NULL);
		if(!is_null($stored_response)) {
			$response = unserialize($stored_response);
		}
		
		return false;
	}
	
	public function handleResponse(Request $request, Response & $response) {
		if($request->method() != 'get') return false;
		
		$this->puts("{$this->key_prefix}:{$request->host()}:{$request->path()}", serialize($response));
		return true;
	}
}
