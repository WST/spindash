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

class Directory extends CoreModule
{
	protected $path;
	
	public function __construct(API $base, $path) {
		parent::__construct($base);
		if(! @ file_exists($path)) {
			throw new FileIOException($path, FileIOException::FILE_IO_DIRECTORY_NOT_FOUND);
		}
		if(! @ is_dir($path)) {
			throw new FileIOException($path, FileIOException::FILE_IO_NOT_A_DIRECTORY);
		}
		$this->path = $path;
	}
	
	/**
	* Получить список файлов данного каталога.
	* @note Возвращает полный список файлов текущего каталога в виде нумерованного массива строк.
	* @return array список файлов
	*/
	
	public function listEntries() {
		if(!is_readable($this->path)) {
			throw new FileIOException("{$this->path} is not readable!");
		}
		
		$retval = [];
		for($d = @ opendir($this->path); $d_res = @ readdir($d); true) {
			if($d_res == '.' || $d_res == '..') continue;
			if($d_res[0] == '.') continue; // Конченое Subversion!
			$retval[] = $d_res;
		}
		
		return $retval;
	}
	
	public function listFiles() {
		$retval = [];
		foreach($this->listEntries() as $k=>$v) {
			if(!is_dir($this->path . DIRECTORY_SEPARATOR . $v)) {
				$retval[] = $v;
			}
		}
		
		return $retval;
	}
	
	public function listFilesByTypes() {
		$types = func_get_args();
		$retval = [];
		foreach($types as $k=>$v) {
			if(!preg_match('#^[a-z]+$#iU', $v)) {
				throw new FileIOException("Wrong filename extension: $v");
			}
		}
		
		foreach($this->listFiles() as $k => $v) {
			if(preg_match('#(' . implode('|', $types) . ')#iU', $v)) {
				$retval[] = $v;
			}
		}
		
		return $retval;
	}
	
	public function listDirectories() {
		$retval = [];
		foreach($this->listEntries() as $k => $v) {
			if(is_dir($this->path . DIRECTORY_SEPARATOR . $v)) {
				$retval[] = $v;
			}
		}
		return $retval;
	}
	
	public function listDirectoriesByPattern($pattern) {
		$retval = [];
		foreach($this->listDirectories() as $k => $v) {
			if(preg_match($pattern, $v)) {
				$retval[] = $v;
			}
		}
		return $retval;
	}
	
	public function assignUnusedFilename($type) {
		do {
			$name = atsRandomString(28);
		} while (@ file_exists($this->path . DIRECTORY_SEPARATOR . ($retval = $name . '.' . $type)));
		return $retval;
	}
	
	public function filePutContents($filename, $contents) {
		return file_put_contents($this->path . DIRECTORY_SEPARATOR . $filename, $contents);
	}
	
	public function deleteEntry($name) {
		/// TODO: recursive, realpath
		if($name == '.' || $name == '..') {
			throw new FileIOException("Wrong filename given: $name");
		}
		return @ unlink($this->path . DIRECTORY_SEPARATOR . $name);
	}
	
	public function path() {
		return $this->path;
	}
}

