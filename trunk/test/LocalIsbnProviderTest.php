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
require_once 'books/Book.php';
require_once 'isbn/LocalIsbnProvider.php';
require_once 'isbn/Isbn.php';

class LocalIsbnProviderTest extends PHPUnit_Framework_TestCase {

    private $isbn;
    private $book;

    function setUp() {
        include 'mysql_conn.php';
        $isbn = new Isbn('0596002068');
        $book = new Book(array(
                    'author' => 'Linke, Maikel',
                    'title' => 'uBook Test',
                    'isbn' => $isbn->toString(),
                    'price' => '0,00',
                    'year' => '2011',
                    'description' => 'This is only a test.'
                ));
        $query = 'insert into books'
                . ' (id, author, title, isbn, price, year, description) values'
                . ' (1, "' . $book->get('author') . '", "' . $book->get('title')
                . '", "' . $book->get('isbn')
                . '", "' . $book->get('price') . '", "' . $book->get('year')
                . '", "' . $book->get('description') . '");';
        mysql_query($query);
        $this->isbn = $isbn;
        $this->book = $book;
    }

    function tearDown() {
        mysql_query('delete from books where mail="";');
    }

    function testQuery() {
        $prov = new LocalIsbnProvider();
        $result = $prov->query($this->isbn);
        $this->assertEquals($this->book, $result);
    }

}

?>