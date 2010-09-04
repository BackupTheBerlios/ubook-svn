<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'PHPUnit/Framework.php';
require_once 'books/Book.php';
require_once 'isbn/Isbn.php';
require_once 'isbn/IsbnDbDotComProvider.php';

class IsbnDbDotComProviderTest extends PHPUnit_Framework_TestCase {

    private static $authKey = 'FGOZ2S4A';

    function testIsbnDbDotCom() {
        if (!self::$authKey) {
            $this->markTestSkipped('Auth key for isbndb.com required.');
        }
        $isbn = new Isbn('0596002068');
        $expected = new Book(array(
                        'author' => 'Randy J. Ray and Pavel Kulchenko',
                        'title' => 'Programming Web services with Perl',
                        'isbn' => $isbn->toString()
        ));
        $prov = new IsbnDbDotComProvider(self::$authKey);
        $prov->provideBookFor($isbn);
        $result = $prov->getBook();
        $this->assertEquals($expected, $result);
    }

}
?>