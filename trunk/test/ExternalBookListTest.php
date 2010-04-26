<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'net/ExternalBookList.php';
require_once 'net/Message.php';


class ExternalBookListTest extends PHPUnit_Framework_TestCase {
	
	function testEmptyList() {
		$list = new ExternalBookList('Test', array());
		$this->assertEquals(0, $list->size());
		$this->assertEquals('Test', $list->locationName());
	}
	
	function testExternalBookList() {
        $bookList = array(
            new ExternalBook(array(
                'url' => 'http://bla/',
                'author' => 'Linke, Maikel',
                'title' => 'uBook - Die Bücherbörse',
                'price' => '0',
            ))
        );
		$list = new ExternalBookList('Test', $bookList);
		$this->assertEquals(1, $list->size());
	}
	
}
?>