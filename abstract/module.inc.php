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

abstract class Module implements IATSModule
{
	public static function configure(Application $application) {
		$methods = call_user_func(array(get_called_class(), 'routeMap'));
		foreach($methods as $method => $routes) {
			foreach($routes as $route => $handler) {
				call_user_func(array($application, $method), $route, get_called_class(), $handler, $application);
			}
		}
	}
}
