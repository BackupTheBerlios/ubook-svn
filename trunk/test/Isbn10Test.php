<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2009 Maikel Linke
 * TODO: Depricated.
 */

require_once 'PHPUnit/Framework.php';
require_once 'isbn/Isbn10.php';

class Isbn10Test extends PHPUnit_Framework_TestCase {

    protected $validIsbn;
    protected $invalidIsbn;

	protected function setUp() {
        $this->validIsbn = new Isbn10('3-257-2264-0');
		$this->invalidIsbn = new Isbn10('3-257-2264-1');
	}

    function testIsIsbn10() {
        $this->assertTrue($this->validIsbn->isIsbn10());
    }

    function testIsIsbn13() {
        $this->assertFalse($this->validIsbn->isIsbn13());
    }

}

?>