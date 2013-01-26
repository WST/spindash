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

abstract class Application extends API implements IApplication
{
	// Left for compatibility
	protected $sd = NULL;
	protected $ats = NULL;
	
	public function __construct($debug) {
		parent::__construct($debug);
		parent::registerCommonRequestHandler($this, 'initializeCoreRoutes');
		
		// Left for compatibility
		$this->sd = $this->ats = $this;
	}
	
	public function initializeCoreRoutes(Request $request) {
		$methods = call_user_func(array($this, 'routeMap'), $request);
		foreach($methods as $method => $routes) {
			foreach($routes as $route => $handler) {
				call_user_func(array($this, $method), $route, $this, $handler);
			}
		}
	}
}
