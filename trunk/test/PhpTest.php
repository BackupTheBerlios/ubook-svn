<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';

class PhpTest extends PHPUnit_Framework_TestCase {

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