<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

class ExternalServer {

	private $url;
	private $locationName;
	private $serverName;
	private $serverDirectory;
	private $fails = 0;
	private $nextTry = '0000-01-01';

	public static function newFromXml($xml) {
		$found = array();
		eregi('<ubookServer name="([[:print:]]+)">([[:graph:]]+)</ubookServer>', $xml, $found);
		if (sizeof($found) == 3) {
			return new ExternalServer($found[1], $found[2]);
		}
	}

	public static function newFromArray($array) {
		$server = new ExternalServer($array['name'], $array['url']);
		$server->fails = $array['fails'];
		$server->nextTry = $array['next_try'];
		return $server;
	}
	
	public static function blacklist($url) {
		require_once 'mysql_conn.php';
		mysql_query('update servers set next_try="9999-12-31" where url="'.$url.'";');
	}

	public static function activate($url) {
		require_once 'mysql_conn.php';
		mysql_query('update servers set next_try=curdate() where url="'.$url.'";');
	}

	public function __construct($locationName, $url) {
		$this->locationName = $locationName;
		$this->url = $url;
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
	
	public function getUrl() {
		return $this->url;
	}

	public function equals($otherServer) {
		if ($this->url == $otherServer->url) {
			return true;
		}
		if ($this->locationName == $otherServer->locationName) {
			return true;
		}
		return false;
	}

	public function toXml() {
		$xml = '<ubookServer name="'.$this->locationName.'">';
		$xml .= $this->url;
		$xml .= '</ubookServer>';
		return $xml;
	}
	
	public function toHtmlLink() {
		$link = '<a href="' . $this->url . '" target="_blank">'
		. $this->locationName . '</a>';
		return $link;
	}

	public function dbInsert() {
		require_once 'mysql_conn.php';
		$query = 'insert into servers (url, name) values ('
		. '"'.addslashes($this->url).'", '
		. '"'.addslashes($this->locationName).'");';
		mysql_query($query);
	}
	
	public function isBlacklisted() {
		return ($this->nextTry == '9999-12-31');
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