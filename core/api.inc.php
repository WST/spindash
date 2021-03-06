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

class API
{
	const FRONTEND_BASIC = 0;
	const FRONTEND_FASTCGI = 1;
	const FRONTEND_CLISERVER = 2;

	private $frontend;
	private $debug = false;
	private $cache = NULL;
	private $default_database = NULL;
	private $default_layout = NULL;
	
	private $routes = [];
	private $common_request_handlers = [];
	private $models = [];
	
	/**
	* Create an API instance
	*/
	public function __construct($frontend = self::FRONTEND_BASIC) {
		$this->frontend = $frontend;
		$this->routes['get'] = [];
		$this->routes['post'] = [];
		
		@ set_exception_handler([$this, 'handleException']);
	}
	
	public function __destruct() {
		
	}
	
	/**
	* Checks whether the provided file is includeable
	*/
	public static function isIncludeable($path) {
		if(file_exists($path) && is_readable($path)) {
			return true;
		}

		foreach(explode(PATH_SEPARATOR, ini_get('include_path')) as $include) {
			if(file_exists($f = realpath($include . DIRECTORY_SEPARATOR . $path)) && is_readable($f)) {
				return true;
			}
		}
	
		return false;
	}

	public function registerModel(Database $database, $model_name) {
		// Model manager is created when a model is registered
		if(!array_key_exists($model_name, $this->models)) {
			$this->models[$model_name] = new ModelManager($database, $model_name);
		}
	}

	public function model($model_name) {
		// Models are manipulated through model managers
		if(!isset($this->models[$model_name])) {
			throw new CoreException("Unknown entity model $model_name");
		}
		return $this->models[$model_name];
	}
	
	public function loadModels(Database $database, $filename) {
		if(! @ file_exists($filename)) {
			throw new FileIOException($filename, FILE_IO_FILE_NOT_FOUND);
		}
		if(! @ is_readable($filename)) {
			throw new FileIOException($filename, FILE_IO_ACCESS_DENIED);
		}
		require_once $filename;
	}
	
	public function filter() {
		return new RecordFilter();
	}
	
	public function openLayoutDirectory($path, $webpath) {
		if(!self::isIncludeable('Twig' . DIRECTORY_SEPARATOR . 'Autoloader.php')) {
			throw new CoreException('You don’t seem to have <a href="http://www.twig-project.org/">Twig engine</a> installed');
		}
		
		require_once SPINDASH_TEXTPROC . 'layout-directory.inc.php';
		require_once SPINDASH_TEXTPROC . 'template.inc.php';
		
		return new LayoutDirectory($this, $path, $webpath);
	}
	
	public function useLayoutDirectory($path, $webpath) {
		if(is_null($this->default_layout)) {
			$this->default_layout = $this->openLayoutDirectory($path, $webpath);
		} else {
			throw new CoreException('Default layout directory is already set');
		}
	}
	
	public function openDirectory($path) {
		return new Directory($this, $path);
	}

	public function openSMSGateway($username, $password) {
		return new SMSGateway($this, $username, $password);
	}
	
	public function frontend() {
		return $this->frontend;
	}
	
	public function logMessage($message, $level = SPINDASH_LOGLEVEL_INFORMATION) {
		// TODO
	}
	
	public function openDatabase($hostname, $username, $password, $database, $use_unix_socket = false) {
		// Initialize the database subsystem
		require_once SPINDASH_DB . 'mysql.inc.php';
		require_once SPINDASH_DB . 'model.inc.php';
		require_once SPINDASH_DB . 'model-manager.inc.php';
		require_once SPINDASH_DB . 'record-filter.inc.php';
		require_once SPINDASH_DB . 'statement.inc.php';
		
		return new MySQL($this, $hostname, $username, $password, $database, $use_unix_socket);
	}
	
