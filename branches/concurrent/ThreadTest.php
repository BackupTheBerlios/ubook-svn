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
require_once 'Thread.php';

class ThreadTest extends PHPUnit_Framework_TestCase {

    public static $result;

    protected function setUp() {
        self::$result = array();
    }

    protected function tearDown() {
        Thread::joinAll();
    }

    function testJoinAll() {
        $thread1 = new TestThread(array('Hallo', 'Welt'));
        $thread2 = new TestThread(array('Eins', 'Zwei', 'Drei'));
        Thread::joinAll();
        $this->assertTrue(in_array('Hallo', self::$result));
        $this->assertTrue(in_array('Welt', self::$result));
        $this->assertTrue(in_array('Eins', self::$result));
        $this->assertTrue(in_array('Zwei', self::$result));
        $this->assertTrue(in_array('Drei', self::$result));
    }

    function testJoin() {
        $thread1 = new TestThread(array('Hallo'));
        $thread2 = new TestThread(array('Eins', 'Zwei', 'Drei'));
        $thread1->join();
        $this->assertTrue(in_array('Hallo', self::$result));
        $this->assertTrue(in_array('Eins', self::$result));
        $this->assertFalse(in_array('Zwei', self::$result));
        $this->assertFalse(in_array('Drei', self::$result));
    }

}

class TestThread extends Thread {

    private $parts;

    public function __construct($resultParts) {
        parent::__construct();
        $this->parts = $resultParts;
    }

    public function step() {
        $p = array_shift($this->parts);
        if (sizeof($this->parts <= 2)) {
            ThreadTest::$result[] = $p;
        } else {
            new TestThread(array($p));
        }
    }

    public function isFinished() {
        if (sizeof($this->parts) > 0)
            return false;
        else
            return true;
    }

}
?>
