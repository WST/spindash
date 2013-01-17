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

final class BBCode extends CoreModule
{
	private $base = NULL;
	private $text = '';
	
	public function __construct(API $base) {
		parent::__construct($base);
	}
	
	private function parseTag() {
		
	}
	
	public function parse($text) {
		$this->text = $text;
		return $this->text;
	}
}

