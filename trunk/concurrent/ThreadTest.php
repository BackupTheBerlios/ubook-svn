<?php

/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';
require_once 'Thread.php';

class ThreadTest extends PHPUnit_Framework_TestCase {

    public static $result = array();

    function testRunAndWait() {
        $thread1 = new TestThread(array('Hallo', 'Welt'));
        $thread2 = new TestThread(array('Eins', 'Zwei', 'Drei'));
        Thread::runAndWait();
        $this->assertTrue(in_array('Hallo', self::$result));
        $this->assertTrue(in_array('Welt', self::$result));
        $this->assertTrue(in_array('Eins', self::$result));
        $this->assertTrue(in_array('Drei', self::$result));
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
