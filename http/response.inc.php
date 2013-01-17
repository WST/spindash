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

final class Response
{
	private $body = '';
	private $headers = array();
	private $status = 200;
	private $content_type = 'text/html;charset=utf-8';
	
	private $ready = false;
	private $frontend = API::ATS_FRONTEND_BASIC;
	
	public function __construct($frontend) {
		$this->frontend = $frontend;
	}
	
	public function __sleep() {
		return array('body', 'headers', 'status', 'content_type', 'ready', 'frontend');
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
	
	public function setContentType($mime_type) {
		$this->content_type = $mime_type;
	}
	
	private function statusDescription() {
		switch($this->status) {
			case 200: return 'Found'; break;
			case 403: return 'Forbidden'; break;
			case 404: return 'Not Found'; break;
			case 206: return 'Partial Content'; break;
			case 302: return 'Moved Permanently'; break;
		}
		throw new CoreException('unknown HTTP status code');
	}
	
	public function redirect($location) {
		$this->status = 302;
		$this->headers['Location'] = $location;
	}
	
	public function send() {
		switch($this->frontend) {
			case API::ATS_FRONTEND_BASIC:
				header("HTTP/1.1 {$this->status} {$this->statusDescription()}");
				header("Content-Type: {$this->content_type}");
				
				foreach($this->headers as $k => $v) {
					header("$k: $v");
				}
				
				echo $this->body;
			break;
			case API::ATS_FRONTEND_FASTCGI:
				// TODO
			break;
		}
	}
	
	public function setReady($ready) {
		$this->ready = $ready;
	}
	
	public function ready() {
		return $this->ready;
	}
}

