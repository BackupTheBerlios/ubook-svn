<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

class ExternalServer {

	private $url;
	private $locationName;
	private $fails = 0;
	private $nextTry = '0000-01-01';
	private $dataFromDatabase = false;

	public static function newFromDbArray($array) {
		$server = new ExternalServer($array['name'], $array['url']);
		$server->fails = $array['fails'];
		$server->nextTry = $array['next_try'];
		$server->dataFromDatabase = true;
		return $server;
	}

	public static function newFromUrlString($urlString) {
		if (strlen($urlString) <= 7) return;
		if (self::containsSpecialChar($urlString)) return;
		require_once 'WEBDIR.php';
		if ($urlString == WEBDIR) return;
		require_once 'HttpUrl.php';
		$url = new HttpUrl($urlString);
		if ($url->getDomainName() == 'localhost') return;
		return new ExternalServer('', $urlString);
		return new self('', $urlString);
	}

	public static function blacklist($url) {
		require_once 'mysql_conn.php';
		mysql_query('update servers set next_try="9999-12-31" where url="'.$url.'";');
	}

	public static function activate($url) {
		require_once 'mysql_conn.php';
		mysql_query('update servers set next_try=curdate() where url="'.$url.'";');
	}

	public static function delete($url) {
		require_once 'mysql_conn.php';
		mysql_query('delete from servers where url="'.$url.'";');
	}

	public function __construct($locationName, $url) {
		$this->locationName = $locationName;
		$this->url = $url;
	}

	public function getLocationName() {
		return $this->locationName;
	}

	public function setLocationName($name) {
		if (self::containsSpecialChar($name)) return;
		if ($name == $this->locationName) return;
		$this->locationName = $name;
		if ($this->dataFromDatabase) {
			require_once 'mysql_conn.php';
			$query = 'update servers set'
			. ' name = "' . $this->locationName . '"'
			. ' , fails = 0'
			. ' where url = "' . $this->url . '";';
			mysql_query($query);
		}
		else {
			$this->dbInsert();
		}
	}

	public function getUrl() {
		return $this->url;
	}

	public function equals($otherServer) {
		if ($this->url == $otherServer->url) {
			return true;
		}
		if ($otherServer->locationName) {
			if ($this->locationName == $otherServer->locationName) {
				return true;
			}
		}
		return false;
	}

	public function toHtmlLink() {
		if ($this->locationName) {
			$linkName = $this->locationName;
		}
		else {
			$linkName = $this->url;
		}
		$link = '<a href="' . $this->url . '" target="_blank">'
		. $linkName . '</a>';
		return $link;
	}

	public function dbInsert() {
		if ($this->fails) return;
		require_once 'mysql_conn.php';
		$query = 'insert into servers (url, name) values ('
		. '"'.$this->url.'", '
		. '"'.$this->locationName.'");';
		mysql_query($query);
		$this->dataFromDatabase = true;
	}

	public function isBlacklisted() {
		return ($this->nextTry == '9999-12-31');
	}

	public function failed() {
		$this->fails++;
		if ($this->dataFromDatabase) {
			if ($this->locationName == '') {
				self::delete(($this->url));
				return;
			}
			if ($this->fails > 8) {
				self::delete($this->url);
				return;
			}
			require_once 'mysql_conn.php';
			$query = 'update servers set'
			. ' fails = fails + 1'
			. ' , next_try = adddate(curdate(), fails * fails)'
			. ' where url = "' . $this->url . '";';
			mysql_query($query);
		}
	}

	private static function containsSpecialChar($string) {
		if (strpos($string, '"') !== false) return true;
		if (strpos($string, "'") !== false) return true;
		if (strpos($string, '\\') !== false) return true;
		if (strpos($string, "\0") !== false) return true;
		return false;
	}

}

?>