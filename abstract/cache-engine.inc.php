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
	
	protected function createKey($request, $response) {
		$key_prefix = @ $this->key_prefix ? "{$this->key_prefix}:" : '';
		$key = "{$key_prefix}{$request->host()}:{$request->path()}";
		foreach($response->varyOn() as $header) {
			$key .= ':' . md5($request->headerValue($header));
		}
		return $key;
	}
	
	public function handleRequest(Request $request, Response $response) {
		if($request->method() != 'get') return false;
		
		$cached = $this->gets($key = $this->createKey($request->host(), $request->path()), NULL);
		if(!is_null($cached)) {
			$response->setBody($cached);
			return true;
		}
		
		return false;
	}
	
	public function handleResponse(Request $request, Response $response) {
		if($request->method() != 'get') return false;
		if($response->status() != 200) return false;
		
		$key = $this->createKey($request, $response);
		if($response->cachingForbidden()) {
			if($this->ada($key)) {
				// TODO: DELETE
			}
			return false;
		}
		
		$this->puts($key, (string) $response);
		return true;
	}
}
