<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'ExternalServer.php';

class ExternalServerPool {
	
	const queryStart = 'select url, name, fails, next_try from servers ';

	private $index = 0;
	private $servers = array();

	public static function activeServerPool() {
		$pool = new self();
		$pool->loadActive();
		return $pool;
	}

	public static function whiteServerPool() {
		$pool = new self();
		$pool->loadWhite();
		return $pool;
	}

	public static function unknownServerPool() {
		$pool = new self();
		$pool->loadUnknown();
		return $pool;
	}

	public static function blacklistServerPool() {
		$pool = new self();
		$pool->loadBlacklist();
		return $pool;
	}

	public function next() {
		if (isset($this->servers[$this->index])) {
			return $this->servers[$this->index++];
		}
		else return null;
	}

	public function toTextList() {
		$list = '';
		foreach ($this->servers as $i => $server) {
			$list .= $server->getUrl() . "\n";
		}
		return $list;
	}

	public function append($serverList) {
		$lineArray = split("\n", $serverList);
		foreach ($lineArray as $i => $urlString) {
			$server = ExternalServer::newFromUrlString($urlString);
			if ($server && $server->isNew()) {
				$this->add($server);
			}
		}
	}

	public function size() {
		return sizeof($this->servers);
	}

	private function loadActive() {
		$query = self::queryStart
		. ' where url != ""'
		. ' and next_try <= curdate();';
		$this->loadFromDb($query);
	}

	private function loadWhite() {
		$query = self::queryStart
		. ' where url != ""'
		. ' and next_try < "9999-12-31";';
		$this->loadFromDb($query);
	}

	private function loadUnknown() {
		$query = self::queryStart
		. ' where url != ""'
		. ' and name = ""'
		. ' and next_try = "9999-12-31";';
		$this->loadFromDb($query);
	}

	private function loadBlacklist() {
		$query = self::queryStart
		. ' where url != ""'
		. ' and name != ""'
		. ' and next_try = "9999-12-31";';
		$this->loadFromDb($query);
	}

	private function loadFromDb($query) {
		$result = mysql_query($query);
		if (!$result) return;
		while ($serverArray = mysql_fetch_array($result)) {
			$this->servers[] = ExternalServer::newFromDbArray($serverArray);
		}
	}

	private function add($newServer) {
		if ($newServer) {
			if (!$this->isInList($newServer)) {
				$this->servers[] = $newServer;
			}
		}
	}

	private function isInList($newServer) {
		$serverArray = $this->servers;
		foreach ($serverArray as $i => $server) {
			if ($server->equals($newServer)) {
				return true;
			}
		}
		return false;
	}

}

?>