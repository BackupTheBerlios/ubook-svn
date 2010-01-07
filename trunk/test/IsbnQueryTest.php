<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'isbn/IsbnQuery.php';

class IsbnQueryTest extends PHPUnit_Framework_TestCase {

	function testQuery() {
		$isbn13 = '978-3897215429';
		$expected = array(
		'author' => 'Günther, Karsten',
		'title' => 'LaTeX',
		'year' => '2008',
        'isbn' => $isbn13
		);
        $result = IsbnQuery::query($isbn13);
		$this->assertEquals($expected, $result);
	}

}
?>