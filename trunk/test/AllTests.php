<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';

/*
require_once 'ExternalBookListTest.php';
require_once 'HttpUrlTest.php';
require_once 'MailerTest.php';
require_once 'MessageTest.php';
require_once 'PhpTest.php';
*/

class AllTests {

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('uBook Tests');
		self::addTests($suite);
/*
		$suite->addTestSuite('ExternalBookListTest');
		$suite->addTestSuite('HttpUrlTest');
		$suite->addTestSuite('MailerTest');
		$suite->addTestSuite('MessageTest');
		$suite->addTestSuite('PhpTest');
*/
		return $suite;
	}

	public static function addTests(&$suite) {
		$handle = opendir('.');
		while (($file = readdir($handle)) !== false) {
			if (substr($file, -8) == 'Test.php') {
				require_once $file;
				$suite->addTestSuite(substr($file, 0, -4));
			}
		}
		closedir($handle);
	}
	
}
?>
