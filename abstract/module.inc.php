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
	protected $application = NULL;
	
	public function __construct(Application $application) {
		$this->application = $application;
	}
	
	public static function configure(Application $application, $persistent = false) {
		$class_name = get_called_class();
		
		if($persistent) {
			$instance = new $class_name($application);
			$application->registerModule($instance);
			$methods = call_user_func([$class_name, 'routeMap']);
			foreach($methods as $method => $routes) {
				foreach($routes as $route => $handler) {
					call_user_func([$application, $method], $route, $instance, $handler);
				}
			}
		} else {
			$methods = call_user_func([$class_name, 'routeMap']);
			foreach($methods as $method => $routes) {
				foreach($routes as $route => $handler) {
					call_user_func([$application, $method], $route, get_called_class(), $handler, $application);
				}
			}
		}
	}
}
