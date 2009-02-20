<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'HttpUrl.php';

class HttpConnection {

	private $url = null;

	public function __construct($httpUrl) {
		$this->url = $httpUrl;
	}
	
	public function read() {
		$request = self::createRequest($externalServer);
		$filePointer = fsockopen($this->url->getDomain(), 80);
		if ($filePointer == null) return;
		fputs($filePointer, $request);
		$answer = '';
		while (!feof($filePointer)) {
			$answer .= fread($filePointer, 1024);
		}
		fclose($filePointer);
		return $answer;
	}

	private function createRequest() {
		$request = 'GET ' . $this->url->getDirectory() . ' HTTP/1.0'."\n"
		. 'Host: ' . $this->url->getDomain() . "\n"
		. 'Connection: close' . "\n\n";
		return $request;
	}

}

?>