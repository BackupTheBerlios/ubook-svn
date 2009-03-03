<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'net/HttpConnection.php';
require_once 'net/HttpUrl.php';


class HttpConnectionTest extends PHPUnit_Framework_TestCase {

    function testRead() {
        $conn = new HttpConnection(new HttpUrl('http://localhost/'));
        $this->assertNotNull($conn);
        $fp = $conn->open();
        if (!$fp) {
            $this->markTestSkipped('Could not connect to localhost.');
        }
        $readCount = 0;
        while (($readString = $conn->read()) !== null) {
            $this->assertType('string', $readString);
            $readCount++;
        }
        $this->assertNotEquals(0, $readCount, 'Nothing read.');
        if ($readCount == 1) {
            $this->markTestSkipped('Direct Response!'
            . ' Perhaps the connection is blocking?');
        }
        $this->assertType('string', $conn->getBody());
        $this->assertContains('html', $conn->getBody());
    }

}
?>