<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'ExternalBookList.php';
require_once 'ExternalServer.php';
require_once 'HttpUrl.php';
require_once 'HttpConnection.php';
require_once 'WEBDIR.php';

class ThreadedBookListReader {

	private $serverPool = null;
	private $searchKey = null;

	public function __construct($externalServerPool, $searchKey) {
		$this->serverPool = $externalServerPool;
		$this->searchKey = $searchKey;
	}

	public function read() {
		$bookListArray = array();
		$connectionArray = array();
		$scriptRequest = self::scriptRequest($this->searchKey);
		do {
			if ($server = $this->serverPool->next()) {
				// unsused server available, use it
				$urlString = $server->getUrl() . $scriptRequest;
				$httpUrl = new HttpUrl($urlString);
				$connection = new HttpConnection($httpUrl);
				$pointer = $connection->open();
				if (!$pointer) {
					$server->failed();
					continue;
				}
				$connData = new ConnectionData($server, $pointer);
				$i = sizeof($connectionArray);
				$connectionArray[] = $connData;
			}
			else {
				// nothing new, iterate in connection pool
				list($i, $connData) = each($connectionArray);
				if (!next($connectionArray)) {
					if (reset($connectionArray) === false) {
						break;
					}
				}
			}
			if (!$connData) break; // if both lists are empty
			if ($connData->end()) {
				// end of file, remove from pool and create bookList
				unset($connectionArray[$i]);
				$bookList = $connData->createBookList();
				if (!$bookList) continue;
				if ($bookList->size() > 0) {
					$bookListArray[] = $bookList;
				}
				else {
					$this->serverPool->append($connData->getNewServers());
				}
				continue;
			}
			// read data and store it
			$bodyPart = fread($connData->pointer(), 1024);
			$connData->append($bodyPart);
		}
		while (true);
		return $bookListArray;
	}

	private static function scriptRequest($searchKey) {
		$requestUrlString = 'query.php?search='
		. urlencode($searchKey->asText())
		. '&from=' . WEBDIR;
		return $requestUrlString;
	}

}

class ConnectionData {

	private $server = null;
	private $pointer = null;
	private $body = '';
	private $newServers = '';

	public function __construct($server, $pointer) {
		$this->server = $server;
		$this->pointer = $pointer;
	}

	public function pointer() {
		return $this->pointer;
	}

	public function end() {
		return feof($this->pointer);
	}

	public function append($bodyPart) {
		$this->body .= $bodyPart;
	}

	public function createBookList() {
		$message = Message::parseString($this->body);
		if (!$message) {
			$this->server->failed();
			return;
		}
		$this->server->setLocationName($message->fromServer());
		$this->newServers = $message->getNewServers();
		return new ExternalBookList($message->fromServer(), $message->bookTextList());

	}

	public function getNewServers() {
		return $this->newServers;
	}

}

?>