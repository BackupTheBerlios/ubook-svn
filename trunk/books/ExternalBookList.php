<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'AbstractBookList.php';
require_once 'ExternalServer.php';

class ExternalBookList extends AbstractBookList {

	private $searchKey;
	private $server;
	private $booksAsHtmlTable;
	private $newServers;

	public function __construct($searchKey, $externalServer) {
		$this->searchKey = $searchKey;
		$this->server = $externalServer;
		//if (!$this->parseUrl($externalServer)) return;
		$request = self::createRequest($externalServer);
		$filePointer = fsockopen($this->server->getServerDomain(), 80);
		if ($filePointer == null) return;
		fputs($filePointer, $request);
		$answer = '';
		while (!feof($filePointer)) {
			$answer.= fread($filePointer, 1024);
		}
		fclose($filePointer);
		$lineArray = split("\n", $answer);
		$numberOfLines = sizeof($lineArray);
		$statusArray = split(' ', $lineArray[0]);
		if ($statusArray[1] != '200') {
			return;
		}
		$i = 1;
		while ($i < $numberOfLines && trim($lineArray[$i])) {
			$i++;
		}
		if (!isset($lineArray[++$i])) return;
		parent::setSize(trim($lineArray[$i]));
		$restLines = '';
		while ($i++ < $numberOfLines) {
			$restLines .= $lineArray[$i];
		}
		if ($this->size() > 0) {
			$this->booksAsHtmlTable = $restLines;
		}
		else {
			$this->newServers = $restLines;
		}

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

}

?>