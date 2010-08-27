<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'PHPUnit/Framework.php';
require_once 'books/Book.php';
require_once 'isbn/GoogleProvider.php';
require_once 'net/ThreadedDownloader.php';

class GoogleProviderTest extends PHPUnit_Framework_TestCase {

    function testGetBook() {
        $isbn = '0596002068';
        $expected = new Book(array(
                        'author' => 'Ray, Randy J. and Kulchenko, Pavel',
                        'title' => 'Programming Web services with Perl',
                        'isbn' => $isbn,
                        'year' => '2003'
        ));
        $prov = new GoogleProvider();
        ThreadedDownloader::startDownload($prov->urlFor($isbn), $prov);
        ThreadedDownloader::finishAll();
        $result = $prov->getBook();
        $this->assertEquals($expected, $result);
    }

}
?>