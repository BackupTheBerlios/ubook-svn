<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'HttpUrl.php';

class HttpConnection {
	
	const newline = "\r\n";
	const emptyline = "\r\n\r\n";

	private $url = null;

	public function __construct($httpUrl) {
		$this->url = $httpUrl;
	}
	
	public function read() {
		$request = self::createRequest($externalServer);
		$filePointer = fsockopen($this->url->getDomainName(), 80);
		if ($filePointer === false) return null;
		fputs($filePointer, $request);
		$response = '';
		while (!feof($filePointer)) {
			$response .= fread($filePointer, 1024);
		}
		fclose($filePointer);
		$body = $this->splitBody($response);
		return $body;
	}
	
	private function splitBody($response) {
		list($header, $body) = split(self::emptyline, $response, 2);
		/*
		 * The Status-Code is not used at the moment.
		 * 
		list($statusLine, $generalHeader) = split(self::newline, $header, 2);
		$statusCode = substr($statusLine, 9, 3);
		$this->statusCode = $statusCode;
		 */
		return $body;
	}

	private function createRequest() {
		$request = 'GET ' . $this->url->getDirectory() . ' HTTP/1.0' . self::newline
		. 'Host: ' . $this->url->getDomainName() . self::newline
		. 'Connection: close' . self::emptyline;
		return $request;
	}

}

?>