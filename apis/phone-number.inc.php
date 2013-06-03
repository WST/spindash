<?php

/**
* ATS — A web development framework
* © 2007–2013 Ilya I. Averkov <admin@jsmart.web.id>
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

namespace SpinDash;

final class PhoneNumber extends CoreModule
{
	private $country_code;
	private $operator_code;
	private $number;
	
	const RE_PHONE = '#^\+(1|2[0-9]{2}|3[0-9]|4[0-9]|5[0-9]|6[0-9]{2}|7|8[0-9]{2}|9[0-9]{2})([0-9]{3})([0-9]{4,})$#';
	const RE_PHONE_PART_COUNTRY_CODE = 1;
	const RE_PHONE_PART_OPERATOR_CODE = 2;
	const RE_PHONE_PART_NUMBER = 3;
	
	const PHONE_NUMBER_INVALID = 'Invalid phone number';
	
	public function __construct(API $base, $number) {
		parent::__construct($base);
		
		$parts = self::validate($number);
		$this->country_code = $parts[self::RE_PHONE_PART_COUNTRY_CODE];
		$this->operator_code = $parts[self::RE_PHONE_PART_OPERATOR_CODE];
		$this->number = $parts[self::RE_PHONE_PART_NUMBER];
	}
	
	public static function fromString($number) {
		return new self($number);
	}
	
	public static function validate($number) {
		$m = array();
		if(!preg_match(self::RE_PHONE, $number, $m)) {
			throw new PhoneNumberException(self::PHONE_NUMBER_INVALID);
		}
		return $m;
	}
	
	public function getCountry() {
		
	}
	
	public function __toString() {
		return "+{$this->country_code}{$this->operator_code}{$this->number}";
	}
	
	public function format() {
		return "+{$this->country_code} ({$this->operator_code}) {$this->number}";
	}
}

