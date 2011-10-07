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