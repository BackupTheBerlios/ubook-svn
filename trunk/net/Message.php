<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'net/ExternalServer.php';

class Message {

	private $from = '';
	private $bookList = array();
	private $servers = array();

	public static function hasBadChar($string) {
		if (strpos($string, '<') !== false) {
			return true;
		}
		if (strpos($string, '>') !== false) {
			return true;
		}
		if (strpos($string, '"') !== false) {
			return true;
		}
		return false;
	}

	public static function createFromXml($xmlString) {
		try {
			$xml = new SimpleXMLElement($xmlString);
		} catch (Exception $ex) {
			// malformed xml
			return;
		}
		// check for mandatory server tag with name
		if (!isset($xml->server)) return null;
		$from = self::parseFromServer($xml->server);
		if ($from == '') return null;
		$m = new self($from);
		if (isset($xml->books)) {
			$m->parseBookList($xml->books);
		}
		if (isset($xml->servers)) {
			$m->parseServerList($xml->servers);
		}
		return $m;
	}

	public function __construct($from) {
		$this->from = $from;
	}

	public function setBooks($books) {
		$this->bookList = $books;
	}

	public function setServers($servers) {
		$this->servers = $servers;
	}

	public function toXmlString() {
		$doc = new DOMDocument('1.0', 'UTF-8');
		//$doc->formatOutput = true; // Does not work. Why?
		$root = $doc->createElement('root', "\n\t");
		$doc->appendChild($root);
		$from = $doc->createElement('server', null);
		$root->appendChild($from);
		$root->appendChild($doc->createTextNode("\n\t"));
		$from->setAttribute('name', $this->from);
		$books = $doc->createElement('books', "\n\t");
		$root->appendChild($books);
		foreach ($this->bookList as $i => $b) {
			$book = $doc->createElement('book', '');
			$books->appendChild($doc->createTextNode("\t"));
			$books->appendChild($book);
			$books->appendChild($doc->createTextNode("\n\t"));
			$book->setAttribute('url', $b->getUrl());
			$book->setAttribute('author', $b->getAuthor());
			$book->setAttribute('title', $b->getTitle());
			$book->setAttribute('price', $b->getPrice());
		}
		$root->appendChild($doc->createTextNode("\n\t"));
		$servers = $doc->createElement('servers', "\n\t");
		$root->appendChild($servers);
		foreach ($this->servers as $i => $s) {
			$server = $doc->createElement('server', '');
			$servers->appendChild($doc->createTextNode("\t"));
			$servers->appendChild($server);
			$servers->appendChild($doc->createTextNode("\n\t"));
			$server->setAttribute('url', $s->getUrl());
		}
		$root->appendChild($doc->createTextNode("\n"));
		return $doc->saveXML();
	}

	public function fromServer() {
		return $this->from;
	}

	public function resultSize() {
		return sizeof($this->bookList);
	}

	public function bookList() {
		return $this->bookList;
	}

	public function getNewServers() {
		return $this->servers;
	}

	private static function parseFromServer($serverXml) {
		if (!isset($serverXml['name'])) return null;
		$name = (string) $serverXml['name'];
		if (self::hasBadChar($name)) return;
		return $name;
	}

	private function parseBookList($xml) {
		foreach ($xml->book as $b) {
			$this->bookList[] = new ExternalBook($b);
		}
	}

	private function parseServerList($xml) {
		foreach ($xml->server as $s) {
			if (!isset($s['url'])) return;
			$url = (string) $s['url'];
			$server = ExternalServer::newFromUrlString($url);
			if ($server == null) return;
			$this->servers[] = $server;
		}
	}

}

class ExternalBook {

	private $fields;
	private $isValid = false;

	public function  __construct($xmlOrArray) {
		$this->fields = array(
			'url' => NULL,
			'author' => NULL,
			'title' => NULL,
			'price' => NULL
		);
		$this->importFields($xmlOrArray);
	}

	public function importFields($xmlOrArray) {
		foreach ($this->fields as $name => $v) {
			if (!isset($xmlOrArray[$name])) return;
			$newValue = (string) $xmlOrArray[$name];
			if (Message::hasBadChar($newValue)) return;
			$this->fields[$name] = $newValue;
		}
		$this->isValid = true;
	}

	public function getUrl() {
		return $this->fields['url'];
	}

	public function getAuthor() {
		return $this->fields['author'];
	}

	public function getTitle() {
		return $this->fields['title'];
	}

	public function getPrice() {
		return $this->fields['price'];
	}

}

?>