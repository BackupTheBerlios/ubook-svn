<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once '../net/Message.php';

class MessageTest extends PHPUnit_Framework_TestCase {
	
	function testBadChar() {
		$this->assertFalse(Message::hasBadChar('hallo'));
		$this->assertTrue(Message::hasBadChar('<hallo>'));
	}
	
	function testMessageFromString() {
		$string = "<!-- section -->\nTest\n<!-- section -->\n0\n<!-- section -->\n\n<!-- section -->\n\n";
		$message = Message::parseString($string);
		$this->assertEquals($string, $message->toString());
	}
	
	function testMalformedString() {
		$message = Message::parseString('');
		$this->assertNull($message);
		$string = "<!-- section -->\n<script>I am evil!</script>\n<!-- section -->\n0\n<!-- section -->\n\n<!-- section -->\n\n";
		$message = Message::parseString($string);
		$this->assertNull($message);
	}
	
}
?>