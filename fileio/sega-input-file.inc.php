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

class SegaInputFile extends CoreModule
{
	private $filename;
	private $header;
	
	public function __construct(API $base, $filename) {
		parent::__construct($base);
		$this->filename = $filename;
		
		if(! @ file_exists($filename)) throw new FileIOException($filename, FileIOException::FILE_IO_FILE_NOT_FOUND);
		if(! @ is_readable($filename)) throw new FileIOException($filename, FileIOException::FILE_IO_ACCESS_DENIED);
		
		$handle = @ fopen($filename, 'r');
		$this->header = @ fgets($handle, 64);
		@ fclose($handle);
	}
	
	public function playerNumber() {
		
	}
	
	public function rerecords() {
		
	}
	
	public function framesPerSecond() {
		
	}
	
	public function requiresSavestate() {
		
	}
	
	public function comment() {
		$comment_raw = substr($this->header, 0x18, 40);
		return substr($comment_raw, 0, strpos($comment_raw, "\0"));
	}
}
