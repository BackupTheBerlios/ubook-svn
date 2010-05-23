<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'PHPUnit/Framework.php';
require_once 'books/Book.php';
require_once 'isbn/UBKarlsruheProvider.php';
require_once 'net/ThreadedDownloader.php';

class UBKarlsruheProviderTest extends PHPUnit_Framework_TestCase {

    function testUBKarlsruhe() {
        $isbn13 = '978-3897215429';
        $expected = new Book(array(
                        'author' => 'Günther, Karsten',
                        'title' => 'LaTeX',
                        'year' => '2008',
                        'isbn' => $isbn13
        ));
        $prov = new UBKarlsruheProvider();
        ThreadedDownloader::startDownload($prov->urlFor($isbn13), $prov);
        ThreadedDownloader::finishAll();
        $result = $prov->getBook();
        $this->assertEquals($expected, $result);
    }

}
?>