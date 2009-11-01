<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'tools/KeyGenerator.php';

class KeyGeneratorTest extends PHPUnit_Framework_TestCase {

	function testGenKey() {
		$key1 = KeyGenerator::genKey();
		$key2 = KeyGenerator::genKey();
		$this->assertNotEquals($key1, $key2);
	}

}
?>