<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2009 Maikel Linke
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