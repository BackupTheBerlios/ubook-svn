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
require_once 'DocTest.php';

class DocTestTest extends PHPUnit_Framework_TestCase {

    function testEval() {
        eval('function foo() {}');
        $this->assertTrue(function_exists('foo'));
    }

    function testOutputBuffer() {
        ob_start();
        echo 'Hello.';
        ob_start();
        echo 'Hello Buffer!';
        $innerContent = ob_get_contents();
        ob_end_clean();
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('Hello Buffer!', $innerContent);
        $this->assertEquals('Hello.', $contents);
    }

    function testDocTest() {
        $test = new DocTest('DocTest.php');
        $this->assertEquals($test->getExpected(), $test->getResult());
        $this->assertFalse($test->failed());
    }

    function testGoodExample() {
        $file = 'example_good.php';
        $this->needFile($file);
        $test = new DocTest($file);
        $this->assertFalse($test->failed());
        $expected = 3;
        $result = $test->numOfPassed();
        $this->assertEquals($expected, $result);
    }

    function testBadExample() {
        $this->needFile('example_bad.php');
        $test = new DocTest('example_bad.php');
        $this->assertTrue($test->failed());
        $this->assertEquals(10, $test->getLineNumber());
        $this->assertEquals('42', $test->getExpected());
        $this->assertEquals('440', $test->getResult());
    }

    function needFile($file) {
        if (!is_file($file))  {
            $this->markTestSkipped($file . ' does not exist.');
        }
    }

}

?>
