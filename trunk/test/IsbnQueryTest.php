<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2011 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'isbn/Isbn.php';
require_once 'isbn/IsbnQuery.php';

class IsbnQueryTest extends PHPUnit_Framework_TestCase {

    function testQuery() {
        include 'mysql_conn.php';
        $isbn13 = new Isbn('978-3897215429');
        $result = IsbnQuery::query($isbn13);
        $this->assertEquals($isbn13->toString(), $result->get('isbn'));
        $this->assertEquals('2008', $result->get('year'));
    }

}

?>