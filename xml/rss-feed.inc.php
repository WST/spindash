<?php

/**
* ATS — A web development framework
* © 2007–2013 Ilya I. Averkov <admin@jsmart.web.id>
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

namespace ATS;

final class RSSFeed
{
	private $items = array();
	
	private $title = '';
	private $link = '';
	private $language = 'ru';
	private $description = '';
	private $copyright = '';
	
	public function __construct($address = NULL) {
		
	}
	
	public function items() {
		return $this->items;
	}
	
	public function addItem($title, $pub_date, $link, $description) {
		$this->items[] = new RSSFeedItem($title, $pub_date, $link, $description);
	}
	
	public function __toString() {
		
		$title = htmlspecialchars($this->title);
		$link = htmlspecialchars($this->link);
		$language = htmlspecialchars($this->language);
		$description = htmlspecialchars($this->description);
		$copyright = htmlspecialchars($this->copyright);
		
		$result = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<rss version=\"2.0\">";
		$result .= "<channel><generator>ATS by http://averkov.web.id</generator><title>{$title}</title><link>{$link}</link><description>{$description}</description><language>{$language}</language><copyright>{$copyright}</copyright>";
		$result .= implode('', $this->items);
		$result .= "</channel></rss>";
		
		return $result;
	}
}
