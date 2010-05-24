<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'tools/Parser.php';

/**
 * Test class for Parser.
 */
class ParserTest extends PHPUnit_Framework_TestCase {

    public function testText2html() {
    	$text = '';
    	$expected = '';
    	$result = Parser::text2html($text);
    	$this->assertEquals($expected, $result);
    	$text = 'Hello!';
    	$expected = 'Hello!';
    	$result = Parser::text2html($text);
    	$this->assertEquals($expected, $result);
    	$text = '5"<br />';
    	$expected = '5&quot;&lt;br /&gt;';
    	$result = Parser::text2html($text);
    	$this->assertEquals($expected, $result);
    	$text = '5\'<br />';
    	$expected = '5&#039;&lt;br /&gt;';
    	$result = Parser::text2html($text);
    	$this->assertEquals($expected, $result);
    	$text = "1\n2";
    	$expected = "1\n2";
    	$result = Parser::text2html($text);
    	$this->assertEquals($expected, $result);
    }

}
?>