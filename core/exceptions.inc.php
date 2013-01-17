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

class CoreException extends \Exception
{
	
}

final class FileIOException extends CoreException
{
	const FILE_IO_FILE_NOT_FOUND = 1;
	const FILE_IO_ACCESS_DENIED = 2;
	const FILE_IO_NOT_A_DIRECTORY = 3;
	const FILE_IO_DIRECTORY_NOT_FOUND = 4;
	
	private function getErrorMessage($error) {
		switch($error) {
			case self::FILE_IO_FILE_NOT_FOUND: return 'file not found'; break;
			case self::FILE_IO_ACCESS_DENIED: return 'access denied'; break;
			case self::FILE_IO_NOT_A_DIRECTORY: return 'not a directory'; break;
			case self::FILE_IO_DIRECTORY_NOT_FOUND: return 'directory does not exist'; break;
		}
	}
	
	private function formatErrorMessage($filename, $error) {
		return 'file IO error (' . $this->getErrorMessage($error) . ") when accessing $filename";
	}
	
	public function __construct($filename, $error) {
		parent::__construct($this->formatErrorMessage($filename, $error));
	}
}

final class TemplateException extends CoreException
{
	private $error = NULL;
	
	public function __construct(& $error) {
		$this->error = & $error;
		parent::__construct($error->getMessage());
	}
}

final class PhoneNumberException extends CoreException
{
	const PHONE_NUMBER_INVALID = 1;
}

final class SessionException extends CoreException
{
}

final class DatabaseException extends CoreException
{
	private $pdo_exception = NULL;
	
	public function __construct(\PDOException & $pdo_exception) {
		$this->pdo_exception = & $pdo_exception;
		parent::__construct($pdo_exception->getMessage());
	}
}

final class SMSGatewayException extends CoreException
{
	private function formatErrorMessage($error) {
		$messages = array();
		$messages[1] = 'Invalid API call';
		$messages[2] = 'Authentication failed';
		$messages[3] = 'Not enough money';
		$messages[4] = '';
		$messages[5] = '';
		$messages[6] = '';
		$messages[7] = 'Invalid phone number';
		$messages[8] = '';
		$messages[9] = 'Repeating messages detected';
		
		return "SMS gateway error: {$messages[$error]}";
	}
	
	public function __construct($error) {
		parent::__construct($this->formatErrorMessage($error));
	}
}
