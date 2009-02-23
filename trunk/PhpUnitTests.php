<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';

require_once 'test/AllTests.php';

class PhpUnitTests {
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('uBook');
		$suite->addTest(AllTests::suite());
		return $suite;
	}
	
	
}
?>