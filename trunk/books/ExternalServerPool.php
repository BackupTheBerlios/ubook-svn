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

	public function __construct() {
		include 'external_servers.php';
		$this->servers = $external_servers;
		$this->startSize = sizeof($external_servers);
	}

	public function next() {
		if (isset($this->servers[$this->index])) {
			return $this->servers[$this->index++];
		}
		else return null;
	}

	public function append($lineArray) {
		foreach ($lineArray as $i => $xml) {
			$server = ExternalServer::newFromXml($xml);
			if ($server && !$this->isInList($server)) {
				$this->servers[] = $server;
			}
		}
	}

	public function saveInDb() {
		for ($i = $this->startSize; $i < sizeof($this->servers); $i++) {
			$newServer = $this->servers[$i];
			$newServer->dbInsert();
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