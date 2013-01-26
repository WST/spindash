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
	protected $sd = NULL;
	
	public function __construct($debug) {
		$this->sd = new API($debug);
		$this->sd->registerCommonRequestHandler($this, 'initializeCoreRoutes');
	}
	
	public function initializeCoreRoutes(Request $request) {
		$methods = call_user_func(array($this, 'routeMap'), $request);
		foreach($methods as $method => $routes) {
			foreach($routes as $route => $handler) {
				call_user_func(array($this->sd, $method), $route, $this, $handler);
			}
		}
	}
	
	public function execute() {
		return $this->sd->execute();
	}
}
