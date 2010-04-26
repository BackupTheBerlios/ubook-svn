<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'PHPUnit/Framework.php';
require_once 'isbn/IsbnDbDotComProvider.php';
require_once 'net/ThreadedDownloader.php';

class IsbnDbDotComProviderTest extends PHPUnit_Framework_TestCase {

    private static $authKey = '';

    function testIsbnDbDotCom() {
        if (!self::$authKey) {
            $this->markTestSkipped('Auth key for isbndb.com required.');
        }
        $isbn = '0596002068';
        $expected = array(
                'author' => 'Randy J. Ray and Pavel Kulchenko',
                'title' => 'Programming Web services with Perl',
                'year' => '',
                'isbn' => $isbn,
                'isbn13' => '9780596002060',
        );
        $prov = new IsbnDbDotComProvider(self::$authKey);
        ThreadedDownloader::startDownload($prov->urlFor($isbn), $prov);
        ThreadedDownloader::finishAll();
        $result = $prov->getBook();
        $this->assertEquals($expected, $result);
    }

}
?>