<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2011 Maikel Linke
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