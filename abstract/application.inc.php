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

abstract class Application implements IApplication
{
	protected $ats = NULL;
	
	public function __construct($debug) {
		$this->ats = new API($debug);
		$this->initializeCoreRoutes();
	}
	
	protected function initializeCoreRoutes() {
		$methods = call_user_func(array(get_called_class(), 'routeMap'));
		foreach($methods as $method => $routes) {
			foreach($routes as $route => $handler) {
				call_user_func(array($this->ats, $method), $route, $this, $handler);
			}
		}
	}
	
	public function execute() {
		return $this->ats->execute();
	}
}
