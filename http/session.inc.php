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

final class Session extends CoreModule
{
	const SESSION_CORRUPTED_VARIABLE = 'The requested session variable is corrupted';
	const SESSION_INVALID_AUTH_HANDLER = 'Invalid authentication callback given';
	
	private $request = NULL;
	
	private $login_callback;
	private $logged_in;
	private $username;
	
	public function __construct(API $base, Request $request) {
		parent::__construct($base);
		
		// TODO: FastCGI, heavy refactoring
		session_start();
		
		$this->request = $request;
		
		if(!isset($_SESSION['IP'])) {
			$_SESSION['IP'] = (string) $request->ip(false);
		} else {
			if($_SESSION['IP'] != (string) $request->ip(false)) {
				session_unset();
			}
		}
		
		$this->logged_in = false;
		$this->login_callback = array($this, 'loginDefault');
		
		if(@ $_SESSION['ATS_LOGGED_IN'] == 'yes') {
			$this->logged_in = true;
			$this->username = @ $_SESSION['ATS_USERNAME'];
		}
	}
	
	public function __destruct() {
		
	}
	
	public function username() {
		return $this->username;
	}
	
	public function isActive() {
		return $this->logged_in;
	}
	
	private function loginDefault($username, $password) {
		return true;
	}
	
	public function puts($variable, $value) {
		$_SESSION[$variable] = serialize($value);
	}
	
	private function isSerialized($val) {
		return is_string($val) && (trim($val) != '') &&  preg_match('/^(i|s|a|o|d|b):(.*);?/si', $val);
	}
	
	public function gets($variable, $default_value = NULL) {
		if(!isset($_SESSION[$variable])) {
			return $default_value;
		}
		if(!self::isSerialized($_SESSION[$variable])) {
			//die($_SESSION[$variable]);
			throw new SessionException(self::SESSION_CORRUPTED_VARIABLE);
		}
		return unserialize($_SESSION[$variable]);
	}
	
	public function delete($variable) {
		unset($_SESSION[$variable]);
	}
	
	public function setAuthHandler($callback) {
		if(!is_callable($callback)) {
			throw new SessionException(self::SESSION_INVALID_AUTH_HANDLER);
		}
		$this->login_callback = $callback;
	}
	
	public function login($username, $password) {
		if(is_callable($this->login_callback)) {
			if(($this->logged_in = call_user_func($this->login_callback, $username, $password)) === true) {
				$this->username = $username;
				$_SESSION['ATS_LOGGED_IN'] = 'yes';
				$_SESSION['ATS_USERNAME'] = $username;
				return true;
			}
			return false;
		} else {
			throw new SessionException(self::SESSION_INVALID_AUTH_HANDLER);
		}
	}
	
	public function logout() {
		$this->logged_in = false;
		$this->username = NULL;
		return session_destroy();
	}
	
	public function randomString($length = 32, $use_letters = true) {
		$retval = '';
		for($i = 0; $i < $length; $i ++) {
			if($use_letters) {
				switch(mt_rand(0,2)) {
					case 0: $retval .= chr(mt_rand(0x30, 0x39)); break;
					case 1: $retval .= chr(mt_rand(0x41, 0x5A)); break;
					case 2: $retval .= chr(mt_rand(0x61, 0x7A)); break;
				}
			} else {
				$retval .= chr(mt_rand(0x30, 0x39));
			}
		}
		return $retval;
	}

	
	public function registerForm($fid) {
		$forms = $this->gets('FORMS', array());
		if(!is_array($forms)) {
			$forms = array($fid => ATS_NOW);
		} else {
			$forms[$fid] = ATS_NOW;
		}
		$this->puts('FORMS', $forms);
	}
	
	public function registerCaptcha($key, $code) {
		$captchas = $this->gets('CAPTCHAS', array());
		if(!is_array($captchas)) {
			$captchas = array($key => $code);
		} else {
			$captchas[$key] = $code;
		}
		$this->puts('CAPTCHAS', $captchas);
	}
	
	public function newCaptcha() {
		$this->registerCaptcha($key = $this->randomString(32), $code = $this->randomString(6, false));
		return array($key, $code);
	}
	
	public function newForm() {
		$this->registerForm($key = $this->randomString(32));
		return $key;
	}
	
	public function validateCaptcha() {
		$captchas = $this->gets('CAPTCHAS', array());
		if(!is_array($captchas)) {
			return false;
		} else {
			foreach($captchas as $key => $code) {
				if(@ $this->request->post($key) == $code) {
					unset($captchas[$key]);
					$this->puts('CAPTCHAS', $captchas);
					return true;
				} else {
					return false;
				}
			}
		}
	}
	
	public function validateForm($fid) {
		$forms = $this->gets('FORMS', array());
		if(!is_array($forms)) {
			return false;
		} else {
			if(isset($forms[@ $fid])) {
				unset($forms[@ $fid]);
				$this->puts('FORMS', $forms);
				return true;
			} else {
				return false;
			}
		}
	}
}
