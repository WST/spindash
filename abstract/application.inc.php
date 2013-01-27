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
		parent::__construct(PHP_SAPI == 'cli' ? API::FRONTEND_FASTCGI : API::FRONTEND_BASIC);
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
		
		if(isset($settings['paths']['layout']['directory'])) {
			parent::useLayoutDirectory($settings['paths']['layout']['directory'], @ $settings['paths']['layout']['webpath']);
		}
		
		if(isset($settings['database'])) {
			switch($settings['database']['engine']) {
				default: throw new CoreException("Unknown database engine in config.inc.php <{$settings['database']['engine']}>"); break;
				case 'MySQL':
					parent::useMySQL($settings['database']['hostname'], $settings['database']['username'], $settings['database']['password'], $settings['database']['name']);
				break;
				case 'PostgreSQL':
					parent::usePostgreSQL($settings['database']['hostname'], $settings['database']['username'], $settings['database']['password'], $settings['database']['name']);
				break;
				case 'SQLite':
					parent::useSQLite($settings['database']['filename']);
				break;
			}
		}
		
		if(isset($settings['cache'])) {
			switch(@ $settings['cache']['engine']) {
				default: throw new CoreException("Unknown caching engine in config.inc.php <{$settings['database']['engine']}>"); break;
				case 'Database':
					parent::useDatabaseCache($settings['cache']['key_prefix']);
				break;
				case 'Memcached':
					parent::useMCCache($settings['cache']['hostname'], $settings['cache']['port'], $settings['cache']['key_prefix']);
				break;
				case 'Redis':
					parent::useRedisCache($settings['cache']['hostname'], $settings['cache']['port'], $settings['cache']['key_prefix']);
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
