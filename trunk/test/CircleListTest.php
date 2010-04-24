<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
 */

require_once 'tools/CircleList.php';

class CircleListTest extends PHPUnit_Framework_TestCase {

    function testEmpty() {
        $list = new CircleList();
        $result = $list->getNext();
        $this->assertNull($result);
    }

    function testAdd() {
        $list = new CircleList();
        $elem = "Estoy listo!";
        $list->add($elem);
        $result = $list->getNext();
        $this->assertEquals($result, $elem);
    }

    function testDelete() {
        $list = new CircleList();
        $elem = "Estoy aqui!";
        $list->add($elem);
        $list->deleteActual();
        $result = $list->getNext();
        $this->assertNull($result);
    }

    function testMultiple() {
        $list = new CircleList();
        $e1 = "e1";
        $e2 = "e2";
        $e3 = "e3";
        $e4 = "e4";
        $list->add($e1);
        $list->add($e2);
        $list->add($e3);
        $list->add($e4);
        $this->assertEquals($e1, $list->getNext());
        $this->assertEquals($e2, $list->getNext());
        $this->assertEquals($e3, $list->getNext());
        $list->deleteActual();
        $list->deleteActual();
        $this->assertEquals($e4, $list->getNext());
        $this->assertEquals($e1, $list->getNext());
    }

}

?>