	public function openSQLiteDatabase($filename) {
		require_once SPINDASH_DB . 'sqlite.inc.php';
		require_once SPINDASH_DB . 'model.inc.php';
		require_once SPINDASH_DB . 'model-manager.inc.php';
		require_once SPINDASH_DB . 'record-filter.inc.php';
		require_once SPINDASH_DB . 'statement.inc.php';
		
		return new SQLite($this, $filename);
	}
	
	public function openPostgreSQLDatabase($hostname, $username, $password, $database) {
		require_once SPINDASH_DB . 'postgresql.inc.php';
		require_once SPINDASH_DB . 'model.inc.php';
		require_once SPINDASH_DB . 'model-manager.inc.php';
		require_once SPINDASH_DB . 'record-filter.inc.php';
		require_once SPINDASH_DB . 'statement.inc.php';
		
		return new PostgreSQL($this, $hostname, $username, $password, $database);
	}
	
	public function useMySQL($hostname, $username, $password, $database, $use_unix_socket = false) {
		if(is_null($this->default_database)) {
			$this->default_database = $this->openDatabase($hostname, $username, $password, $database, $use_unix_socket);
		} else {
			throw new CoreException('Default database is already initialized');
		}
	}
	
	public function useDatabase($hostname, $username, $password, $database, $use_unix_socket = false) {
		return $this->useMySQL($hostname, $username, $password, $database, $use_unix_socket);
	}
	
	public function useSQLite($filename) {
		if(is_null($this->default_database)) {
			$this->default_database = $this->openSQLiteDatabase($filename);
		} else {
			throw new CoreException('Default database is already initialized');
		}
	}
	
	public function usePostgreSQL($hostname, $username, $password, $database) {
		if(is_null($this->default_database)) {
			$this->default_database = $this->openPostgreSQLDatabase($hostname, $username, $password, $database);
		} else {
			throw new CoreException('Default database is already initialized');
		}
	}
	
	public function database() {
		if(is_null($this->default_database)) {
			throw new CoreException('Default database is not initialized yet');
		}
		return $this->default_database;
	}
	
	public function layout() {
		if(is_null($this->default_layout)) {
			throw new CoreException('Default layout directory is not set');
		}
		return $this->default_layout;
	}
	
	private function makeLink($template, $page_number) {
		return str_replace('{page}', $page_number, $template);
	}
	
	public function createPager($element_count, $elements_per_page, $current_page, $link_template, $additional_class = 'pagination-centered', $previous_page = 'Previous_page', $next_page = 'Next page') {
		
		$pages = [];
		
		$pages_to = ($element_count % $elements_per_page) ? floor($element_count / $elements_per_page) + 1 : ($element_count / $elements_per_page);
		
		$pages[0] = ($current_page == 1) ? '' : '<li><a href="' . $this->makeLink($link_template, $current_page - 1) . '" title="' . $previous_page . '">←</a></li>';
		
		if($pages_to < 10) {
			for($i = 1; $i <= $pages_to; $i ++) {
				$selected = ($i == $current_page) ? ' class="active"' : '';
				$pages[$i] = '<li' . $selected . '><a href="' . $this->makeLink($link_template, $i) . '">' . $i . '</a></li>';
			}
		} else {
			for($i = 1; $i <= 5; $i ++) {
				$selected = ($i == $current_page) ? ' class="active"' : '';
				$pages[$i] = '<li' . $selected . '><a href="' . $this->makeLink($link_template, $i) . '">' . $i . '</a></li>';
			}
			
			if($current_page > 5 && $current_page < $pages_to - 4) {
				if($current_page > 6) {
					$pages[6] = '…';
				}
				$pages[$current_page] = '<li class="active"><a href="' . $this->makeLink($link_template, $current_page) . '">' . $current_page . '</a></li>';
				if($current_page < $pages_to - 5) {
					$pages[$pages_to - 5] = '…';
				}
			} else {
				$pages[6] = '…';
			}
			
			for($i = $pages_to - 4; $i <= $pages_to; $i ++) {
				$selected = ($i == $current_page) ? ' class="active"' : '';
				$pages[$i] = '<li' . $selected . '><a href="' . $this->makeLink($link_template, $i) . '">' . $i . '</a></li>';
			}
		}
		
		$pages[$pages_to + 1] = ($current_page == $pages_to) ? '' : '<li><a href="' . $this->makeLink($link_template, $current_page + 1) . '" title="' . $next_page . '">→</a></li>';
		
		
		return '<div class="pagination' . ($additional_class ? " $additional_class" : '') . '"><ul>' . implode('', $pages) . '</ul></div>';
	}
	
