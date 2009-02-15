<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'AbstractBookList.php';

class ExternalBookList extends AbstractBookList {
	
	private $searchKey;
	private $server;
	private $location;

	public function __construct($searchKey, $externalServer) {
		$this->searchKey = $searchKey;
		if (!$this->parseUrl($externalServer)) return;
		$request = self::createRequest($externalServer);
		$filePointer = fsockopen($this->server, 80);
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
		$booksAsHtml = '';
		while ($i++ < $numberOfLines) {
			$booksAsHtml .= $lineArray[$i];
		}
		parent::setHtmlRows($booksAsHtml);

	}
	
	private function parseUrl($serverUrl) {
		$protocol = 'http://';
		if (!self::stringHasPrefix($serverUrl, $protocol)) return;
		$serverAndLocation = substr($serverUrl, strlen($protocol));
		$this->location = strstr($serverAndLocation, '/');
		$this->server = substr($serverAndLocation, 0, -(strlen($this->location)));
		return true;
	}

	private function createRequest() {
		$request = 'GET '.$this->location;
		$request .= 'query.php?search='.$this->searchKey->asText().' HTTP/1.0'."\n";
		$request .= 'Host: '.$this->server."\n";
		$request .= 'Connection: close'."\n\n";
		return $request;
	}
	
	private static function stringHasPrefix($string, $prefix) {
		if (substr($string, 0, strlen($prefix)) == $prefix) {
			return true;
		}
		return false;
	}

}

?>