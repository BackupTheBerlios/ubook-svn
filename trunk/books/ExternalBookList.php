<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'AbstractBookList.php';
require_once 'ExternalServer.php';

class ExternalBookList extends AbstractBookList {

	private $searchKey = null;
	private $server = null;
	private $booksAsHtmlTable = '';
	private $newServers = '';

	public function __construct($server, $stringFromServer) {
		$this->server = $server;
		$this->parseString($stringFromServer);
	}

	public function locationName() {
		return $this->server->getLocationName();
	}

	public function toHtmlTable() {
		return $this->booksAsHtmlTable;
	}

	public function getNewServers() {
		return $this->newServers;
	}
	
	private function parseString($string) {
		$sectionArray = split('<!-- section -->', $string);
		if (sizeof($sectionArray) != 4) {
			$this->server->failed();
			return;
		}
		$this->rememberName($sectionArray[1]);
		$this->setSizeString($sectionArray[2]);
		$this->parseList($sectionArray[3]);
	}

	private function rememberName($serverName) {
		$this->server->setLocationName(trim($serverName));
	}

	private function setSizeString($sizeString) {
		parent::setSize(trim($sizeString));
	}

	private function parseList($listString) {
		if ($this->size() > 0) {
			$this->booksAsHtmlTable = $listString;
		}
		else {
			$this->newServers = $listString;
		}
	}

}

?>