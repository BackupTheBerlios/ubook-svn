<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'ExternalBookList.php';
require_once 'ExternalServer.php';
require_once 'HttpUrl.php';
require_once 'HttpConnection.php';
require_once 'ConnectionData.php';
require_once 'LocalServer.php';

require_once 'WEBDIR.php';

class ThreadedBookListReader {

	private $scriptRequest = null;
	private $serverPool = null;
	private $connDataPool = array();
	private $nextConnId = 0;

	public function __construct($externalServerPool, $searchKey) {
		$this->serverPool = $externalServerPool;
		$this->scriptRequest = self::scriptRequest($searchKey);
	}

	public function read() {
		$bookListArray = array();
		$localServer = new LocalServer();
		while ($connData = $this->nextConnData()) {
			$connData->read();
			if (!$connData->end()) continue;
			// This connection is read completely.
			unset($this->connDataPool[$connData->getId()]);
			$bookList = $connData->createBookList();
			if (!$bookList) continue;
			if ($bookList->size() > 0) {
				$bookListArray[] = $bookList;
			}
			else {
				if ($localServer->acceptSuggestedServers()) {
					$this->serverPool->append($connData->getNewServers());
				}
			}
		}
		return $bookListArray;
	}

	private function nextConnData() {
		if ($server = $this->serverPool->next()) {
			// unsused server available, use it
			$urlString = $server->getUrl() . $this->scriptRequest;
			$httpUrl = new HttpUrl($urlString);
			$connection = new HttpConnection($httpUrl);
			if (!$connection->open()) {
				$server->failed();
				return null;
			}
			$connData = new ConnectionData($this->nextConnId, $connection, $server);
			$this->connDataPool[$this->nextConnId++] = $connData;
			return $connData;
		}
		else {
			// nothing new, iterate in connection pool
			$connData = current($this->connDataPool);
			if (!next($this->connDataPool)) {
				reset($this->connDataPool);
			}
			return $connData;
		}
	}

	private static function scriptRequest($searchKey) {
		$requestUrlString = 'query.php?search='
		. urlencode($searchKey->asText())
		. '&from=' . WEBDIR;
		return $requestUrlString;
	}

}

?>