<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2008, 2009 Maikel Linke
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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