<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once '../books/HttpUrl.php';
require_once '../books/HttpConnection.php';


class HttpConnectionTest extends PHPUnit_Framework_TestCase {
	
	function testHttpUrl() {
		$url = new HttpUrl('http://localhost/');
		$connection = new HttpConnection($url);
		$answer = $connection->read();
		if ($anwser === null) {
			$this->fail('Could not test: could not reach http server on localhost.');
		}
		$this->assertFalse($answer == '');
	}
	
}
?>