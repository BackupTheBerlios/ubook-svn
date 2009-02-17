<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */
/*
 * TODO: create table servers (url varchar(128) primary key not null, name varchar(128) not null, last_active date not null, fails tinyint unsigned not null);
 * 
 */

class ExternalServer {

	private $url;
	private $locationName;
	private $serverName;
	private $serverDirectory;

	public static function newFromXml($xml) {
		$found = array();
		eregi('<ubookServer name="([[:print:]]+)">([[:graph:]]+)</ubookServer>', $xml, $found);
		if (sizeof($found) == 3) {
			return new ExternalServer($found[1], $found[2]);
		}
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

	public function dbInsert() {
		require_once 'mysql_conn.php';
		$query = 'insert into servers values ('
		. '"'.addslashes($this->url).'", '
		. '"'.addslashes($this->locationName).'")';
		mysql_query($query);
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