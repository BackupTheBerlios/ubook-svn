<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

class Message {

	const token = "<!-- section -->\n";

	private $from = '';
	private $resultSize = 0;
	private $bookList = '';
	private $servers = '';

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

	public static function parseString($string) {
		$sectionArray = split(self::token, $string);
		if (sizeof($sectionArray) != 5) {
			return;
		}
		if (self::hasBadChar($sectionArray[1])) return;
		if (self::hasBadChar($sectionArray[2])) return;
		foreach ($sectionArray as $i => $section) {
			$sectionArray[$i] = trim($section);
		}
		return new Message($sectionArray[1], $sectionArray[2], $sectionArray[3], $sectionArray[4]);
	}

	public function __construct($from, $resultSize, $bookString, $serverString) {
		$this->setFrom($from);
		$this->setSize($resultSize);
		$this->setBooks($bookString);
		$this->setServers($serverString);
	}

	public function fromServer() {
		return $this->from;
	}

	public function resultSize() {
		return $this->resultSize;
	}

	public function bookTextList() {
		return $this->bookList;
	}
	
	public function getNewServers() {
		$this->servers;
	}

	public function toString() {
		$string = self::token . $this->from . "\n"
		. self::token . $this->resultSize . "\n"
		. self::token . $this->bookList . "\n"
		. self::token . $this->servers . "\n";
		return $string;
	}

	private function setFrom($from) {
		$this->from = trim($from);
	}

	private function setSize($sizeLine) {
		$this->resultSize = (int) trim($sizeLine);
	}

	private function setBooks($bookString) {
		$this->bookList = trim($bookString);
	}

	private function setServers($serverString) {
		$this->servers = trim($serverString);
	}

}

?>