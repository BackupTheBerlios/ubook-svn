<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'isbn/IsbnQuery.php';

class IsbnQueryTest extends PHPUnit_Framework_TestCase {

    function testContainsValidChars() {
        $this->assertTrue(IsbnQuery::containsValidChars('978-3897215429'));
        $this->assertFalse(IsbnQuery::containsValidChars('978-38&7215429'));
    }

	function testQuery() {
		$isbn13 = '978-3897215429';
        $expected = new Book(
                'Günther, Karsten',
                'LaTeX',
                '2008',
                '',
                $isbn13
        );
        $result = IsbnQuery::query($isbn13);
		$this->assertEquals($expected, $result);
	}

}
?>