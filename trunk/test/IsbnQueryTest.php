<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'isbn/Isbn.php';
require_once 'isbn/IsbnQuery.php';

class IsbnQueryTest extends PHPUnit_Framework_TestCase {

	function testQuery() {
		$isbn13 = new Isbn('978-3897215429');
        $expected = new Book(
                'Günther, Karsten',
                'LaTeX',
                '2008',
                '',
                $isbn13->toString()
        );
        $result = IsbnQuery::query($isbn13);
		$this->assertEquals($expected, $result);
	}

}
?>