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

final class SMSGateway extends CoreModule
{
	private $username = '';
	private $password = '';
	private $sender_id = '';

	public function __construct(API $base, $username, $password) {
		parent::__construct($base);
		$this->username = $username;
		$this->password = $password;
	}
	
	public function setSenderId($sender_id) {
		$this->sender_id = $sender_id;
	}

	public function send(PhoneNumber $number, $text) {
		$sender_id = $this->sender_id ? "&sender={$this->sender_id}" : '';
		$reply = @ file_get_contents("http://smsc.ru/sys/send.php?charset=utf-8&fmt=2&cost=2&login={$this->username}&psw={$this->password}{$sender_id}&phones=$number&mes=" . urlencode($text));
		$result = new \DOMDocument('1.0', 'utf-8');
		if(! @ $result->loadXML($reply)) {
			throw new SMSGatewayException(0);
		}
		
		$error_code = $result->getElementsByTagName('error_code');
		if($error_code->length != 0) {
			throw new SMSGatewayException((int) ($error_code->item(0)->textContent));
		}
	}
}

