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

final class Request extends CoreModule
{
	private $get_variables;
	private $post_variables;
	private $cookie_variables;
	private $method;
	
	private $session = NULL;
	
	public function __construct(API $base) {
		parent::__construct($base);
		switch($base->frontend()) {
			case API::ATS_FRONTEND_BASIC:
				$this->get_variables = & $_GET;
				$this->post_variables = & $_POST;
				$this->cookie_variables = & $_COOKIE;
				$this->method = strtolower($_SERVER['REQUEST_METHOD']);
			break;
			case API::ATS_FRONTEND_FASTCGI:
				// TODO
			break;
		}
	}
	
	public function session() {
		if(!isset($this->session)) {
			$this->session = new Session($this->base, $this);
		}
		return $this->session;
	}
	
	private function fetchVariable($variable, $from) {
		return is_null($variable) ? $from : (array_key_exists($variable, $from) ? $from[$variable] : NULL);
	}
	
	public function requestPath() {
		return $_SERVER['REQUEST_URI'];
		// TODO: FastCGI
	}
	
	public function path() {
		return $_SERVER['REQUEST_URI'];
	}
	
	public function get($variable = NULL) {
		return $this->fetchVariable($variable, $this->get_variables);
	}
	
	public function post($variable = NULL) {
		return $this->fetchVariable($variable, $this->post_variables);
	}
	
	public function cookie($variable = NULL) {
		return $this->fetchVariable($variable, $this->cookie_variables);
	}
	
	public function gpc($variable = NULL) {
		if(is_null($variable)) return array('GET' => $this->get_variables, 'POST' => $this->post_variables, 'COOKIE' => $this->cookie_variables);
		return is_null($result = $this->get($variable)) ? (is_null($result = $this->post($variable)) ? (is_null($result = $this->cookie($variable)) ? NULL : $result) : $result) : $result;
	}
	
	public function __toString() {
		return serialize($this->gpc() + array('METHOD' => $this->method()));
	}
	
	public function restore($from) {
		$gpc = @ unserialize($from);
		$this->get_variables = isset($gpc['GET']) ? $gpc['GET'] : array();
		$this->post_variables = isset($gpc['POST']) ? $gpc['POST'] : array();
		$this->cookie_variables = isset($gpc['COOKIE']) ? $gpc['COOKIE'] : array();
		$this->method = isset($gpc['METHOD']) ? $gpc['METHOD'] : 'get';
	}
	
	public function setPost($key, $value) {
		$this->post_variables[$key] = $value;
	}
	
	public function file($name) {
		return @ $_FILES[$name];
		// TODO: FastCGI
	}
	
	public function hasFile($name) {
		return (@ $_FILES[$name]['error'] == 0);
	}
	
	public function host() {
		return $_SERVER['HTTP_HOST'];
		// TODO: FastCGI
	}
	
	public function ip($resolve_proxy = true) {
		return $resolve_proxy ? (IPAddress::validate(@ $_SERVER['HTTP_X_FORWARDED_FOR']) ? new IPAddress($_SERVER['HTTP_X_FORWARDED_FOR']) : new IPAddress($_SERVER['REMOTE_ADDR'])) : new IPAddress($_SERVER['REMOTE_ADDR']);
		// TODO: FastCGI
	}
	
	public function method() {
		return $this->method;
	}
}

