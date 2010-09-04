<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'PHPUnit/Framework.php';
require_once 'books/Book.php';
require_once 'isbn/GoogleProvider.php';
require_once 'isbn/Isbn.php';

class GoogleProviderTest extends PHPUnit_Framework_TestCase {

    function testGetBook() {
        $isbn = new Isbn('0596002068');
        $expected = new Book(array(
                        'author' => 'Ray, Randy J. and Kulchenko, Pavel',
                        'title' => 'Programming Web services with Perl',
                        'isbn' => $isbn->toString(),
                        'year' => '2003'
        ));
        $prov = new GoogleProvider();
        $prov->provideBookFor($isbn);
        $result = $prov->getBook();
        $this->assertEquals($expected, $result);
    }

}
?>