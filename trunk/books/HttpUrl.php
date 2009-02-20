<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

class HttpUrl {

	private $urlString = '';
	private $domainName = '';
	private $directory = '';

	public function __construct($urlString) {
		$this->parseUrl($url);
	}

	public function toString() {
		return $this->urlString;
	}

	public function getDomainName() {
		return $this->domainName;
	}

	public function getDirectory() {
		return $this->directory;
	}
	
	private function parseUrl($serverUrl) {
		$protocol = 'http://';
		if (!self::stringHasPrefix($serverUrl, $protocol)) return;
		$serverAndDirectory = substr($serverUrl, strlen($protocol));
		$directory = strstr($serverAndDirectory, '/');
		$serverName = substr($serverAndDirectory, 0, -(strlen($directory)));
		$this->domainName = $serverName;
		$this->directory = $directory;
	}

	private static function stringHasPrefix($string, $prefix) {
		if (substr($string, 0, strlen($prefix)) == $prefix) {
			return true;
		}
		return false;
	}

}

?>