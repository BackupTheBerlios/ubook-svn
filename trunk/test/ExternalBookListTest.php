<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once '../net/ExternalBookList.php';


class ExternalBookListTest extends PHPUnit_Framework_TestCase {
	
	function testEmptyList() {
		$list = new ExternalBookList('Test', '');
		$this->assertEquals(0, $list->size());
		$this->assertEquals('Test', $list->locationName());
	}
	
	function testExternalBookList() {
		$listString = 'http://bla/<p>Linke, Maikel<p>uBook - Die Bücherbörse<p>0' . "\n";
		$list = new ExternalBookList('Test', $listString);
		$this->assertEquals(1, $list->size());
	}
	
}
?>