<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'isbn/Isbn.php';

class IsbnTest extends PHPUnit_Framework_TestCase {

    function testInvalidChar() {
        $string = '1%2034567890';
        try {
            $isbn = new Isbn($number);
            $this->fail('An ISBN must not contain an % char.');
        } catch (Exception $ex) {
            $this->assertNotNull($ex->getMessage());
        }
    }

    function testToString() {
        $number = 1234567890;
        $string = '1234567890';
        $isbn = new Isbn($number);
        $result = $isbn->toString();
        $this->assertEquals($string, $result);
        $this->assertTrue($result === $string);
        $this->assertFalse($number === $string);
    }

    function testStringFromPost() {
        $string = '1234567890';
        $_POST['isbn'] = $string;
        $this->assertEquals($string, $_POST['isbn']);
        $result = Isbn::stringFromPost();
        $this->assertEquals($string, $result);
    }

    function testContainsValidChars() {
        $this->assertTrue(Isbn::containsValidChars('1234567890'));
        $this->assertTrue(Isbn::containsValidChars('978-3-86680-192-9'));
        $this->assertTrue(Isbn::containsValidChars('978 3 86680 192 9'));
        $this->assertFalse(Isbn::containsValidChars('12345678'));
        $this->assertFalse(Isbn::containsValidChars('978 3 86680 192 99'));
    }

}

?>