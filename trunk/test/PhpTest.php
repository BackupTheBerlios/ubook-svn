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

class Root {
    public static function makeNew() {
        return new self();
    }
}

class Child extends Root {

}

class PhpTest extends PHPUnit_Framework_TestCase {

    function testSelf() {
        $this->assertTrue(Root::makeNew() instanceof Root);
        $this->assertTrue(Child::makeNew() instanceof Root);
        $this->assertFalse(Child::makeNew() instanceof Child);
        $child = new Child();
        $this->assertFalse($child->makeNew() instanceof Child);
    }

    /**
     * Demonstrates array walking behaviour, important for
     * net/ExternalBookListReader.
     */
    function testArrayWalking() {
        $a = array('a', 'b', 'c');
        $this->assertEquals('a', current($a));
        $this->assertEquals('b', next($a));
        $this->assertEquals('b', current($a));
        $a[] = 'd';
        $this->assertEquals('c', next($a));
        $this->assertEquals('d', next($a));
        $b = array('a', 'b');
        $this->assertEquals('b', next($b));
        reset($a);
        list($i, $v) = each($a);
        $this->assertEquals(0, $i);
        $this->assertEquals('a', $v);
        $this->assertEquals('b', current($a));
        unset($a[1]);
        $this->assertEquals('c', current($a));
        reset($a);
        unset($a[0]);
        $this->assertEquals('c', current($a));
    }

    /**
     * This behaviour is important for net/HttpConnection.
     */
    function testFeof() {
        $fp = fopen('test/emptyFile', 'r');
        if (!$fp) {
            $this->markTestSkipped('Could not open file.');
        }
        $this->assertFalse(feof($fp));
        $emptyString = fread($fp, 1);
        $this->assertEquals('', $emptyString);
        $this->assertTrue(feof($fp));
        fclose($fp);
    }

    /**
     * @see add.php
     */
    function testNumberCasting() {
        $expected = 5.07;
        $germanPriceString = '5,07';
        $float = (float) $germanPriceString;
        $this->assertNotEquals($expected, $float);
        $priceString = str_replace(',', '.', $germanPriceString);
        $float = (float) $priceString;
        $this->assertEquals($expected, $float);
    }

    /**
     * @see net/ExternalBookListReader.php
     */
    function testReferences() {
        function &identity(&$x) {
            return $x;
        }
        $a = array();
        $b = &identity($a);
        $a[] = null;
        $this->assertEquals(1, sizeof($b));
    }

    function testImplode() {
        $a = array(
            'a' => 'A',
            'b' => 'B'
        );
        $expected = 'A:B';
        $result = implode(':', $a);
        $this->assertEquals($expected, $result);
    }

}

?>