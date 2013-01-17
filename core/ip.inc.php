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

class IPAddress
{
	private $addr;
	private $netmask;
	
	public static function validate($ip_string) {
		return filter_var($ip_string, FILTER_VALIDATE_IP);
	}
	
	public function __construct($ip_string) {
		$this->addr = $ip_string;
	}
	
	public function setNetmask($netmask) {
		// TODO: вычислять начало сети и broadcast-адрес
		// только короткие маски, нахуй хуйню!
	}
	
	public function __toString() {
		return $this->addr;
	}
	
	public function pack() {
		// TODO: IPv6
		return ip2long($this->addr);
	}
}

