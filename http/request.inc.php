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
	private $path;
	private $host;
	private $fastcgi_headers;
	
	private $session = NULL;
	
	public function __construct(API $base) {
		parent::__construct($base);
		switch($base->frontend()) {
			case API::FRONTEND_BASIC:
				$this->get_variables = & $_GET;
				$this->post_variables = & $_POST;
				$this->cookie_variables = & $_COOKIE;
				$this->method = strtolower($_SERVER['REQUEST_METHOD']);
				$this->path = $_SERVER['REQUEST_URI'];
				$this->host = $_SERVER['HTTP_HOST'];
			break;
			case API::FRONTEND_FASTCGI:
				// nothing
			break;
		}
	}
	
	private function handleFastCGICookie($raw_data) {
		
	}
	
	private function handleRequestString($raw_data) {
		if(($delimiter = strpos($raw_data, '?')) !== false) {
			$this->path = substr($raw_data, 0, $delimiter);
			$args = explode('&', substr($raw_data, $delimiter + 1));
			foreach($args as $raw_argument) {
				$raw_argument = urldecode($raw_argument);
				$parts = [];
				preg_match('#^([^=]+)=([^=]*)$#', $raw_argument, $parts);
				$this->get_variables[$parts[1]] = $parts[2];
			}
		} else {
			$this->path = $raw_data;
		}
	}
	
	public function parseFastCGIRequest($raw_data) {
		$raw_headers = substr($raw_data, 0, $delimiter_position = strpos($raw_data, "\r\n\r\n"));
		$raw_body = substr($raw_data, $delimiter_position + 4);
		
		$headers = explode("\r\n", $raw_headers);
		
		$parts = [];
		if(!preg_match('#^(GET|POST|PUT|HEAD) (.*) HTTP/1\.[01]$#', array_shift($headers), $parts)) {
			throw new CoreException('Broken HTTP request — chrome?');
		}
		
		$this->method = strtolower($parts[1]);
		$this->handleRequestString($parts[2]);
		
		foreach($headers as $raw_header) {
			$parts = [];
			preg_match('#([a-zA-Z\-]+): (.*)#', $raw_header, $parts);
			
			switch($parts[1]) {
				default:
					$this->fastcgi_headers[$parts[1]] = $parts[2];
				break;
				case 'Cookie':
					$this->handleFastCGICookie($parts[2]);
				break;
				case 'Host':
					$this->host = $parts[2];
				break;
			}
		}
		
		return true;
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
		return $this->path;
	}
	
	public function path() {
		return $this->path;
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
		if(is_null($variable)) return ['GET' => $this->get_variables, 'POST' => $this->post_variables, 'COOKIE' => $this->cookie_variables];
		return is_null($result = $this->get($variable)) ? (is_null($result = $this->post($variable)) ? (is_null($result = $this->cookie($variable)) ? NULL : $result) : $result) : $result;
	}
	
	public function __toString() {
		return serialize($this->gpc() + ['METHOD' => $this->method()]);
	}
	
	public function restore($from) {
		$gpc = @ unserialize($from);
		$this->get_variables = isset($gpc['GET']) ? $gpc['GET'] : [];
		$this->post_variables = isset($gpc['POST']) ? $gpc['POST'] : [];
		$this->cookie_variables = isset($gpc['COOKIE']) ? $gpc['COOKIE'] : [];
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
		return $this->host;
	}
	
	public function ip($resolve_proxy = true) {
		return $resolve_proxy ? (IPAddress::validate(@ $_SERVER['HTTP_X_FORWARDED_FOR']) ? new IPAddress($_SERVER['HTTP_X_FORWARDED_FOR']) : new IPAddress($_SERVER['REMOTE_ADDR'])) : new IPAddress($_SERVER['REMOTE_ADDR']);
		// TODO: FastCGI
	}
	
	public function headerValue($header_name, $default_value = NULL) {
		$header_name = strtoupper(str_replace('-', '_', $header_name));
		if(array_key_exists($key = "HTTP_$header_name", $_SERVER)) {
			return $_SERVER[$key];
		}
		return $default_value;
	}
	
	public function method() {
		return $this->method;
	}
}
