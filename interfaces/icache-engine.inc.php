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

interface ICacheEngine
{
	public function puts($key, $value, $lifetime = 0);
	
	public function ada($key);
	
	public function gets($key, $default = NULL);
	
	public function handleRequest(Request $request, Response $response);
	public function handleResponse(Request $request, Response $response);
}
