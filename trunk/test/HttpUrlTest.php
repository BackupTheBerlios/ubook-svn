<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'net/HttpUrl.php';


class HttpUrlTest extends PHPUnit_Framework_TestCase {
	
	function testHttpUrl() {
		$urlString = 'http://localhost/ubook/';
		$url = new HttpUrl($urlString);
		$this->assertEquals('localhost', $url->getDomainName());
		$this->assertEquals('/ubook/', $url->getDirectory());
	}
	
}
?>