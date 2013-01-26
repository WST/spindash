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
	
	private $settings = array();
	
	public function __construct($configuration_file_name) {
		parent::__construct();
		$this->initializeConfiguration($configuration_file_name);
		
		if(method_exists($this, 'routeMap')) {
			parent::registerCommonRequestHandler($this, 'initializeDynamicRouteTable');
		}
		
		// Left for compatibility
		$this->sd = $this->ats = $this;
	}
	
	private function initializeConfiguration($configuration_file_name) {
		if(!parent::isIncludeable($configuration_file_name)) {
			throw new CoreException("Configuration file <$configuration_file_name> is not includeable");
		}
		$settings = & $this->settings;
		require_once $configuration_file_name;
		
		foreach($settings as $setting => $value) {
			switch($setting) {
				case 'paths':
					
				break;
				case 'core':
					
				break;
				case 'database':
					
				break;
			}
		}
	}
	
	protected function initializeDynamicRouteTable(Request $request) {
		$methods = call_user_func(array($this, 'routeMap'), $request);
		foreach($methods as $method => $routes) {
			foreach($routes as $route => $handler) {
				call_user_func(array($this, $method), $route, $this, $handler);
			}
		}
	}
}
