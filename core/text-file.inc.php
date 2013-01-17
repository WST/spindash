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

class TextFile
{
	private $data;
	private $filename;
	
	public function __construct($filename) {
		// TODO: ability not to store entire file in the memory
		if(! @ file_exists($filename)) {
			throw new FileIOException($filename, FILE_IO_FILE_NOT_FOUND);
		}
		if(! @ is_readable($filename)) {
			throw new FileIOException($filename, FILE_IO_ACCESS_DENIED);
		}
		$this->data = file_get_contents($filename);
		$this->filename = $filename;
	}
	
	public function __destruct() {
		
	}
	
	public function save() {
		file_put_contents($this->filename, $this->data);
	}
	
	public function setData($text) {
		$this->data = $text;
	}
	
	public function replace($from, $to) {
		$this->data = str_replace($from, $to, $this->data);
	}
	
	public function __toString() {
		return $this->data;
	}
}

