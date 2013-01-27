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

class Template extends CoreModule
{
	private $template = NULL;
	private $tags = [];
	private $twig = NULL;
	
	public function __construct(API $base, LayoutDirectory $directory, $filename) {
		parent::__construct($base);
		$this->twig = $directory->twig();
		try {
			$this->template = $this->twig->loadTemplate($filename);
		} catch(\Twig_Error $e) {
			throw new TemplateException($e);
		}
	}

	public function reset() {
		$this->tags = [];
	}
	
	public function addTag($name, $value) {
		$this->tags[$name] = $value;
	}
	
	public function addTags(array & $tags) {
		$this->tags += $tags;
	}
	
	public function push($name, $value) {
		$this->tags[$name][] = $value;
		return true;
	}
	
	public function & tags() {
		return $this->tags;
	}
	
	public function __toString() {
		return $this->template->render($this->tags);
	}
}
