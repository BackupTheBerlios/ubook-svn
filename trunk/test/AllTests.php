<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';

require_once 'ExternalBookListTest.php';
require_once 'HttpUrlTest.php';
require_once 'MailerTest.php';
require_once 'MessageTest.php';
require_once 'PhpTest.php';

class AllTests {

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('uBook Tests');
		$suite->addTestSuite('ExternalBookListTest');
		$suite->addTestSuite('HttpUrlTest');
		$suite->addTestSuite('MailerTest');
		$suite->addTestSuite('MessageTest');
		$suite->addTestSuite('PhpTest');
		return $suite;
	}
	
}
?>