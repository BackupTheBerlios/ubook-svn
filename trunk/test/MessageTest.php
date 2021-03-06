<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2010 Maikel Linke
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
require_once 'net/Message.php';

class MessageTest extends PHPUnit_Framework_TestCase {

    function testMessageFromXml() {
        $xmlString = file_get_contents("test/uBookAnswer.xml");
        $m = Message::createFromXml($xmlString);
        $this->assertNotNull($m);
        $this->assertEquals('Bielefeld', $m->fromServer());
        $this->assertEquals(2, $m->resultSize());
        $books = $m->bookList();
        $this->assertEquals('http://ubook.asta-bielefeld.de/book.php?id=2169', $books[0]->getUrl());
        $this->assertEquals('Merkl und Waack', $books[1]->getAuthor());
        $servers = $m->getNewServers();
        $this->assertEquals(1, sizeof($servers));
        $server = $servers[0];
        $this->assertEquals('http://www.example.org/', $server->getUrl());
    }

    function testBadMessageFromXml() {
        $xmlString = file_get_contents("test/uBookAnswer.xml");
        $xmlString = str_replace('Bioinformatik', '<b>Bioinformatik</b>', $xmlString);
        try {
            $m = Message::createFromXml($xmlString);
            $this->fail('Bad Message was parsed');
        } catch (Exception $ex) {
            // everything fine
        }
    }

    function testToXml() {
        $xmlString = file_get_contents("test/uBookAnswer.xml");
        $m1 = Message::createFromXml($xmlString);
        $m2 = Message::createFromXml($m1->toXmlString());
        $this->assertEquals($m1, $m2);
    }

}
?>