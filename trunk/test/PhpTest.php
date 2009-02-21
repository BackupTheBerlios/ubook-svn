<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';

class PhpTest extends PHPUnit_Framework_TestCase {
	
	function testArrayWalking() {
		$a = array('a', 'b', 'c');
		$this->assertEquals('a', current($a));
		$this->assertEquals('b', next($a));
		$this->assertEquals('b', current($a));
		$a[] = 'd';
		$this->assertEquals('c', next($a));
		$b = array('a', 'b');
		$this->assertEquals('b', next($b));
		reset($a);
		list($i, $v) = each($a);
		$this->assertEquals(0, $i);
		$this->assertEquals('a', $v);
	}
	
}
?>