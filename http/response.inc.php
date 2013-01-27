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

final class Response extends CoreModule
{
	private $body = '';
	private $headers = array();
	private $vary_headers = array();
	private $status = 200;
	private $content_type = 'text/html;charset=utf-8';
	
	private $ready = false;
	private $do_not_cache = false;
	
	public function __construct(API $base) {
		parent::__construct($base);
	}
	
	public function setBody($data) {
		if($data instanceof RSSFeed) {
			$this->setContentType('application/rss+xml');
		}
		
		$this->body = (string) $data;
	}
	
	public function setStatusCode($code) {
		$this->status = $code;
	}
	
	public function statusCode() {
		return $this->status;
	}
	
	public function setContentType($mime_type) {
		$this->content_type = $mime_type;
	}
	
	public function doNotCache($do_not_cache = true) {
		$this->do_not_cache = $do_not_cache;
	}
	
	public function cachingForbidden() {
		return $this->do_not_cache;
	}
	
	private function statusDescription() {
		switch($this->status) {
			case 200: return 'Found'; break;
			case 206: return 'Partial Content'; break;
			case 302: return 'Moved Permanently'; break;
			case 403: return 'Forbidden'; break;
			case 404: return 'Not Found'; break;
			case 410: return 'Gone'; break;
			case 502: return 'Bad Gateway'; break;
			case 503: return 'Service Temporarily Unavailable'; break;
			case 504: return 'Gateway Timeout'; break;
		}
		
		throw new CoreException('unknown HTTP status code');
	}
	
	public function redirect($location) {
		$this->status = 302;
		$this->setHeader('Location', $location);
	}
	
	public function setHeader($header_name, $value) {
		$this->headers[$header_name] = $value;
	}
	
	public function varyOn($header_name = NULL) {
		if(!is_null($header_name)) {
			$this->vary_headers[] = $header_name;
		}
		
		return $this->vary_headers;
	}
	
	public function sendFastCGI($client = 0) {
		socket_write($client, "HTTP/1.1 {$this->status} {$this->statusDescription()}\r\n");
		socket_write($client, "Content-Type: {$this->content_type}\r\n");
		
		foreach($this->headers as $header_name => $value) {
			socket_write($client, "{$header_name}: {$value}\r\n");
		}
		
		if(count($this->vary_headers)) {
			socket_write($client, 'Vary: ' . implode(', ', $this->vary_headers) . "\r\n");
		}
		
		socket_write($client, "\r\n");
		socket_write($client, $this->body);
	}
	
	public function sendBasic() {
		header("HTTP/1.1 {$this->status} {$this->statusDescription()}");
		header("Content-Type: {$this->content_type}");
		
		foreach($this->headers as $header_name => $value) {
			header("{$header_name}: {$value}");
		}
		
		if(count($this->vary_headers)) {
			header('Vary: ' . implode(', ', $this->vary_headers));
		}
		
		echo $this->body;
	}
	
	public function __toString() {
		return $this->body;
	}
	
	public function setReady($ready) {
		$this->ready = $ready;
	}
	
	public function ready() {
		return $this->ready;
	}
}
