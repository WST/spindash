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

final class LayoutDirectory extends Directory
{
	private $webpath = '';
	private $default_extension = '';
	private $global_tags = array();
	private $templates = array();
	private $singleton_templates = array();
	private $loader = NULL;
	private $twig = NULL;
	
	public function __construct(API $base, $path, $webpath) {
		parent::__construct($base, $path);
		
		require_once 'Twig/Autoloader.php';
		
		$this->webpath = $webpath;
		$settings = $this->base->debug() ? array() : array('cache' => $this->base->cachePath());
		try {
			\Twig_Autoloader::register();
			$this->loader = new \Twig_Loader_Filesystem($path);
			$this->twig = new \Twig_Environment($this->loader, array());
		} catch(\Twig_Error_Loader $e) {
			throw new TemplateException($e);
		}
	}
	
	public function setDefaultExtension($extension) {
		$this->default_extension = $extension;
	}
	
	public function setGlobalTags($tags) {
		$this->global_tags = $tags;
	}
	
	public function addGlobalFilter($filter_name, $callback) {
		if(is_array($callback)) {
			$this->twig->addFilter($filter_name, new \Twig_Filter_Method($callback[0], $callback[1]));
		} else {
			$this->twig->addFilter($filter_name, new \Twig_Filter_Function($callback));
		}
	}
	
	public function addGlobalTag($name, $value) {
		$this->global_tags[$name] = $value;
	}
	
	public function pushGlobalTag($name, $value) {
		$this->global_tags[$name][] = $value;
	}
	
	public function openTemplate($template_name) {
		if(!isset($this->templates[$template_name])) {
			$this->templates[$template_name] = new Template($this->base, $this, $template_name . ((is_null($this->default_extension)) ? '' : '.' . $this->default_extension));
		}
		$this->templates[$template_name]->reset();
		$this->templates[$template_name]->addTag('webpath', $this->webpath);
		$this->templates[$template_name]->addTags($this->global_tags);
		return $this->templates[$template_name];
	}
	
	public function template($template_name) {
		return isset($this->singleton_templates[$template_name]) ? $this->singleton_templates[$template_name] : ($this->singleton_templates[$template_name] = $this->openTemplate($template_name));
	}
	
	public function twig() {
		return $this->twig;
	}
}

