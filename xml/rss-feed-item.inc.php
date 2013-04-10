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

final class RSSFeedItem
{
	private $title = '';
	private $pub_date = '';
	private $link = '';
	private $description = '';
	
	public function __construct($title, $pub_date, $link, $description) {
		$this->title = $title;
		$this->pub_date = date('r', $pub_date);
		$this->link = $link;
		$this->description = $description;
	}
	
	public function __toString() {
		$title = htmlspecialchars($this->title);
		$pub_date = htmlspecialchars($this->pub_date);
		$link = htmlspecialchars($this->link);
		$description = htmlspecialchars($this->description);
		
		return "<item><title>$title</title><pubDate>$pub_date</pubDate><link>$link</link><description>$description</description></item>";
	}
	
	public function title() {
		return $this->title;
	}
	
	public function pubDate() {
		return $this->pub_date;
	}
	
	public function link() {
		return $this->link;
	}
	
	public function description() {
		return $this->description;
	}
}