	public function cachePath() {
		return sys_get_temp_dir();
	}
	
	public function parseBBCode($text) {
		require_once SPINDASH_TEXTPROC . 'bbcode.inc.php';
		
		$bbcode = new BBCode($this);
		return $bbcode->parse($text);
	}
	
	public function registerCommonRequestHandler() {
		$args = func_get_args();
		$this->common_request_handlers[] = (count($args) == 1) ? $args[0] : [$args[0], $args[1]];
	}
	
	public function get() {
		$args = func_get_args();
		$this->routes['get'][$args[0]] =
			(count($args) == 2) ? $args[1] : ((count($args) == 3) ? [$args[1], $args[2]] : [$args[1], $args[2], $args[3]]);
	}
	
	public function post() {
		$args = func_get_args();
		$this->routes['post'][$args[0]] =
			(count($args) == 2) ? $args[1] : ((count($args) == 3) ? [$args[1], $args[2]] : [$args[1], $args[2], $args[3]]);
	}
	
	public function gp() {
		switch(count($args = func_get_args())) {
			case 2:
				$this->get($args[0], $args[1]);
				$this->post($args[0], $args[1]);
			break;
			case 3:
				$this->get($args[0], $args[1], $args[2]);
				$this->post($args[0], $args[1], $args[2]);
			break;
			case 4:
				$this->get($args[0], $args[1], $args[2], $args[3]);
				$this->post($args[0], $args[1], $args[2], $args[3]);
			break;
		}
	}
	
	private function selectHandler(Request & $request) {
		if(!array_key_exists($type = $request->method(), $this->routes)) {
			throw new CoreException("Unsupported request method {$type}");
		}
		
		$request_path = $request->requestPath();
		if(($pos = strpos($request_path, '?')) !== false) $request_path = substr($request_path, 0, $pos);
		foreach($this->routes[$type] as $k => $v) {
			$pattern = preg_replace(['#(:[a-z_]+?)#iU', '#(%[a-z_]+?)#iU'], ['([^/]+)', '([\\d]+)'], $k);
			$matches = [];
			if(preg_match("#^$pattern$#", $request_path, $matches)) {
				return count($matches) > 1 ? [$v, $matches] : [$v];
			}
		}
		
		throw new CoreException("no route matching {$request_path}");
	}
	
	private function process(Request $request) {
		$response = new Response($this);
		
		if(!is_null($this->cache) && $this->cache->handleRequest($request, $response)) {
			return $response;
		}
		
		foreach($this->common_request_handlers as $callback) {
			if(!is_callable($callback)) continue;
			
			ob_start();
			call_user_func($callback, $request, $response);
			$error = ob_get_contents();
			ob_clean();
			
			if($error != '') {
				throw new CoreException($error);
			}
			
			if($response->ready()) {
				return $response;
			}
		}
		
		try {
			$handler = $this->selectHandler($request);
		} catch(CoreException $e) {
			$this->documentNotFound($request, $response);
			return $response;
		}
		
		if(count($handler[0]) == 3) {
			$handler[0] = [new $handler[0][0]($handler[0][2]), $handler[0][1]];
		}
		
		if(!is_callable($handler[0])) {
			$this->documentNotFound($request, $response);
			return $response;
		}
		
		isset($handler[1]) ? call_user_func($handler[0], $request, $response, $handler[1]) : call_user_func($handler[0], $request, $response);
		
		if(!is_null($this->cache)) {
			$this->cache->handleResponse($request, $response);
		}
		
		return $response;
	}
	
