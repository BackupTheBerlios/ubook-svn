<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'ExternalServer.php';

class ExternalServerPool {

	private $index = 0;
	private $servers = array();

	public function __construct() {
		include 'external_servers.php';
		$this->servers = $external_servers;
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
			if ($server) {
				$this->servers[] = $server;
			}
		}
	}

}

?>