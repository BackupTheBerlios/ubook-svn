<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2011 Maikel Linke
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