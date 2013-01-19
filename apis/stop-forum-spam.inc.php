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

final class StopForumSpam extends CoreModule
{
	private $api_key = '';

	public function __construct(API $base, $api_key = NULL) {
		parent::__construct($base);
		$this->api_key = $api_key;
	}

	public function checkUsername($username) {
		$response = @ file_get_contents('http://www.stopforumspam.com/api?username=' . urlencode($username) . '&f=json');
		$info = json_decode($response);
		if($info->success) {
			return (bool) $info->username->appears;
		} else {
			throw new \Exception('SFS service failed');
		}
	}

	public function checkIpAddress($ip_address) {
		$response = @ file_get_contents('http://www.stopforumspam.com/api?ip=' . urlencode($ip_address) . '&f=json');
		$info = json_decode($response);
		if($info->success) {
			return (bool) $info->ip->appears;
		} else {
			throw new \Exception('SFS service failed');
		}
	}

	public function checkEMailAddress($email_address) {
		$response = @ file_get_contents('http://www.stopforumspam.com/api?email=' . urlencode($email_address) . '&f=json');
		$info = json_decode($response);
		if($info->success) {
			return (bool) $info->email->appears;
		} else {
			throw new \Exception('SFS service failed');
		}	
	}
}
