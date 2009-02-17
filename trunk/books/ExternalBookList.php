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
	private $newServers = array();

	public function __construct($searchKey, $externalServer) {
		$this->searchKey = $searchKey;
		$this->server = $externalServer;
		$request = self::createRequest($externalServer);
		$filePointer = fsockopen($this->server->getServerDomain(), 80);
		if ($filePointer == null) return;
		fputs($filePointer, $request);
		$answer = '';
		while (!feof($filePointer)) {
			$answer.= fread($filePointer, 1024);
		}
		fclose($filePointer);
		$sectionArray = split('<!-- section -->', $answer);
		if (sizeof($sectionArray) != 4) return;
		$this->rememberName($sectionArray[1]);
		$this->setSizeString($sectionArray[2]);
		$this->parseList($sectionArray[3]);
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

	private function createRequest() {
		$host = $this->server->getServerDomain();
		$dir = $this->server->getServerDirectory();
		$request = 'GET '.$dir;
		$request .= 'query.php?search='.$this->searchKey->asText().' HTTP/1.0'."\n";
		$request .= 'Host: '.$host."\n";
		$request .= 'Connection: close'."\n\n";
		return $request;
	}

	private function rememberName() {

	}

	private function setSizeString($sizeString) {
		parent::setSize(trim($sizeString));
	}

	private function parseList($listString) {
		if ($this->size() > 0) {
			$this->booksAsHtmlTable = $listString;
		}
		else {
			$this->newServers = split("\n", $listString);
		}
	}

}

?>