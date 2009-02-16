<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

class ExternalServer {
	
	private $locationName;
	private $serverName;
	private $serverDirectory;

	public function __construct($locationName, $url) {
		$this->locationName = $locationName;
		$this->parseUrl($url);
	}
	
	public function getLocationName() {
		return $this->locationName;
	}
	
	public function getServerDomain() {
		return $this->serverName;
	}
	
	public function getServerDirectory() {
		return $this->serverDirectory;
	}
	
	private function parseUrl($serverUrl) {
		$protocol = 'http://';
		if (!self::stringHasPrefix($serverUrl, $protocol)) return;
		$serverAndDirectory = substr($serverUrl, strlen($protocol));
		$directory = strstr($serverAndDirectory, '/');
		$serverName = substr($serverAndDirectory, 0, -(strlen($directory)));
		$this->serverName = $serverName;
		$this->serverDirectory = $directory;
	}

	private static function stringHasPrefix($string, $prefix) {
		if (substr($string, 0, strlen($prefix)) == $prefix) {
			return true;
		}
		return false;
	}

}

?>