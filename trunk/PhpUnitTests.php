<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

/*
 * Run this file via:
 * shell> phpunit PhpUnitTests.php
 */

require_once 'PHPUnit/Framework.php';

class PhpUnitTests {
	
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('uBook');
		self::addTests($suite);
		return $suite;
	}
	
	public static function addTests(&$suite) {
		$testDir = 'test/';
		$handle = opendir($testDir);
		while (($file = readdir($handle)) !== false) {
			if (substr($file, -8) == 'Test.php') {
				require_once $testDir.$file;
				$suite->addTestSuite(substr($file, 0, -4));
			}
		}
		closedir($handle);
	}
	
}
?>