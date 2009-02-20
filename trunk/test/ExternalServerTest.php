<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once '../books/ExternalServer.php';


class ExternalServerTest extends PHPUnit_Framework_TestCase {
	
	function testNewFromXml() {
		$xml = '<ubookServer name="Bielefeld">http://ubook.asta-bielefeld.de/</ubookServer>';
		$result = ExternalServer::newFromXml($xml);
		$this->assertNotNull($result);
		$this->assertEquals($result->getLocationName(), 'Bielefeld');
	}
	
}
?>