	private function processBasicRequest() {
		if(!is_object($response = $this->process($request = new Request($this)))) {
			throw new CoreException("request handler has corrupted it’s SpinDash\Response instance");
		}
		return $response->sendBasic();
	}
	
	private function handleFastCGIClient($client) {
		socket_set_nonblock($client);
		
		$raw_request = '';
		while(true) {
			$recv = socket_read($client, 1024, PHP_BINARY_READ);
			if($recv == '') break;
			$raw_request .= $recv;
		}
		
		try {
			// Creating Request instance
			$request = new Request($this);
			$request->parseFastCGIRequest($raw_request);
			if(is_object($response = $this->process($request))) {
				$response->sendFastCGI($client);
			} else {
				// Log an error
			}
		} catch(CoreException $e) {
			// Log an error
		}
	}
	
	private function fastCGI() {
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_bind($socket, '127.0.0.1', 8000);
		socket_listen($socket);
		
		while(($client = socket_accept($socket)) !== false) {
			$this->handleFastCGIClient($client);
			socket_close($client);
		}
	}
	
	public function execute() {
		switch($this->frontend) {
			case self::FRONTEND_BASIC: return $this->processBasicRequest(); break;
			case self::FRONTEND_FASTCGI: return $this->fastCGI(); break;
		}
		throw new CoreException('unknown frontend selected');
	}
	
	public function useCacheEngine(CacheEngine $engine) {
		$this->cache = $engine;
	}
	
	public function useMCCache($hostname, $port, $key_prefix = '') {
		require_once SPINDASH_CACHE . 'mc-cache-engine.inc.php';
		$this->useCacheEngine(new MCCacheEngine($this, $key_prefix, $hostname, $port));
	}
	
	public function useRedisCache($hostname, $port, $key_prefix = '') {
		require_once SPINDASH_CACHE . 'redis-cache-engine.inc.php';
		$this->useCacheEngine(new RedisCacheEngine($this, $key_prefix, $hostname, $port));
	}
	
	public function useDatabaseCache($key_prefix = '') {
		if(is_null($this->default_database)) {
			throw new CoreException('You should be „using” the default database in order to start database caching');
		}
		
		require_once SPINDASH_CACHE . 'database-cache-engine.inc.php';
		$this->useCacheEngine(new DatabaseCacheEngine($this, $this->default_database, $key_prefix));
	}
	
	public function openSegaInputFile($filename) {
		require_once SPINDASH_FILEIO . 'sega-input-file.inc.php';
		return new SegaInputFile($this, $filename);
	}
	
	public function createRSSFeed($title, $link) {
		require_once SPINDASH_XML . 'rss-feed-item.inc.php';
		require_once SPINDASH_XML . 'rss-feed.inc.php';
		
		return new RSSFeed($this, $title, $link);
	}
	
	public function phoneNumber($phone_number_string) {
		require_once SPINDASH_APIS . 'phone-number.inc.php';
		
		return new PhoneNumber($this, $phone_number_string);
	}
	
	public function simplePage($title, $body, $description = '') {
		$page = new TextFile(SPINDASH_ROOT . 'misc' . DIRECTORY_SEPARATOR . 'simple_page.htt');
		$page->replace(['{TITLE}', '{BODY}', '{DESCRIPTION}', '{VERSION}'], [ucfirst($title), ucfirst($body), ucfirst($description), SPINDASH_VERSION]);
		return (string) $page;
	}
	
	public function documentNotFound($request, & $response) {
		$response->setStatusCode(404);
		$response->setBody($this->simplePage('404 Not Found', 'Requested page was not found', 'This means that you’ve requested something that does not exist within this site. If you beleive this should not happen, contact the website owner.'));
	}
	
	public function handleException(\Exception $e) {
		die($this->simplePage('General error', $e->getMessage(), 'This could happen because of an error in the web application’s code, settings or database. If you are the owner of this website, contact your web programming staff.'));
	}
	
	public function debug() {
		return $this->debug;
	}
}
