<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'ExternalServer.php';

class ExternalServerPool {

	private $index = 0;
	private $servers = array();
	private $startSize = 0;

	public function __construct($loadNew = false, $loadBlacklisted = false) {
		$this->loadFromDb($loadNew, $loadBlacklisted);
		$this->startSize = sizeof($external_servers);
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

	public function saveInDb() {
		for ($i = $this->startSize; $i < sizeof($this->servers); $i++) {
			$this->servers[$i]->dbInsert();
		}
	}

	public function size() {
		return sizeof($this->servers);
	}

	public function resetDb() {
		mysql_query('delete from servers where url != "";');
		include 'external_servers.php';
		foreach ($external_servers as $i => $server) {
			$server->dbInsert();
		}
		$this->servers = $external_servers;
	}

	private function loadFromDb($loadNew, $loadBlacklisted) {
		$query = 'select url, name, fails, next_try from servers'
		. ' where url != ""';
		/*
		 * TODO: remove parameter
		*/
		if (!$loadNew) {
			$query .= ' and name != ""';
		}
		if (!$loadBlacklisted) {
			$query .= ' and next_try <= curdate()';
		}
		$query .= ';';
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