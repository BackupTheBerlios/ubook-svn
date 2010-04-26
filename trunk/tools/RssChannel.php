<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

/*
 * Represents an RSS channel and produces the correct XML output.
*/
class RssChannel {

	private $doc;
    private $channel;

	/**
	 * Creates an empty news feed.
	 * @param string $title title of the feed
	 * @param string $link link to the website
	 * @param string $desc description
	 * @param string $lang language, e.g. de-de
	 * @param string $copyright author of the feed
	 */
	public function  __construct($title, $link, $desc, $lang, $copyright) {
		$this->doc = $this->createRssDoc();
		$channel = $this->createChannel($title, $link, $desc, $lang, $copyright);
        $this->channel = $channel;
		$this->doc->documentElement->appendChild($channel);
	}

	/**
	 * Adds an image (logo) to the news feed.
	 * @param string $url URL of the image
	 * @param string $title title of the image
	 * @param string $link link to website
	 */
	public function addImage($url, $title, $link) {
		$this->addToChannel($this->createImage($url, $title, $link));
	}

	/**
	 * Adds a news item to the feed.
	 * @param string $id a unique identifyer of this item
	 * @param string $title title of the news item
	 * @param string $desc description of the news item
	 * @param string $link link to a longer text
	 * @param string $author author of the text
	 * @param string $date for example '2010-04-26 15:26:16'
	 */
	public function addItem($id, $title, $desc, $link, $author, $date) {
		$this->addToChannel($this->createItem($id, $title, $desc, $link, $author, $date));
	}

	/**
	 * Sends the XML output to the user.
	 */
	public function send() {
		header('Content-Type: text/xml; charset=utf-8');
		echo $this->doc->saveXML();
	}

	private function createRssDoc() {
		$doc = new DOMDocument('1.0', 'UTF-8');
		$rss = $doc->createElement('rss');
		$rss->setAttribute('version', '2.0');
		$doc->appendChild($rss);
		return $doc;
	}

	private function createElement($tagName, $value = '') {
        $value = str_replace('&', '&amp;', $value);
		return $this->doc->createElement($tagName, $value);
	}

	private function addToChannel(DOMElement $element) {
		$this->channel->appendChild($element);
	}

	private function createChannel($title, $link, $desc, $lang, $copyright) {
		$channel = $this->createElement('channel');
		$channel->appendChild(
				$this->createElement('title', $title)
		);
		$channel->appendChild(
				$this->createElement('link', $link)
		);
		$channel->appendChild(
				$this->createElement('description', $desc)
		);
		$channel->appendChild(
				$this->createElement('language', $lang)
		);
		$channel->appendChild(
				$this->createElement('copyright', $copyright)
		);
		$channel->appendChild(
				$this->createElement('pubDate', date('D, d M Y H:i:s'))
		);
		return $channel;
	}

	private function createImage($url, $title, $link) {
		$image = $this->createElement('image');
		$image->appendChild(
				$this->createElement('url', $url)
		);
		$image->appendChild(
				$this->createElement('title', $title)
		);
		$image->appendChild(
				$this->createElement('link', $link)
		);
		return $image;
	}

	private function createItem($id, $title, $desc, $link, $author, $date) {
		$item = $this->createElement('item');
		$item->appendChild(
				$this->createElement('title', $title)
		);
		$item->appendChild(
				$this->createElement('description', $desc)
		);
		$item->appendChild(
				$this->createElement('link', $link)
		);
		$item->appendChild(
				$this->createElement('author', $author)
		);
		$item->appendChild(
				$this->createElement('guid', $id)
		);
		$time = strtotime($date);
		$item->appendChild(
				$this->createElement('pubDate', date('D, d M Y H:i:s', $time))
		);
		return $item;
	}

}
?>
